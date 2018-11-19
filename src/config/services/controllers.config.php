<?php

use ResourceBundle\Controller\Factory\AbstractControllerFactory;
use UserBundle\Controller\SecurityController;

return [
	AbstractControllerFactory::class => [
		SecurityController::class => [],
	],

	'dependencies' => [
		'aliases' => [
			'tower.controller.security' => SecurityController::class,
		],
	],
];
