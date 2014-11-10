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
         * @var Broker $broker
         */
        $broker = $this->get('genj_sso_client.broker');

        if ($request->getMethod() == 'POST') {
            $loginResponse = $broker->login();

            if (isset($loginResponse->error)) {
                $request->getSession()->getFlashBag()->add('error', $loginResponse->error);
            }
        }

        $info = $broker->getInfo();

        var_dump($info);

        return $this->render(
            'GenjSsoClientBundle:Login:index.html.twig'
        );
    }

    /**
     * @param Request $request
     */
    public function loginAction(Request $request)
    {
        /**
         * @var Broker $broker
         */
        $broker = $this->get('genj_sso_client.broker');

        $broker->login();
    }

    public function logoutAction(Request $request)
    {
        /**
         * @var Broker $client
         */
        $broker = $this->get('genj_sso_client.broker');

        $broker->logout();

        return new RedirectResponse($request->headers->get('referer'));
    }
}
