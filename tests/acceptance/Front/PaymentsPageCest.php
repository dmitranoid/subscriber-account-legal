<?php


class PaymentsPageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTestPaymentsPage(\Step\Acceptance\Front $I)
    {
        $I->loginAsCustomer();
        $I->amOnPage('/finances');
        $I->see('Финансы');
    }
}
