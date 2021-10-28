<?php


class DocsPageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTestDocsPage(Step\Acceptance\Front $I)
    {
        $I->loginAsCustomer();
        $I->amOnPage('/docs');
        $I->see('Документы');
    }
}
