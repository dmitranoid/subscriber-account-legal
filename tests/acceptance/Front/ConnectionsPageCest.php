<?php


class ConnectionsPageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(\Step\Acceptance\Front $I)
    {
        $I->loginAsCustomer();
        $I->amOnPage('/connections');
        $I->see('Точки учета');
    }
}
