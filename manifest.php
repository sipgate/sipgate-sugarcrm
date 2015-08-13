<?php
$manifest = array(
	'acceptable_sugar_flavors' => array('CE'),
	'acceptable_sugar_versions' => array(
		'regex_matches' => array(
			'6\\.5\\.[0-9]+$'
		)
	),
	'author' => 'sipgate GmbH',
	'description' => 'Connect SugarCRM to your sipgate account via sipgate.io',
	'icon' => '',
	'is_uninstallable' => true,
	'name' => 'sipgate Call Info plugin',
	'published_date' => '2015-08-13 2015 10:00:00',
	'type' => 'module',
	'version' => '1.0.0',
);

$installdefs = array(
	'id' => 'sipgateio',
	'copy' => array(
		0 => array(
			'from' => '<basepath>/Files/custom/modules/sipgateio/footer.php',
			'to' => 'custom/modules/sipgateio/footer.php',
		),
		1 => array(
			'from' => '<basepath>/Files/custom/modules/sipgateio/sipgateio.php',
			'to' => 'custom/modules/sipgateio/sipgateio.php',
		),
		2 => array(
			'from' => '<basepath>/Files/custom/modules/sipgateio/sipgateio.js',
			'to' => 'custom/modules/sipgateio/sipgateio.js',
		),
		3 => array(
			'from' => '<basepath>/Files/custom/modules/sipgateio/sipgateio.css',
			'to' => 'custom/modules/sipgateio/sipgateio.css'
		)
	),
	'logic_hooks' => array(
		array(
			'module' => '',
			'hook' => 'after_ui_frame',
			'order' => 99,
			'description' => 'Logic Hook - do something with sipgate.io',
			'file' => 'custom/modules/sipgateio/footer.php',
			'class' => 'SipgateIO',
			'function' => 'after_ui_footer_method',
		),
	),
);

?>
