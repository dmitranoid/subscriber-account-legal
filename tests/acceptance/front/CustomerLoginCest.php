<?php

class CustomerLoginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function loginAsCustomer(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Личный кабинет');
        $I->fillField('login', 'user48');
        $I->fillField('password', '48');
        $I->click('Войти');
        $I->see('Данные о договоре');
    }
}
