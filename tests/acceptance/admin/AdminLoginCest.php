<?php

class AdminLoginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function loginAsAdmin(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Личный кабинет');
        $I->fillField('login', 'lkadmin');
        $I->fillField('password', 'lkadmin');
        $I->click('Войти');
        $I->see('Список пользователей');
    }
}
