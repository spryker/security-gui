<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\SecurityGui\Communication\Form;

use Codeception\Test\Unit;
use Spryker\Zed\SecurityGui\Communication\Form\LoginForm;
use Spryker\Zed\SecurityGui\SecurityGuiDependencyProvider;
use Spryker\Zed\SecurityGuiExtension\Dependency\Plugin\UserLoginFormExpanderPluginInterface;
use SprykerTest\Zed\SecurityGui\SecurityGuiCommunicationTester;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group SecurityGui
 * @group Communication
 * @group Form
 * @group LoginFormTest
 * Add your own group annotations below this line
 */
class LoginFormTest extends Unit
{
    protected const string FIELD_CAPTCHA = 'captcha';

    protected SecurityGuiCommunicationTester $tester;

    public function testBuildFormAddsFieldOfUserLoginFormExpanderPluginAfterLoginFields(): void
    {
        // Arrange
        $this->tester->setDependency(SecurityGuiDependencyProvider::PLUGINS_USER_LOGIN_FORM_EXPANDER, [
            $this->createCaptchaFieldExpanderPlugin(),
        ]);

        // Act
        $form = $this->createLoginForm();

        // Assert
        $this->assertSame(
            [LoginForm::FIELD_USERNAME, LoginForm::FIELD_PASSWORD, static::FIELD_CAPTCHA],
            array_keys($form->all()),
        );
    }

    public function testBuildFormKeepsLoginFieldsWhenNoUserLoginFormExpanderPluginIsRegistered(): void
    {
        // Act
        $form = $this->createLoginForm();

        // Assert
        $this->assertSame(
            [LoginForm::FIELD_USERNAME, LoginForm::FIELD_PASSWORD],
            array_keys($form->all()),
        );
    }

    /**
     * @return \Symfony\Component\Form\FormInterface<mixed>
     */
    protected function createLoginForm(): FormInterface
    {
        $loginFormMock = $this->getMockBuilder(LoginForm::class)
            ->onlyMethods(['getFactory'])
            ->getMock();
        $loginFormMock->expects($this->atLeastOnce())
            ->method('getFactory')
            ->willReturn($this->tester->getFactory());

        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addType($loginFormMock)
            ->getFormFactory()
            ->create($loginFormMock::class);
    }

    protected function createCaptchaFieldExpanderPlugin(): UserLoginFormExpanderPluginInterface
    {
        $userLoginFormExpanderPluginMock = $this->createMock(UserLoginFormExpanderPluginInterface::class);
        $userLoginFormExpanderPluginMock->expects($this->once())
            ->method('expand')
            ->willReturnCallback(function (FormBuilderInterface $builder): FormBuilderInterface {
                return $builder->add(static::FIELD_CAPTCHA, TextType::class);
            });

        return $userLoginFormExpanderPluginMock;
    }
}
