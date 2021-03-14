<?php

namespace Step\Acceptance;

class Front extends \AcceptanceTester
{

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