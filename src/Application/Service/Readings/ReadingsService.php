<?php
declare(strict_types=1);


namespace App\Application\Service\Readings;


use PDO;

class ReadingsService
{
    /** @var PDO */
    protected $pdo;
    private $customer;

    private function saveToFileJson(array $readingsData)
    {
        $filename = sprintf("%s-%s.json", $contractNom, date('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings/%s/", date('Ym'));
        if (!file_exists($dir)) {
            if (!mkdir($dir) && !is_dir($dir)) {
                $this->logger->error('Directory {dir} was not created', ['dir' => $dir]);
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }

    }
    private function saveToFileCsv(array $readingsData)
    {
        $readingsCsvSaveDirectory = 'd:';
        $filename = sprintf(
            '%s/%s-%s.txt',
            $readingsCsvSaveDirectory,
            $this->customer['code_contract'],
            date('Ym')
        );
        foreach ($readingsData as $reading) {
            fputcsv()
        }

    }

    private function emailToCustomer(array $readingsData)
    {

    }

    private function logToManager(array $readingsData)
    {
        $customersLogRepository = new CustomersLogRepository($this->pdo);
        $customersLogRepository->addInfo(
            [
                'login' => '',
                'date' => new \DateTime(),
                'type' => 'info',
                'message' => 'переданы показания',
            ]
        );
    }


    public function __construct(PDO $pdo, $customer)
    {
        $this->pdo = $pdo;
        $this->customer = $customer;
    }

    public function publishReadings(array $readingsData)
    {
        $publishers = [
            [$this, 'emailToCustomer'],
            [$this, 'saveToFileJson'],
            [$this, 'saveToFileCsv'],
            [$this, 'logToManager'],
        ];

        foreach ($publishers as $publisher) {
            //todo $publisher->publish($readingsData);
            $publisher($readingsData);
        }
    }
}