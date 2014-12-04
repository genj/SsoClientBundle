<?php

namespace Genj\SsoClientBundle\Controller;

use Genj\SsoClientBundle\Security\User\SsoUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Genj\SsoClientBundle\Sso\Broker;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

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
            } else {
                $request->getSession()->set('ssoAuthToken', $loginResponse->data->authToken);

                $user = new SsoUser($loginResponse->data->username, $loginResponse->data->authToken, array('ROLE_USER'));

                $token = new PreAuthenticatedToken($user, $loginResponse->data->authToken,
                    'secured_area', array('ROLE_USER'));
                $this->get('security.context')->setToken($token);
            }

            $redirectUri = $request->get('redirectUri');

            if (!$redirectUri) {
                $requestStack  = $this->container->get('request_stack');
                $masterRequest = $requestStack->getMasterRequest();

                if ($masterRequest->attributes->get('_route') != 'genj_sso_client_login') {
                    $redirectUri = $masterRequest->getUri();
                } else {
                    $redirectUri = $this->generateUrl('homepage');
                }
            }

            return new RedirectResponse($redirectUri);
        }

        $userData = $broker->getInfo();
        if ($userData->status->code != 200) {
            $userData = (object) array('data' => array());
        }

        $requestStack  = $this->container->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();

        return $this->render(
            'GenjSsoClientBundle:Login:index.html.twig',
            array(
                'redirectUri' => $masterRequest->getUri(),
                'userData' => $userData->data
            )
        );
    }

    public function logoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);

        $request->getSession()->remove('ssoAuthToken');
        $request->getSession()->invalidate();

        /**
         * @var Broker $client
         */
        $broker = $this->get('genj_sso_client.broker');
        $broker->logout();

        return new RedirectResponse($request->headers->get('referer'));
    }

    public function attachAction(Request $request)
    {
        $ssoClientService = $this->container->get('genj_sso_client.broker');

        if (!$ssoClientService->isAttached()) {
            return $ssoClientService->autoAttach($request->getUri());
        } else {
            return new JsonResponse(array('status' => 'attached'));
        }
    }
}
