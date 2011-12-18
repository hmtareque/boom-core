<?php defined('SYSPATH') or die('No direct script access.');

/**
* Slot Class.
* @package Slots
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

abstract class Slot extends ORM {
	
	/**
	* Factory method for generating slots.
	*
	* @param string $type The type of slot.
	* @param Model_Page $page The page which the slot belongs to.
	* @param string $slotname The slotname which identifies the slot
	* @param boolean $editable Whether editing of the slot should be enabled.
	* @return Slot
	*/ 
	public static function factory( $type, Model_Page $page, $slotname, $editable = null )
	{
		$slot = parent::factory( $type )->where( 'page_id', '=', $page->id )->and_where( 'slotname', '=', $slotname )->find();
		
		/*
		This is how it should work - but disabled during development (haven't created the cms/site page classes yet).
		
		if ($editable !== false && $page instanceof Page_Cms )
		{
			return new Slot_Cms( $slot );
		}
		else
		{
			return new Slot_Site( $slot );
		}
		*/
		
		return new Slot_Cms( $slot );
	}
	
	public function __toString()
	{
		return $this->show();
	}
	
	abstract function show();
}

?>
