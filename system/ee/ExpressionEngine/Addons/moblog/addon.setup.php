<?php

return array(
	'author'      => 'ExpressionEngine',
	'author_url'  => 'https://expressionengine.com/',
	'name'        => 'Moblog',
	'description' => 'Submit channel entries to EE via email',
	'version'     => '3.2.0',
	'namespace'   => 'ExpressionEngine\Addons\Moblog',
	'settings_exist' => TRUE,
	'models' => array(
		'Moblog' => 'Model\Moblog'
	)
);

// EOF