<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $this->$I

class Acceptance extends \Codeception\Module
{
    protected \AcceptanceTester $tester;

    public function loginAsCustomer($username = 'user99', $password = '99')
    {
        $I = $this;
        $I->amOnPage('/login');
        $I->see('Вход в личный кабинет');
        $I->selectOption('form select[name="region"]', 'Барановичские электрические сети');
        $I->fillField('login', $username);
        $I->fillField('password', $password);
        $I->click('Войти');
        $I->seeCurrentUrlEquals('/contract');
    }
}
