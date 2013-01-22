<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Group controller
 * Pages for managing groups.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Groups extends Boom_Controller
{
	/**
	 * Directory where view files for this controller are stored.
	 *
	 * @var string
	 */
	protected $_view_directory = 'boom/groups';

	/**
	 *
	 * @var Model_Group	ORM object for the gorup we're editing.
	 */
	public $group;

	/**
	 * Checks that the logged user has permission to access the people manager.
	 * Also instantiates a Model_Group instance and assignes it to [Boom_Controller_Cms_Groups::$group]
	 *
	 * @uses Boom_Controller::before()
	 * @uses Boom_Controller::authorization()
	 * @uses Boom_Controller_Cms_Groups::$group
	 */
	public function before()
	{
		parent::before();

		// Check that we're allowed to be here.
		$this->authorization('manage_people');

		// Instantiate a group object.
		$this->group = new Model_Group($this->request->param('id'));
	}

	/**
	 * Create a new group.
	 *
	 * A group name should be given in the POST data.
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Group::set()
	 * @uses Model_Group::create()
	 */
	public function action_add()
	{
		if ($this->request->method() === Request::POST)
		{
			// POST request
			// Process the data to create the group.
			$this->group
				->set('name', $this->request->post('name'))
				->create();

			// Log the action.
			$this->log("Created group: ".$this->group->name);

			// Put the group ID in the response body.
			$this->response->body($this->group->id);
		}
		else
		{
			// Display the 'edit group' view with an empty group model.
			$this->template = View::factory("$this->_view_directory/add", array(
				'group' => new Model_Group,
			));
		}
	}

	/**
	 * Adds a role to the current group.
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Group::add_role()
	 */
	public function action_add_role()
	{
		// Log the action.
		$this->log("Edited the roles of group ".$this->group->name);

		// Call [Model_Group::add_role()] with the role ID from the POST data.
		$this->group->add_role($this->request->post('role_id'), $this->request->post('allowed'));
	}

	/**
	 * Delete a single group.
	 *
	 * The group to be deleted is specified in the URL
	 *
	 *	/cms/groups/delete/1
	 *
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Group::delete()
	 */
	public function action_delete()
	{
		// Log the action.
		$this->log("Deleted group ".$this->group->name);

		// Delete the group.
		$this->group->delete();
	}

	/**
	 * Display a page to edit a group's details, e.g. name and permissions.
	 *
	 */
	public function action_edit()
	{
		// Display the 'edit group' view with an empty group model.
		$this->template = View::factory("$this->_view_directory/edit", array(
			'group'		=>	$this->group,
			'general_roles'	=>	ORM::factory('Role')
				->where('name', 'not like', 'p_%')
				->order_by('description', 'asc')
				->find_all(),
			'page_roles'	=>	ORM::factory('Role')
				->where('name', 'like', 'p_%')
				->order_by('description', 'asc')
				->find_all(),
		));
	}

	public function action_list_roles()
	{
		
	}

	/**
	 * Removes a role from the current group.
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Group::remove_role()
	 */
	public function action_remove_role()
	{
		// Log the action.
		$this->log("Edited the roles of group ".$this->group->name);

		// Remove the role.
		$this->group->remove_role($this->request->post('role_id'));
	}

	/**
	 * Save group details.
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Group::set()
	 * @ueses Model_Group::update();
	 */
	public function action_save()
	{
		// Log the action.
		$this->log("Edited group " . $this->group->name . " (ID: " . $this->group->id . ")");

		// Update the group.
		$this->group
			->set('name', $this->request->post('name'))
			->update();
	}
}