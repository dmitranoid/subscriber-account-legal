<?php


class LogoutCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(\Step\Acceptance\Front $I)
    {
        $I->loginAsCustomer();
        $I->see('Выйти');
        $I->click('Выйти');
        $I->seeCurrentUrlEquals('/login');
    }
}
