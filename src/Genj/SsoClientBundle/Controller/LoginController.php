<?php

namespace Genj\SsoClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $client->pass401 = true;
        $command = $request->get('cmd', false);

        if ($command) {
            $ret = $client->$command();
        }

        var_dump($client->getInfo());


        return $this->render(
            'GenjSsoClientBundle:Login:index.html.twig'
        );
    }
}
