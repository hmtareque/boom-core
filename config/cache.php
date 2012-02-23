<?php

return array(
	'default'	=> array(						// Default group
		'driver'	=> 'memcache',		// using Memcache driver
		'servers'	=> array(				// Available server definitions
			array(
				'host'		=> 'localhost',
				'port'		=> 11211,
				'persistent'=> FALSE
				)
			),
		'compression'	=> FALSE,				// Use compression?
	),
)

?>