<?php

namespace Genj\SsoClientBundle\Security;

use Genj\SsoClientBundle\Security\User\SsoUser;
use Genj\SsoClientBundle\Security\User\SsoUserProvider;
use Genj\SsoClientBundle\Sso\Broker;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SsoClientAuthenticator implements SimplePreAuthenticatorInterface
{
    protected $userProvider;
    protected $requestStack;
    protected $brokerConfig;

    public function __construct(SsoUserProvider $userProvider, RequestStack $requestStack, $brokerConfig)
    {
        $this->userProvider = $userProvider;
        $this->requestStack = $requestStack;
        $this->brokerConfig = $brokerConfig;
    }

    public function createToken(Request $request, $providerKey)
    {
        $request = $this->requestStack->getMasterRequest();
        // look for an apikey query parameter
        $authToken = $request->getSession()->get('ssoAuthToken');

        return new PreAuthenticatedToken(
            'anon.',
            $authToken,
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $authToken = $token->getCredentials();

        $broker = new Broker($this->requestStack, $this->brokerConfig);
        $userData = $broker->validateAuthToken($authToken);

        if (!$userData) {
            return new PreAuthenticatedToken(
                'anon.',
                $authToken,
                $providerKey
            );
        }

        $user = new SsoUser($userData->data->username, $userData->data->authToken, array('ROLE_USER'));

        $token = new PreAuthenticatedToken(
            $user,
            $authToken,
            $providerKey,
            $user->getRoles()
        );

        return $token;
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}