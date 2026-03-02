<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Dependency\Facade;

class SecurityGuiToSecurityFacadeBridge implements SecurityGuiToSecurityFacadeInterface
{
    /**
     * @var \Spryker\Zed\Security\Business\SecurityFacadeInterface
     */
    protected $securityFacade;

    /**
     * @param \Spryker\Zed\Security\Business\SecurityFacadeInterface $securityFacade
     */
    public function __construct($securityFacade)
    {
        $this->securityFacade = $securityFacade;
    }

    public function isUserLoggedIn(): bool
    {
        return $this->securityFacade->isUserLoggedIn();
    }
}
