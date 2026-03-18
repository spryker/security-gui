<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Communication\Checker;

use Spryker\Service\Http\HttpServiceInterface;
use Spryker\Zed\SecurityGui\Dependency\Facade\SecurityGuiToSecurityFacadeInterface;
use Symfony\Component\HttpFoundation\Request;

class LastVisitedPageUrlChecker implements LastVisitedPageUrlCheckerInterface
{
    public function __construct(
        protected SecurityGuiToSecurityFacadeInterface $securityFacade,
        protected HttpServiceInterface $httpService,
    ) {
    }

    public function isEligibleForPostLoginRedirect(Request $request): bool
    {
        if (!$this->securityFacade->isUserLoggedIn()) {
            return false;
        }

        return $this->httpService->isRequestEligibleForRedirect($request);
    }
}
