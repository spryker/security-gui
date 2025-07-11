<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Communication;

use Generated\Shared\Transfer\UserTransfer;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\SecurityGui\Communication\Authenticator\LoginFormAuthenticator;
use Spryker\Zed\SecurityGui\Communication\Badge\MultiFactorAuthBadge;
use Spryker\Zed\SecurityGui\Communication\Builder\SecurityGuiOptionsBuilder;
use Spryker\Zed\SecurityGui\Communication\Builder\SecurityGuiOptionsBuilderInterface;
use Spryker\Zed\SecurityGui\Communication\Expander\SecurityBuilderExpander;
use Spryker\Zed\SecurityGui\Communication\Expander\SecurityBuilderExpanderInterface;
use Spryker\Zed\SecurityGui\Communication\Form\LoginForm;
use Spryker\Zed\SecurityGui\Communication\Form\ResetPasswordForm;
use Spryker\Zed\SecurityGui\Communication\Form\ResetPasswordRequestForm;
use Spryker\Zed\SecurityGui\Communication\Logger\AuditLogger;
use Spryker\Zed\SecurityGui\Communication\Logger\AuditLoggerInterface;
use Spryker\Zed\SecurityGui\Communication\Plugin\Security\Handler\UserAuthenticationFailureHandler;
use Spryker\Zed\SecurityGui\Communication\Plugin\Security\Handler\UserAuthenticationSuccessHandler;
use Spryker\Zed\SecurityGui\Communication\Plugin\Security\Provider\UserProvider;
use Spryker\Zed\SecurityGui\Communication\Plugin\Security\UserSecurityPlugin;
use Spryker\Zed\SecurityGui\Communication\Security\User;
use Spryker\Zed\SecurityGui\Communication\Security\UserInterface;
use Spryker\Zed\SecurityGui\Dependency\Client\SecurityGuiToSecurityBlockerClientInterface;
use Spryker\Zed\SecurityGui\Dependency\Client\SecurityGuiToSessionClientInterface;
use Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToMessengerFacadeInterface;
use Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToSecurityFacadeInterface;
use Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToUserFacadeInterface;
use Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToUserPasswordResetFacadeInterface;
use Spryker\Zed\SecurityGui\SecurityGuiDependencyProvider;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

/**
 * @method \Spryker\Zed\SecurityGui\SecurityGuiConfig getConfig()
 * @method \Spryker\Zed\SecurityGui\Business\SecurityGuiFacadeInterface getFacade()
 */
class SecurityGuiCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createLoginForm()
    {
        return $this->getFormFactory()->create(LoginForm::class);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createResetPasswordRequestForm()
    {
        return $this->getFormFactory()->create(ResetPasswordRequestForm::class);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createResetPasswordForm()
    {
        return $this->getFormFactory()->create(ResetPasswordForm::class);
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Communication\Plugin\Security\Provider\UserProvider
     */
    public function createUserProvider(): UserProvider
    {
        return new UserProvider(
            $this->getUserRoleFilterPlugins(),
            $this->getUserLoginRestrictionPlugins(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param array<string> $roles
     *
     * @return \Spryker\Zed\SecurityGui\Communication\Security\UserInterface
     */
    public function createSecurityUser(UserTransfer $userTransfer, array $roles): UserInterface
    {
        return new User($userTransfer, $roles);
    }

    /**
     * @return \Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface
     */
    public function createUserAuthenticationSuccessHandler(): AuthenticationSuccessHandlerInterface
    {
        return new UserAuthenticationSuccessHandler();
    }

    /**
     * @return \Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface
     */
    public function createUserAuthenticationFailureHandler(): AuthenticationFailureHandlerInterface
    {
        return new UserAuthenticationFailureHandler();
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Communication\Logger\AuditLoggerInterface
     */
    public function createAuditLogger(): AuditLoggerInterface
    {
        return new AuditLogger();
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToUserFacadeInterface
     */
    public function getUserFacade(): SecurityGuiToUserFacadeInterface
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::FACADE_USER);
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToUserPasswordResetFacadeInterface
     */
    public function getUserPasswordResetFacade(): SecurityGuiToUserPasswordResetFacadeInterface
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::FACADE_USER_PASSWORD_RESET);
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToMessengerFacadeInterface
     */
    public function getMessengerFacade(): SecurityGuiToMessengerFacadeInterface
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::FACADE_MESSENGER);
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToSecurityFacadeInterface
     */
    public function getSecurityFacade(): SecurityGuiToSecurityFacadeInterface
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::FACADE_SECURITY);
    }

    /**
     * @return array<\Spryker\Zed\SecurityGuiExtension\Dependency\Plugin\AuthenticationLinkPluginInterface>
     */
    public function getAuthenticationLinkPlugins(): array
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::PLUGINS_AUTHENTICATION_LINK);
    }

    /**
     * @return array<\Spryker\Zed\SecurityGuiExtension\Dependency\Plugin\UserRoleFilterPluginInterface>
     */
    public function getUserRoleFilterPlugins(): array
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::PLUGINS_USER_ROLE_FILTER);
    }

    /**
     * @return array<\Spryker\Zed\SecurityGuiExtension\Dependency\Plugin\UserLoginRestrictionPluginInterface>
     */
    public function getUserLoginRestrictionPlugins(): array
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::PLUGINS_USER_LOGIN_RESTRICTION);
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Communication\Builder\SecurityGuiOptionsBuilderInterface
     */
    public function createSecurityGuiOptionsBuilder(): SecurityGuiOptionsBuilderInterface
    {
        return new SecurityGuiOptionsBuilder(
            $this->getConfig(),
            $this->createUserProvider(),
        );
    }

    /**
     * @return \Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface
     */
    public function createLoginFormAuthenticator(): AuthenticatorInterface
    {
        return new LoginFormAuthenticator(
            $this->createUserProvider(),
            $this->createUserAuthenticationSuccessHandler(),
            $this->createUserAuthenticationFailureHandler(),
            $this->getConfig(),
            $this->createMultiFactorAuthBadge(),
        );
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Communication\Expander\SecurityBuilderExpanderInterface
     */
    public function createSecurityBuilderExpander(): SecurityBuilderExpanderInterface
    {
        if (class_exists(AuthenticationProviderManager::class) === true) {
            return new UserSecurityPlugin();
        }

        return new SecurityBuilderExpander(
            $this->createSecurityGuiOptionsBuilder(),
            $this->getConfig(),
            $this->createLoginFormAuthenticator(),
        );
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Dependency\Client\SecurityGuiToSecurityBlockerClientInterface
     */
    public function getSecurityBlockerClient(): SecurityGuiToSecurityBlockerClientInterface
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::CLIENT_SECURITY_BLOCKER);
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Communication\Badge\MultiFactorAuthBadge
     */
    public function createMultiFactorAuthBadge(): MultiFactorAuthBadge
    {
        return new MultiFactorAuthBadge($this->getUserMultiFactorAuthenticationHandlerPlugins());
    }

    /**
     * @return array<\Spryker\Zed\SecurityGuiExtension\Dependency\Plugin\AuthenticationHandlerPluginInterface>
     */
    public function getUserMultiFactorAuthenticationHandlerPlugins(): array
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::PLUGINS_USER_AUTHENTICATION_HANDLER);
    }

    /**
     * @return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::SERVICE_SECURITY_TOKEN_STORAGE);
    }

    /**
     * @return \Spryker\Zed\SecurityGui\Dependency\Client\SecurityGuiToSessionClientInterface
     */
    public function getSessionClient(): SecurityGuiToSessionClientInterface
    {
        return $this->getProvidedDependency(SecurityGuiDependencyProvider::CLIENT_SESSION);
    }
}
