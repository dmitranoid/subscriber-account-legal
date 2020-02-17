<?php
declare(strict_types=1);


namespace App\Application\Service\Readings;


use App\Application\Exceptions\AppException;
use App\Infrastructure\Service\Email\PhpMailerEmailSender;
use DateTime;
use Exception;
use PDO;
use RuntimeException;

class ReadingsService
{
    /** @var PDO */
    protected $pdo;
    private $customer;

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

        try {
            foreach ($publishers as $publisher) {
                //todo $publisher->publish($readingsData);
                $publisher($readingsData);
            }
        } catch (Exception $e) {
            throw new AppException($e->getMessage(), 0, $e);
        }
        // remove saved readings
        $this->removeTmpReadings();
    }

    public function removeTmpReadings()
    {
        $filename = sprintf("%s.json", $this->customer['code_contract']);
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/../storage/readings-tmp/";
        if (file_exists($dir . $filename)) {
            unlink($dir . $filename);
        }
    }

    public function loadPeriodReadings(DateTime $onDate): ?array
    {
        // load settings
        $filename = sprintf("%s-%s.json", $this->customer['code_contract'], date('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings/%s/", date('Ym'));

        // load
        if (!file_exists($dir . $filename)) {
            return null;
        }
        $readingsText = file_get_contents($dir . $filename);
        try {
            // $readingsData = json_decode($readingsText, true, 512, JSON_THROW_ON_ERROR);
            $readingsData = json_decode($readingsText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // $this->logger->error(json_last_error_msg());
                return null;
            }
        } catch (Exception $e) {
            //$this->logger->error($e->getMessage);
            return null;
        }
        return $readingsData;
    }

    public function saveTmpReadings(array $readingsData)
    {
        // load settings
        $filename = sprintf("%s.json", $this->customer['code_contract']);
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/../storage/readings-tmp/";
        // save
        try {
            file_put_contents($dir . $filename, json_encode($readingsData));
        } catch (Exception $e) {
            throw new AppException('не удалось сохранить данные, обратитесь к администратору');
        }
    }

    public function loadTmpReadings(): ?array
    {
        // load settings
        $filename = sprintf("%s.json", $this->customer['code_contract']);
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/../storage/readings-tmp/";
        // load
        if (!file_exists($dir . $filename)) {
            return null;
        }
        $readingsText = file_get_contents($dir . $filename);
        try {
//            $readingsData = json_decode($readingsText, true, 512, JSON_THROW_ON_ERROR);
            $readingsData = json_decode($readingsText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // $this->logger->error(json_last_error_msg());
                return null;
            }
        } catch (Exception $e) {
            //$this->logger->error($e->getMessage);
            return null;
        }
        return $readingsData;
    }

    private function saveToFileJson(array $readingsData)
    {
        // load settings
        $filename = sprintf("%s-%s.json", $this->customer['code_contract'], date('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings/%s/", date('Ym'));
        // save
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
//                $this->logger->error('Directory {dir} was not created', ['dir' => $dir]);
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        file_put_contents($dir . $filename, json_encode($readingsData));
    }

    private function saveToFileCsv(array $readingsData)
    {
        // load settings
        $filename = sprintf("%s-%s.txt", $this->customer['code_contract'], date('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings-csv/%s/", date('Ym'));
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
//                $this->logger->error('Directory {dir} was not created', ['dir' => $dir]);
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        // для теста попробуем сохранить по другому
        $csvFileHandler = fopen($dir . $filename, 'w');
        foreach ($readingsData as $counterNo => $reading) {
            fputs($csvFileHandler, $counterNo . '~' . $reading . PHP_EOL);
        }
        fclose($csvFileHandler);
    }

    private function emailToCustomer(array $readingsData)
    {
        // load settings
        // customer
        $recipientEmail = $this->customer['email'] ?? null;
        if (empty($recipientEmail)) {
            exit;
        }
        $subject = sprintf('показания за потребленную электроэнергию от %s', (new DateTime('now'))->format('mm Y'));
        // format readings
        $readingsTxt = '';
        foreach ($readingsData as $counterNo => $reading) {
            $readingsTxt .= sprintf("%s\t%s", $counterNo, $reading) . PHP_EOL;
        }
        $body = 'Вами переданы следующие показания за потребленную электроэнергию: ' . PHP_EOL . $readingsTxt;
        try {
            (new PhpMailerEmailSender)->send($recipientEmail, $subject, $body);
        } catch (AppException $e) {
            throw $e;
        }
    }

    private function emailToManager(array $readingsData)
    {
        // load settings
        // customer
        $recipientEmail = $this->customer['email'] ?? null;
        if (empty($recipientEmail)) {
            exit;
        }
        $subject = sprintf('показания за потребленную электроэнергию от %s', (new DateTime('now'))->format('mm Y'));
        // format readings
        $readingsTxt = '';
        foreach ($readingsData as $counterNo => $reading) {
            $readingsTxt .= sprintf("%s\t%s", $counterNo, $reading) . PHP_EOL;
        }
        $body = 'Вами переданы следующие показания за потребленную электроэнергию: ' . PHP_EOL . $readingsTxt;
        try {
            (new PhpMailerEmailSender)->send($recipientEmail, $subject, $body);
        } catch (AppException $e) {
            throw $e;
        }
    }

    private function logToManager(array $readingsData)
    {
//        $customersLogRepository = new CustomersLogRepository($this->pdo);
//        $customersLogRepository->addInfo(
//            [
//                'login' => '',
//                'date' => new DateTime(),
//                'type' => 'info',
//                'message' => 'переданы показания',
//            ]
//        );
    }
}