<?php

namespace UserBundle\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ResourceBundle\Controller\Controller;
use Webmozart\Assert\Assert;
use Zend\Diactoros\Response\HtmlResponse;

/**
 *
 */
class SecurityController extends Controller {

	/**
	 * Login form action
	 */
	public function loginAction(ServerRequestInterface $request, RequestHandlerInterface $handler)
	: \Psr\Http\Message\ResponseInterface{
		$template = $this->getOption("template");
		Assert::notNull($template, 'Template is not configured.');

		return new HtmlResponse(
			$this->templates->render($template)
		);
	}

	public function checkAction(ServerRequestInterface $request, RequestHandlerInterface $handler)
	: \Psr\Http\Message\ResponseInterface {
		throw new \RuntimeException('You must configure the check path in your [ core ] bundle.');
	}

	public function logoutAction(ServerRequestInterface $request, RequestHandlerInterface $handler)
	: \Psr\Http\Message\ResponseInterface {
		throw new \RuntimeException('You must configure the logout path in your [ core ] bundle.');
	}
}