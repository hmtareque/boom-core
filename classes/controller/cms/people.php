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
		$person->firstname = Arr::get( 'post', 'firstname', null );
		$person->lastname = Arr::get( 'post', 'firstname', null );		
		$person->emailaddress = Arr::get( 'post', 'firstname', null );
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
			$person->add_group( $group_id );
			
			$this->request->redirect( '/cms/people/view/' . $person->pk() );
		}
		else
		{
			$v = View::factory( 'ui/subtpl_peoplemanager_create_person' );
			$v->groups = ORM::factory( 'group' )->find_all();
			echo $v;
			exit;
		}	
	}
	
	public function action_add_group()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		
		if ($person->loaded())
		{
			if ($this->request->method() == 'POST')
			{
				$groups = Arr::get( $_POST, 'group_id' );
				
				if (is_array( $groups ))
				{
					foreach( $groups as $group_id )
					{
						$person->add_group( $group_id );
					}	
				}
				else
				{
					$person->add_group( $groups );
				}				
				$this->request->redirect( '/cms/people/view/' . $person->pk() );
			}
			else
			{
				// Find the groups that this person isn't already a member of.
				$groups = ORM::factory( 'group' )
							->join( 'person_group', 'right outer' )
							->on( 'person_group.group_id', '!=', 'group.id' )
							->where( 'person_group.person_id', '=', $person->pk() )
							->find_all();
							
				$this->template->subtpl_main = View::factory( 'ui/subtpl_person_addgroup' );
				$this->template->subtpl_main->person = $person;
				$this->template->subtpl_main->groups = $groups;
			}
		}
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
		
		$this->template->subtpl_main = View::factory( 'ui/subtpl_tag_manager_person_detailview' );
		$this->template->subtpl_main->person = $person;		
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
	
	public function action_delete_group()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		
		if ($person->loaded())
		{
			$groups = Arr::get( $_POST, 'group_id' );
		
			if (is_array( $groups ))
			{
				ORM::factory( 'person_group' )->where( 'group_id', '=', $groups )->where( 'person_id', '=', $person->pk() )->delete();
			}
			else
			{
				ORM::factory( 'person_group' )->where( 'group_id', '=', $groups )->where( 'person_id', '=', $person->pk() )->delete();
			}
		}
		
		$this->request->redirect( '/cms/people/view/' . $person->pk() );
	}
	
	/**
	* People manager default page.
	* Displays the people manager template with an array of people.
	*
	* @return void
	*/
	public function action_index()
	{	
		$this->template->subtpl_main = View::factory( 'ui/tpl_people_manager' );
		$this->template->subtpl_main->people = ORM::factory( 'person' )->find_all();
	}
	
}

?>
