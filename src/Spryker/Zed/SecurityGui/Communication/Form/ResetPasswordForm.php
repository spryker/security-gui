<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Communication\Form;

use Spryker\Shared\Validator\Constraints\NotCompromisedPassword;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @method \Spryker\Zed\SecurityGui\Communication\SecurityGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\SecurityGui\Business\SecurityGuiFacadeInterface getFacade()
 * @method \Spryker\Zed\SecurityGui\SecurityGuiConfig getConfig()
 */
class ResetPasswordForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_PASSWORD = 'password';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addPasswordField($builder);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'reset_password';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addPasswordField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_PASSWORD, RepeatedType::class, [
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => $this->getConfig()->getUserPasswordMinLength(),
                    'max' => $this->getConfig()->getUserPasswordMaxLength(),
                ]),
                new Regex([
                    'pattern' => $this->getConfig()->getUserPasswordPattern(),
                    'message' => $this->getConfig()->getPasswordValidationMessage(),
                ]),
                new NotCompromisedPassword(),
            ],
            'invalid_message' => 'The password fields must match.',
            'first_options' => [
                'label' => 'Password',
                'attr' => [
                    'placeholder' => 'Password',
                ],
            ],
            'second_options' => [
                'label' => 'Repeat Password',
                'attr' => [
                    'placeholder' => 'Repeat Password',
                ],
            ],
            'required' => true,
            'type' => PasswordType::class,
            'attr' => [
                'class' => 'btn btn-default btn-block btn-outline',
            ],
        ]);

        return $this;
    }
}
