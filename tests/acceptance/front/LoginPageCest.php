<?php


class LoginPageCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Вход в личный кабинет');
    }

    // tests
    public function successLoginAsCustomer(AcceptanceTester $I)
    {
        $I->selectOption('form select[name="region"]', 'Барановичские электрические сети');
        $I->fillField('login', 'user99');
        $I->fillField('password', '99');
        $I->click('Войти');
        $I->seeCurrentUrlEquals('/contract');
        $I->see('Данные о договоре');
    }

    public function failedLoginAsCustomerCauseWrongUsername(AcceptanceTester $I)
    {
        $I->selectOption('form select[name="region"]', 'Барановичские электрические сети');
        $I->fillField('login', 'WrongUserName');
        $I->fillField('password', '99');
        $I->click('Войти');
        $I->see('неправильные имя пользователя или пароль');
    }

    public function failedLoginAsCustomerCauseWrongPassword(AcceptanceTester $I)
    {
        $I->selectOption('form select[name="region"]', 'Барановичские электрические сети');
        $I->fillField('login', 'user99');
        $I->fillField('password', 'WrongPassword');
        $I->click('Войти');
        $I->see('неправильные имя пользователя или пароль');
    }

}
