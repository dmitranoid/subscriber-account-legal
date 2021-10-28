<?php


class SendReadingsPageCest
{
    public function _before(AcceptanceTester $I)
    {

    }

    // tests
    public function tryToTestSendReadingsPage(\Step\Acceptance\Front $I)
    {
        $I->loginAsCustomer();
        $I->amOnPage('/sendreadings');
        $I->see('Передача показаний приборов учета');
        $I->see('Ввод показаний за период:');
    }
}

