<?php

namespace Genj\SsoClientBundle\Security\User;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SsoUser implements UserInterface, EquatableInterface
{
    private $username;
    private $authToken;
    private $roles;

    public function __construct($username, $authToken, $roles = array())
    {
        $this->username = $username;
        $this->authToken = $authToken;
        $this->roles = array('ROLE_USER');
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getAuthToken()
    {
        return $this->authToken;
    }

    public function getPassword()
    {
        return $this->authToken;
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof SsoUser) {
            return false;
        }

        if ($this->authToken !== $user->getAuthToken()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}