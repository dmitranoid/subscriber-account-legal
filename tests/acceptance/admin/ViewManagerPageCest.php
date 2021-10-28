<?php

class ViewManagerPageCest
{

    public function tryToTestManagerPage(\Step\Acceptance\Front $I)
    {
        $I->loginAsAdmin();
        $I->click('ШляхтичДВ');
        $I->seeInCurrentUrl('/admin/user/' . urlencode('ШляхтичДВ'));
        $I->see('Редактирование пользователя');
    }
}
