<?php

namespace UserBundle\Authentication;

use Psr\Http\Message\ResponseInterface;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepositoryInterface;
use Traversable;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Session\SessionMiddleware;
use \Firebase\JWT\JWT;
use Zend\Expressive\Session\SessionInterface;

/**
 *
 *
 */
class JWTSessionAccess implements AuthenticationInterface {

	/**
	 * @var UserRepositoryInterface
	 */
	private $repository;

    /**
     * [__construct description]
     * 
     * @param UserRepositoryInterface $repository      [description]
     * @param array                   $config          [description]
     * @param callable                $responseFactory [description]
     * @param callable                $userFactory     [description]
     */
	function __construct(
		UserRepositoryInterface $repository,
		array $config,
		callable $responseFactory,
		callable $userFactory
	) {
		$this->repository = $repository;
		$this->config = $config;

		// Ensures type safety of the composed factory
		$this->responseFactory = function () use ($responseFactory): ResponseInterface {
			return $responseFactory();
		};

		// Ensures type safety of the composed factory
		$this->userFactory = function (
			string $identity,
			array $roles = [],
			array $details = []
		) use ($userFactory): UserInterface {
			return $userFactory($identity, $roles, $details);
		};
	}

    /**
     * {@inheritDoc}
     */
    public function unauthorizedResponse(ServerRequestInterface $request) : ResponseInterface
    {
        return ($this->responseFactory)()
            ->withHeader(
                'Location',
                $this->config['redirect']
            )
            ->withStatus(302);
    }


    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequestInterface $request) : ?UserInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        if (! $session) {
            throw new \Exception();
        }

        if ($session->has(UserInterface::class)) {
            return $this->createUserFromSession($session);
        }   

        $key = $this->config['shared_secret'] ?? null;

        if (null === $key) {
            throw new \Exception("shared_secret not found.", 1);
        }

        $params = $request->getQueryParams();
        $jwt = $params['jwt'] ?? null;

        if (null === $jwt) {
            return null;
        }

        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
        } catch (\Exception $e) {
            return null;
        }

        $user = $this->repository->authenticate($decoded->username);

        if (null !== $user) {
            $session->set(UserInterface::class, [
                'username' => $user->getIdentity(),
                'roles'    => iterator_to_array($this->getUserRoles($user)),
                'details'  => $user->getDetails(),
            ]);
            $session->regenerate();
        }

        return $user;
    }

      /**
     * Create a UserInterface instance from the session data.
     *
     * zend-expressive-session does not serialize PHP objects directly. As such,
     * we need to create a UserInterface instance based on the data stored in
     * the session instead.
     */
    private function createUserFromSession(SessionInterface $session) : ?UserInterface
    {
        $userInfo = $session->get(UserInterface::class);
        if (! is_array($userInfo) || ! isset($userInfo['username'])) {
            return null;
        }
        $roles   = $userInfo['roles'] ?? [];
        $details = $userInfo['details'] ?? [];

        return ($this->userFactory)($userInfo['username'], (array) $roles, (array) $details);
    }

    /**
     * Convert the iterable user roles to a Traversable.
     */
    private function getUserRoles(UserInterface $user) : Traversable
    {
        return yield from $user->getRoles();
    }


}
