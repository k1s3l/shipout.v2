<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AuthorizationSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
    }

    public function onKernelController(ControllerEvent $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
            'kernel.controller' => 'onKernelController',
        ];
    }
}
