<?php
/**
 * Created by PhpStorm.
 * User: Alexa
 * Date: 15/6/4
 * Time: 下午3:16
 */
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Httpkernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
//use AppBundle\Wechat;
use AppBundle\Weibo;
use Doctrine\ORM\EntityManager;

class OAuthListener
{
	protected $container;
	protected $router;
	protected $em;
	public function __construct($router, \Symfony\Component\DependencyInjection\Container $container,EntityManager $em)
	{
		$this->container = $container;
		$this->router = $router;
		$this->em = $em;
	}
	/*
	public function onKernelController(FilterControllerEvent $event)
	{
		//$controller = $event->getController();
		// 此处controller可以被该换成任何PHP可回调函数
		//$event->setController($controller);
	}
	*/
	public function onKernelRequest(GetResponseEvent $event)
	{
	}
	/*
	public function onKernelResponse(FilterResponseEvent $event)
	{
		$response = $event->getResponse();
		$request = $event->getRequest();
		if ($request->query->get('option') == 3) {
			$response->headers->setCookie(new Cookie("test", 1));
		}
	}
	*/
}