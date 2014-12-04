<?php

namespace Genj\SsoClientBundle\Form\Handler;


use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormHandler {

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function processForm(Form $form) {
        $userData = $form->getData();
        // Post this data to the server

        $response = array('status' => 200, 'data' => array('username' => $userData['email']));

        return true;
    }
}