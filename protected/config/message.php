<?php

return array(
	'sourcePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'messagePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../messages',
	'languages' => array('en','ru'),
	'fileTypes' => array('php', 'tpl'),
	'overwrite' => true,
	'exclude' => array(
		'vendors',
		'utils',
		'extensions'
	),
);
