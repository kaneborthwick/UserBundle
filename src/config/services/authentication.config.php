<?php

use UserBundle\Authentication\Factory\JwtSessionAccessFactory;
use UserBundle\Authentication\JwtSessionAccess;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [

	ConfigAbstractFactory::class => [

	],
	'dependencies' => [
		'aliases' => [],
		'invokables' => [],
		'factories' => [
			JwtSessionAccess::class => JwtSessionAccessFactory::class,
		],
	],

];
