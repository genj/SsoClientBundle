<?php

namespace Genj\SsoClientBundle\Controller;

use Genj\SsoClientBundle\Form\Type\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ArticleController
 *
 * @package Genj\ArticleBundle\Controller
 */
class RegistrationController extends Controller
{
    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new RegistrationType());

        $requestStack  = $this->container->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formHandler = $this->get('sso_client.registration.form.handler');

                $formHandler->processForm($form);

                return new Response('Valid Form');
            }
        }

        return $this->render(
            'GenjSsoClientBundle:Registration:form.html.twig',
            array(
                'redirectUri' => $masterRequest->getUri(),
                'form' => $form->createView()
            )
        );
    }
}
