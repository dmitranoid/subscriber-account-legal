<?php namespace front;


use \AcceptanceTester;

class LogoutCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->LoginAsCustomer();
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->see('Выйти');
        $I->click('Выйти');
        $I->seeCurrentUrlEquals('/login');
    }
}
