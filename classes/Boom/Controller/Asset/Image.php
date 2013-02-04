<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset controller for display images.
 *
 * @package		BoomCMS
 * @category		Assets
 * @category		Controllers
 * @author		Rob Taylor
 * @copyright		Hoop Associates
 *
 */
class Boom_Controller_Asset_Image extends Controller_Asset
{
	/**
	 *
	 * @var int
	 */
	public $crop;

	/**
	 *
	 * @var int
	 */
	public $height;

	/**
	 *
	 * @var int
	 */
	public $quality;

	/**
	 *
	 * @var int
	 */
	public $width;

	public function before()
	{
		parent::before();

		// Set some properties from the request paramaters.
		// This use used for resizing / cropping the image.
		$this->width	= $this->request->param('width');
		$this->height	= $this->request->param('height');
		$this->quality	= ($this->request->param('quality'))? $this->request->param('quality') : 100;
		$this->crop	= (bool) $this->request->param('crop');
	}

	public function action_view()
	{
		//
		$filename = Boom_Asset::$path.$this->asset->id;

		// Are we viewing an old version of the asset?
		if ($timestamp = $this->request->query('timestamp'))
		{
			// Update the filename to include the version ID.
			$filename .= "_".$timestamp."_";
		}

		// Are we going to be resizing the image?
		if ($this->width OR $this->height OR $this->quality < 100)
		{
			// Add the image dimensions to the filename.
			// Cast the width and height to int so that if only one is set 0 will be used for the other rather than null
			$filename .= "_".(int) $this->width."_".(int) $this->height ."_".$this->quality.".cache" ;
		}

		// Does the file exist?
		// It won't if we're resizing the image, or viewing an older version, and the cache file hasn't been generated.
		if ( ! file_exists($filename))
		{
			// No - we'll have to generate a cache file.
			$image = ($timestamp)? Image::factory(Boom_Asset::$path.$this->asset->id . ".$timestamp.bak") : Image::factory(Boom_Asset::$path.$this->asset->id);

			// Set the dimensions and quality of the image.
			$this->height = ($this->height == 0)? $image->height : $this->height;
			$this->width = ($this->width == 0)? $image->width : $this->width;

			if ($this->width OR $this->height)
			{
				if ($this->crop)
				{
					$image->resize($this->width, $this->height, Image::INVERSE);
					$image->crop($this->width, $this->height);
				}
				else
				{
					$image->resize($this->width, $this->height);
				}
			}

			// Save the file.
			// $image->save() doesn't always work with Imagemagick but this does the job.
			file_put_contents($filename, $image->render(NULL, $this->quality));
		}
		else
		{
			// Load the cached file.
			$image =  Image::factory($filename);
		}

		$this->response
			->headers('Content-type', $image->mime)
			// Use file_get_contents() because it's quicker than [Image::render()]
			->body(file_get_contents($filename));
	}

	/**
	 * Show a thumbnail of the asset.
	 * For images a thumbnail is just showing an image with different dimensions.
	 */
	public function action_thumb()
	{
		$this->action_view();
	}
}