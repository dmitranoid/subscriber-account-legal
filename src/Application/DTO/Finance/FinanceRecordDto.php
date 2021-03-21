<?php
declare(strict_types=1);


namespace App\Application\DTO\Finance;


use DateTimeImmutable;

class FinanceRecordDto
{
    /** @var string номер договора */
    public string $contractNo;
    /** @var DateTimeImmutable $date дата документа */
    public DateTimeImmutable $date;
    /** @var DateTimeImmutable $period расчетный период документа (значимые только месяц-год) */
    public DateTimeImmutable $period;
    /** @var string $doctype тип документа */
    public string $doctype;
    /** @var string $docNo тип документа */
    public string $docNo;
    /** @var float $sumDt дебетовая сумма (выставлено нами) */
    public float $sumDt;
    /** @var float $sumKt кредитовая сумма (оплачено нам) */
    public float $sumKt;

    /**
     * FinanceRecordDto constructor.
     * @param DateTimeImmutable $date
     * @param DateTimeImmutable $period
     * @param string $doctype
     * @param string $docNo
     * @param float $sumDt
     * @param float $sumKt
     */
    public function __construct(string $contractNo, DateTimeImmutable $date, DateTimeImmutable $period, string $doctype, string $docNo, float $sumDt, float $sumKt)
    {
        $this->contractNo = $contractNo;
        $this->date = $date;
        $this->period = $period;
        $this->doctype = $doctype;
        $this->docNo = $docNo;
        $this->sumDt = $sumDt;
        $this->sumKt = $sumKt;
    }

}