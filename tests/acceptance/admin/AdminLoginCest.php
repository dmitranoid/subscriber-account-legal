<?php

class AdminLoginCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Личный кабинет');
    }

    // tests
    public function loginAsAdmin(AcceptanceTester $I)
    {
        $I->selectOption('form select[name="region"]', 'Барановичские электрические сети');
        $I->fillField('login', 'lkadmin');
        $I->fillField('password', 'lkadmin');
        $I->click('Войти');
        $I->see('Список пользователей');
    }
}
