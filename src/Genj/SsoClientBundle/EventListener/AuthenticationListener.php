<?php
namespace Genj\SsoClientBundle\EventListener;

use \Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AuthenticationListener
{
    protected $ssoClientService;

    public function __construct($ssoClientService) {
        $this->ssoClientService = $ssoClientService;
    }

    /**
     * Before every request, check if the browser is attached to the SSO server
     * If not, do so
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->ssoClientService->isAttached()) {
            $event->setResponse( $this->ssoClientService->autoAttach($event->getRequest()->getUri()) );
        }
    }
}
