<?php

class ContractPageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTestContractPage(Step\Acceptance\Front $I)
    {
        $I->loginAsCustomer();
        $I->amGoingTo('/');
        $I->see('Данные о договоре');
        $I->see('Договор №');
        $I->see('Дата заключения');
    }
}
