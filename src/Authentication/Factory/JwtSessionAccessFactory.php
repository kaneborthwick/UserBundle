<?php

declare (strict_types = 1);

namespace UserBundle\Authentication\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use UserBundle\Authentication\JwtSessionAccess;
use UserBundle\Authentication\JwtUserRepositoryInterface;
use Zend\Expressive\Authentication\Exception;
use Zend\Expressive\Authentication\UserInterface;

class JwtSessionAccessFactory {
	public function __invoke(ContainerInterface $container): JwtSessionAccess {

		if (!$container->has(JwtUserRepositoryInterface::class)) {
			throw new Exception\InvalidConfigException(
				'JwtUserRepositoryInterface service is missing for authentication'
			);
		}

		$config = $container->get('config')['authentication'] ?? [];

		if (!isset($config['shared_secret'])) {
			throw new Exception\InvalidConfigException(
				'The shared_secret configuration is missing for Jwt Authentication'
			);
		}

		if (!$container->has(UserInterface::class)) {
			throw new Exception\InvalidConfigException(
				'UserInterface factory service is missing for authentication'
			);
		}

		return new JwtSessionAccess(
			$container->get(JwtUserRepositoryInterface::class),
			$config,
			$container->get(ResponseInterface::class),
			$container->get(UserInterface::class)
		);
	}
}
