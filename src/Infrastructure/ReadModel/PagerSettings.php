<?php
declare(strict_types=1);


namespace App\Infrastructure\ReadModel;


use Psr\Http\Message\RequestInterface;

class PagerSettings
{
    private int $pageNo;
    private int $itemsOnPage;

    /**
     * PagerSettings constructor.
     * @param int $pageNo
     * @param int $itemsOnPage
     */
    public function __construct(int $pageNo, int $itemsOnPage)
    {
        $this->pageNo = $pageNo;
        $this->itemsOnPage = $itemsOnPage;
    }

    public static function fromRequest(RequestInterface $request)
    {

    }

    public function getItemsOnPage(): int
    {
        return $this->itemsOnPage;
    }

    public function getSqlOffset(): int
    {
        return ($this->getPageNo() - 1) * $this->itemsOnPage;
    }

    public function getPageNo(): int
    {
        return $this->pageNo;
    }

    public function getSqlLimit(): int
    {
        return $this->itemsOnPage;
    }

}