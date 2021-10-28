<?php

class AddManagerPageCest
{

    public function tryToAddManagerPage(\Step\Acceptance\Front $I)
    {
        $I->loginAsAdmin();
        $I->click('Добавить пользователя');
        $I->seeInCurrentUrl('/admin/user');
        $I->see('Новый пользователь');
    }
}
