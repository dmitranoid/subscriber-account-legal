<?php
declare(strict_types=1);

use App\Regionally\Bares\Helpers\DateTimeHelper;
use Codeception\Test\Unit;

class DateTimeHelperTest extends Unit
{

    /**
     * @dataProvider dataProviderQuarterValid
     */
    public function testDateFromQuarterValid($y, $q, $d, $expected)
    {
        $date = DateTimeHelper::dateFromQuarter($y, $q, $d);
        $this->assertEquals($expected, $date->format('Ymd'));
    }

    public function dataProviderQuarterValid()
    {
        return [
            ['y' => '2021', 'q' => '01', 'd' => null, 'result' => '20210101'],
            ['y' => 2021, 'q' => 1, 'd' => 03, 'result' => '20210103'],
            ['y' => 2021, 'q' => 1, 'd' => 42, 'result' => '20210103'],
        ];
    }


    /**
     * @dataProvider dataProviderQuarterFail
     */
    public function testDateFromQuarterFail($y, $m, $d)
    {
        $date = DateTimeHelper::dateFromQuarter($y, $m, $d);
        $this->assertNull($date);
    }

    public function dataProviderQuarterFail()
    {
        return [
            ['y' => '2021', 'm' => '0012', 'd' => null],
            ['y' => 2021, 'm' => 1, 'd' => 32],
            ['y' => 2021, 'm' => -1, 'd' => 1],
            ['y' => 2021, 'm' => 0, 'd' => 0],
            ['y' => 2021, 'm' => 1, 'd' => false],
        ];
    }


    public function testDateFromParts()
    {

    }

    public function testIsDbfDateValid()
    {

    }
}
