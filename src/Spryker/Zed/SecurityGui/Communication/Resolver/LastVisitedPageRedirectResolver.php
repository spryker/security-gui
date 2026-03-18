<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Communication\Resolver;

use Spryker\Service\Http\HttpServiceInterface;
use Spryker\Zed\SecurityGui\Communication\Storage\LastVisitedPageStorageInterface;
use Symfony\Component\HttpFoundation\Request;

class LastVisitedPageRedirectResolver implements LastVisitedPageRedirectResolverInterface
{
    public function __construct(
        protected LastVisitedPageStorageInterface $lastVisitedPageStorage,
        protected HttpServiceInterface $httpService,
    ) {
    }

    public function hasRedirectUrl(Request $request): bool
    {
        return $this->lastVisitedPageStorage->get($request) !== '';
    }

    public function getRedirectUrl(Request $request): string
    {
        $url = $this->lastVisitedPageStorage->get($request);

        if (!$this->httpService->isValidRelativeUrl($url)) {
            return '';
        }

        return $url;
    }
}
