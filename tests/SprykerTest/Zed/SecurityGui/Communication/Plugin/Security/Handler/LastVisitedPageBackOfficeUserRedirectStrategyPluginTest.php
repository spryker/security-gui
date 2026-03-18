<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\SecurityGui\Communication\Plugin\Security\Handler;

use Codeception\Test\Unit;
use Spryker\Zed\SecurityGui\Communication\Plugin\Security\Handler\LastVisitedPageBackOfficeUserRedirectStrategyPlugin;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group SecurityGui
 * @group Communication
 * @group Plugin
 * @group Security
 * @group Handler
 * @group LastVisitedPageBackOfficeUserRedirectStrategyPluginTest
 * Add your own group annotations below this line
 */
class LastVisitedPageBackOfficeUserRedirectStrategyPluginTest extends Unit
{
    protected const string LAST_VISITED_URL = '/back-office/orders/list';

    protected const string URL_BACK_OFFICE_DASHBOARD = '/back-office/dashboard';

    public function testGivenLastVisitedCookiePresentWhenIsApplicableCalledThenReturnsTrue(): void
    {
        // Arrange
        $plugin = new LastVisitedPageBackOfficeUserRedirectStrategyPlugin();
        $request = $this->createRequestWithCookie(static::LAST_VISITED_URL);

        // Act
        $result = $plugin->isApplicable($request);

        // Assert
        $this->assertTrue($result);
    }

    public function testGivenNoLastVisitedCookieWhenIsApplicableCalledThenReturnsFalse(): void
    {
        // Arrange
        $plugin = new LastVisitedPageBackOfficeUserRedirectStrategyPlugin();
        $request = Request::create(static::URL_BACK_OFFICE_DASHBOARD);

        // Act
        $result = $plugin->isApplicable($request);

        // Assert
        $this->assertFalse($result);
    }

    public function testGivenLastVisitedCookiePresentWhenGetRedirectUrlCalledThenReturnsCookieValue(): void
    {
        // Arrange
        $plugin = new LastVisitedPageBackOfficeUserRedirectStrategyPlugin();
        $request = $this->createRequestWithCookie(static::LAST_VISITED_URL);

        // Act
        $result = $plugin->getRedirectUrl($request);

        // Assert
        $this->assertSame(static::LAST_VISITED_URL, $result);
    }

    protected function createRequestWithCookie(string $url): Request
    {
        return Request::create(static::URL_BACK_OFFICE_DASHBOARD, Request::METHOD_GET, [], ['last-visited-page' => $url]);
    }
}
