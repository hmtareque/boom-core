<?php defined('SYSPATH') or die('No direct script access.');

/**
* People controller
* Pages for managing people.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller_Cms_People extends Controller_Cms
{	
	public function before()
	{		
		parent::before();
		
		if (!$this->person->can( 'manage people' ))
			Request::factory( 'error/403' )->execute();
		
		$this->template->title = 'People Manager';
		$subtpl_topbar = View::factory( 'ui/subtpl_people_topbar' );
		
		View::bind_global( 'subtpl_topbar', $subtpl_topbar );
	}
	
	public function action_save()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		
		$v = ORM::factory( 'version_person' );
		$v->firstname = Arr::get( 'post', 'firstname', null );
		$v->lastname = Arr::get( 'post', 'firstname', null );		
		$v->emailaddress = Arr::get( 'post', 'firstname', null );
		
		$person = $v;
		$person->save();				
	}
	
	/**
	* Add person controller.
	*/
	public function action_add()
	{
		if ($this->request->method() == 'POST')
		{
			// Create the person
			$person = ORM::factory( 'person' );
			$person->firstname = Arr::get( $_POST, 'firstname' );
			$person->lastname = Arr::get( $_POST, 'surname' );
			$person->emailaddress = Arr::get( $_POST, 'email' );
			$person->password = Arr::get( $_POST, 'password' );
			$person->save();
			
			// Add the person to the initial group.
			$group_id = Arr::get( $_POST, 'group_id' );
			
			// Add the person to the group.
			// This can't be done using Kohana's $person->add because the tables are in different databases.
			$link = ORM::factory( 'person_group' )
					->values( array( 'group_id' => $group_id, 'person_id' => $person->pk() ))
					->create();
			
			$this->request->redirect( '/cms/people/view/' . $person->pk() );
		}
		else
		{
			$v = View::factory( 'ui/subtpl_peoplemanager_create_person' );
			$v->groups = ORM::factory( 'group' )->find_all();
			echo $v;
		}	
		
		exit;
	}
	
	/**
	* People manager view person.
	*
	* @todo What if the person ID isn't valid?
	* @todo check permissions
	* @return void
	*/
	public function action_view()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		
		$v = View::factory( 'ui/subtpl_tag_manager_person_detailview' );
		$v->person = $person;	
		
		echo $v;
		exit;	
	}
	
	public function action_delete()
	{
		$people = Arr::get( $_POST, 'people' );
	
		foreach( $people as $person_id )
		{
			$person_id = str_replace( "person_", "", $person_id );
			$person = ORM::factory( 'person', $person_id );
			$person->delete();
		}	
		
		exit;
	}
	
	/**
	* People manager default page.
	* Displays the people manager template with an array of people.
	*
	* @return void
	*/
	public function action_index()
	{	
		if (isset( $_GET['state'] ))
		{	
			$v = View::factory( 'ui/tpl_people_manager' );
			$v->people = ORM::factory( 'person' )->find_all();
			echo $v;
			exit;
		}
	}
	
}

?>
