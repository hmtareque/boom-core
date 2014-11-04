<?php

namespace Boom;

use \HTML as HTML;
use \Kohana as Kohana;
use \Model_Page_Version as Model_Page_Version;
use \ORM as ORM;
use \Request as Request;
use \Profiler as Profiler;
use \View as View;

use \Boom\Auth\Auth as Auth;
use \Boom\Page\Page as Page;
use \Boom\Editor\Editor as Editor;

abstract class Chunk
{
    protected $attributePrefix = 'data-boom-';

    /**
	 *
	 * @var ORM
	 */
    protected $_chunk;

    /**
	 *
	 * @var string
	 */
    protected $_default_template = null;

    /**
	 *
	 * @var boolean
	 */
    protected $_editable = true;

    /**
	 *
	 * @var Model_Page
	 */
    protected $_page;

    /**
	 * An array of parameters which will be passed to the chunk template
	 *
	 * @var array
	 */
    protected $_params = [];

    /**
	 * The slotname used to find the chunk.
	 * This has to be stored seperately to $this->_chunk so that for default chunks where $this->_chunk isn't loaded we know the slotname where the chunk belongs.
	 *
	 * @var string
	 */
    protected $_slotname;

    /**
	 *
	 * @var string
	 */
    protected $_template;

    /**
	 *
	 * @var string
	 */
    protected $_type;

    protected $viewDirectory = 'site/slots/';

    /**
	 * Array of available chunk types.
	 *
	 * @var array
	 */
    public static $types = ['asset', 'text', 'feature', 'linkset', 'slideshow', 'timestamp', 'tag'];

    public function __construct(Page $page, $chunk, $slotname)
    {
        $this->_page = $page;
        $this->_chunk = $chunk;
        $this->_slotname = $slotname;
    }

    /**
	 *
	 * @return string
	 */
    public function __toString()
    {
        return (string) $this->execute();
    }

    /**
	 * Displays the chunk when chunk data has been set.
	 *
	 * @return View
	 */
    abstract protected function _show();

    /**
	 * Displays default chunk HTML
	 *
	 * @return View
	 */
    abstract protected function _show_default();

    /**
	 * Attributes to be added to the chunk HTML. Can be overriden to pass additional info to javascript editor.
	 *
	 * @return array()
	 */
    public function attributes()
    {
        return [];
    }

    /**
	 * This adds the necessary classes to chunk HTML for them to be picked up by the JS editor.
	 * i.e. it makes chunks editable.
	 *
	 * @param string $html HTML to add classes to.
	 * @return string
	 */
    public function add_attributes($html)
    {
        $html = trim( (string) $html);

        $attributes = [
            $this->attributePrefix.'chunk' => $this->_type,
            $this->attributePrefix.'slot-name' => $this->_slotname,
            $this->attributePrefix.'slot-template' => $this->_template,
            $this->attributePrefix.'page' => $this->_page->getId(),
            $this->attributePrefix.'chunk-id' => $this->_chunk->id,
        ];
        $attributes = array_merge($attributes, $this->attributes());
        $attributes_string = HTML::attributes($attributes);

        return preg_replace("|<(.*?)>|", "<$1 $attributes_string>", $html, 1);
    }

    public function defaults(array $values)
    {
        $this->_chunk->values($values);

        return $this;
    }

    /**
	 * Sets wether the chunk should be editable.
	 *
	 * @param bool $value
	 */
    public function editable($value)
    {
        // Set the value of $_editable.
        $this->_editable = $value;

        return $this;
    }

    /**
	 * Attempts to get the chunk data from the cache, otherwise calls _execute to generate the cache.
	 */
    public function execute()
    {
        // If profiling is enabled then record how long it takes to generate this chunk.
        if (Kohana::$profiling === true) {
            $benchmark = Profiler::start("Chunks", $this->_chunk->slotname);
        }

        // Generate the HTML.
        // Don't allow an error displaying the chunk to bring down the whole page.
        try {
            /** Should the chunk be editable?
			 * This can be changed to calling editable(), for instance if we want to make a chunk read only.
			 *
			 * @todo Multiple chunks will be inserted on a single page - need to remove duplicate calles to Auth::instance()->isLoggedIn()
			 */
            $this->_editable = ($this->_editable === true && Editor::instance()->isEnabled() && ($this->_page->wasCreatedBy(Auth::instance()->getPerson()) || Auth::instance()->loggedIn("edit_page_content", $this->_page)));

            // Get the chunk HTML.
            $html = $this->html();

            if ($this->_editable === true) {
                $html = $this->add_attributes($html);
            }
        } catch (Exception $e) {
            // Log the error.
            Kohana_Exception::log($e);

            return;
        }

        if (isset($benchmark)) {
            Profiler::stop($benchmark);
        }

        return $html;
    }

    /**
	 * Chunk object factory.
	 * Returns a chunk object of the required type.
	 *
	 * @param	string	$type		Chunk type, e.g. text, feature, etc.
	 * @param	string	$slotname		The name of the slot to retrieve a chunk from.
	 * @param	mixed	$page		The page the chunk belongs to. If not given then the page from the current request will be used.
	 * @param	boolean	$inherit		Whether the chunk should be inherited down the page tree.
	 * @return 	Chunk
	 */
    public static function factory($type, $slotname, $page = null)
    {
        // Set the class name.
        $class = "\Boom\Chunk\\" . ucfirst($type);

        // Set the page that the chunk belongs to.
        // This is used for permissions check, and quite importantly, for finding the chunk.
        if ($page === null) {
            // No page was given so use the page from the current request.
            $page = Request::current()->param('page');
        } elseif ($page === 0) {
            // 0 was given as the page - this signifies a 'global' chunk not assigned to any page.
            $page = new Model_Page();
        }

        // Load the chunk
        $chunk = Chunk::find($type, $slotname, $page->getCurrentVersion());

        return new $class($page, $chunk, $slotname);
    }

    public static function find($type, $slotname, Model_Page_Version $version)
    {
        if (is_array($slotname)) {
            return Chunk::find_multiple($type, $slotname, $version);
        } else {
            return Chunk::find_single($type, $slotname, $version);
        }
    }

    public static function find_single($type, $slotname, Model_Page_Version $version)
    {
        $model = (strpos($type, "Chunk_") === 0) ? ucfirst($type) : "Chunk_" . ucfirst($type);

        $query = ORM::factory($model)
            ->with('target')
            ->where('page_vid', '=', $version->id);

        if (is_array($slotname)) {
            return $query
                ->where('slotname', 'in', $slotname)
                ->find_all();
        } else {
            return $query
                ->where('slotname', '=', $slotname)
                ->find();
        }
    }

    public static function find_multiple($type, $slotname, Model_Page_Version $version)
    {
        // Get the name of the model that we're looking.
        // e.g. if type is text we want a chunk_text model
        $model = (strpos($type, "Chunk_") === 0) ? ucfirst($type) : "Chunk_" . ucfirst($type);

        return ORM::factory($model)
            ->with('target')
            ->where('slotname', 'in', $slotname)
            ->where('page_vid', '=', $version->id)
            ->find_all()
            ->as_array();
    }

    /**
	 * Returns whether the chunk has any content.
	 *
	 * @return	bool
	 */
    abstract public function hasContent();

    /**
	 * Generate the HTML to display the chunk
	 *
	 * @return 	string
	 */
    public function html()
    {
        if ($this->_template === null) {
            $this->_template = $this->_default_template;
        }

        if ($this->hasContent()) {
            // Display the chunk.
            $return = $this->_show();
        } elseif ($this->_editable === true) {
            // Show the defult chunk.
            $return = $this->_show_default();
        } else {
            // Chunk has no content and the user isn't allowed to add any.
            // Don't display anything.
            return "";
        }

        // If the return data is a View then assign any parameters to it.
        if ($return instanceof View && ! empty($this->_params)) {
            foreach ($this->_params as $key => $value) {
                $return->$key = $value;
            }
        }

        return (string) $return;
    }

    /**
	 * Getter / setter method for template parameters.
	 */
    public function params($params = null)
    {
        if ($params === null) {
            return $this->_params;
        } else {
            $this->_params = $params;

            return $this;
        }
    }

    /**
	 * Set the template to display the chunk
	 *
	 * @param	string	$template	The name of a view file.
	 * @return	Chunk
	 */
    public function template($template = null)
    {
        // Set the template filename.
        $this->_template = $template;

        return $this;
    }
}
