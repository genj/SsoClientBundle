<?php

namespace Genj\SsoClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Genj\SsoClientBundle\Sso\Broker;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class ArticleController
 *
 * @package Genj\ArticleBundle\Controller
 */
class LoginController extends Controller
{
    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /**
         * @var Broker $client
         */
        $client = $this->get('genj_sso_client.broker');

        if (!$client->isAttached()) {
            return $client->autoAttach($request->getUri());
        }

        $command = $request->get('cmd', false);

        if ($command) {
            $ret = $client->$command();
        }

        $info = $client->getInfo();

        var_dump($info);


        return $this->render(
            'GenjSsoClientBundle:Login:index.html.twig'
        );
    }

    public function loginAction(Request $request)
    {

    }

    public function logoutAction(Request $request)
    {
        /**
         * @var Broker $broker
         */
        $broker = $this->get('genj_sso_client.broker');

        $broker->logout();

        return new RedirectResponse($request->headers->get('referer'));
    }

    public function infoAction(Request $request)
    {

    }
}
