<?php

/**
* Defines a shortcut for /cms/account pages (login, logout, etc.) so that account doesn't have to be used in the URL.
*
*/
Route::set('auth', 'cms/<action>',
	array(
		'action' => '(login|logout)'
	))
	->defaults(array(
		'controller' => 'cms/account'
	));

	
/**
* Defines the route for /cms pages.
*
*/
Route::set('cms', '<directory>/(<controller>(/<action>(/<id>)))',
	array(
		'directory'	=> 'cms'
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));
	
?>