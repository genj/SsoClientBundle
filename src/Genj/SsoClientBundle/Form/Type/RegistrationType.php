<?php

namespace Genj\SsoClientBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'text', array(
                'label'       => 'E-mail address',
                'constraints' => array(new NotBlank(), new Email())
            ));
        $builder->add('password', 'password', array(
                'label'       => 'New Password',
                'constraints' => array(
                    new Length(array('min' => 6, 'max' => 20))
                )
            ));
        $builder->add('confirmPassword', 'password', array(
                'label'       => 'Confirm Password',
                'constraints' => new Callback(array($this, 'validatePasswordsMatch'))
            ));

        $builder->add('acceptTerms', 'checkbox', array(
                'label'    => 'I accept the terms and conditions?',
                'required' => true,
            ));

        $builder->add('ngNewsletter', 'checkbox', array(
                'label'    => 'Subscribe me to the NatGeo Newsletter?',
                'required' => false
            ));

        $builder->add('submit', 'submit');

    }

    public function validatePasswordsMatch($value, ExecutionContextInterface $context) {
        $form = $context->getRoot();
        $data = $form->getData();

        if($data['confirmPassword'] != $data['password']) {
            $context->buildViolation('Passwords must match.')
                ->addViolation();
        }
    }


    public function getName()
    {
        return 'registration';
    }

}