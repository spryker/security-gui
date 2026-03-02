<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Dependency\Client;

use Generated\Shared\Transfer\SecurityCheckAuthContextTransfer;
use Generated\Shared\Transfer\SecurityCheckAuthResponseTransfer;

interface SecurityGuiToSecurityBlockerClientInterface
{
    public function incrementLoginAttemptCount(SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer): SecurityCheckAuthResponseTransfer;

    public function isAccountBlocked(SecurityCheckAuthContextTransfer $securityCheckAuthContextTransfer): SecurityCheckAuthResponseTransfer;
}
