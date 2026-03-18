<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Communication\Plugin\Security\Handler;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\SecurityGuiExtension\Dependency\Plugin\BackOfficeUserRedirectStrategyPluginInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\SecurityGui\Communication\SecurityGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\SecurityGui\SecurityGuiConfig getConfig()
 * @method \Spryker\Zed\SecurityGui\Business\SecurityGuiFacadeInterface getFacade()
 */
class LastVisitedPageBackOfficeUserRedirectStrategyPlugin extends AbstractPlugin implements BackOfficeUserRedirectStrategyPluginInterface
{
    /**
     * {@inheritDoc}
     * - Returns `true` if a valid last visited page URL is found in the last visited page storage.
     * - The storage strategy is configurable and defaults to cookie-based storage.
     *
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function isApplicable(Request $request): bool
    {
        return $this->getFactory()->createLastVisitedPageRedirectResolver()->hasRedirectUrl($request);
    }

    /**
     * {@inheritDoc}
     * - Returns the last visited Back Office page URL from the last visited page storage.
     * - The storage strategy is configurable and defaults to cookie-based storage.
     *
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    public function getRedirectUrl(Request $request): string
    {
        return $this->getFactory()->createLastVisitedPageRedirectResolver()->getRedirectUrl($request);
    }
}
