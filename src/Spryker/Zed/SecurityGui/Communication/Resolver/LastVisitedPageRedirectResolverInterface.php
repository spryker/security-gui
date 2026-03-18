<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Communication\Resolver;

use Symfony\Component\HttpFoundation\Request;

interface LastVisitedPageRedirectResolverInterface
{
    public function hasRedirectUrl(Request $request): bool;

    public function getRedirectUrl(Request $request): string;
}
