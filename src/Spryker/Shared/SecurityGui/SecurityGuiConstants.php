<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\SecurityGui;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface SecurityGuiConstants
{
    /**
     * Specification:
     * - Defines whether the last visited page cookie should be sent over HTTPS only.
     *
     * @api
     *
     * @var string
     */
    public const string ZED_IS_SSL_ENABLED = 'SECURITY_GUI:ZED_IS_SSL_ENABLED';
}
