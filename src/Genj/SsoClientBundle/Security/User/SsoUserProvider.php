<?php

namespace Genj\SsoClientBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SsoUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username, $authToken = null)
    {
        return new SsoUser($username, $authToken);
    }

    public function loadUserByAuthToken($authToken)
    {
        // make a call to your webservice here
        $userData = array('username' => 'nico.kaag@genj.nl');
        // pretend it returns an array on success, false if there is no user

        if ($userData && $authToken) {
            $username = $userData['username'];

            return new SsoUser($username, $authToken);
        }

        return false;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SsoUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByAuthToken($user->getAuthToken());
    }

    public function supportsClass($class)
    {
        return $class === 'Genj\SsoClientBundle\Security\User\SsoUser';
    }
}