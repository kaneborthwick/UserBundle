<?php

use Towersystems\User\Model\User;

return [
	'doctrine' => [
		'driver' => [
			'orm_default' => [
				'drivers' => [
					User::class => 'user_bundle_config',
				],
			],
			'user_bundle_config' => [
				'class' => \Doctrine\ORM\Mapping\Driver\XmlDriver::class,
				'paths' => __DIR__ . '/../doctrine',
			],
		],
	],
];