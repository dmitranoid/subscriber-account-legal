<?php
declare(strict_types=1);


namespace App\Application\Service\Readings;


use App\Application\Exceptions\AppException;
use App\Infrastructure\Persistence\User\InMemoryManagerRepository;
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

    public function publishReadings(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {
        $publishers = [
            [$this, 'saveToFileJson'],
            [$this, 'saveToFileCsv'],
            [$this, 'emailToCustomer'],
            [$this, 'emailToManager'],
            [$this, 'logToManager'],
        ];

        try {
            foreach ($publishers as $publisher) {
                //todo переделать в $publisher->publish($readingsData);
                $publisher($contract, $abonentsList, $connectionsList, $readings);
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

    private function saveToFileJson(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {
        // load settings
        $filename = sprintf("%s-%s.json", $this->customer['code_contract'], date('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings/%s/", date('Ym'));
        // save
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
//                $this->logger->error('Directory {dir} was not created', ['dir' => $dir]);
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        file_put_contents($dir . $filename, json_encode($readings));
    }

    private function saveToFileCsv(array $contract, array $abonentsList, array $connectionsList, array $readings)
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
        foreach ($readings as $counterNo => $reading) {
            fwrite($csvFileHandler, $counterNo . '~' . $reading . PHP_EOL);
        }
        fclose($csvFileHandler);
    }

    private function emailToCustomer(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {
        // load settings
        // customer
        $recipientEmail = $this->customer['email'] ?? null;
        if (empty($recipientEmail)) {
            return;
        }
        $subject = sprintf('показания за потребленную электроэнергию за %s', (new DateTime('now'))->format('m.Y'));
        $body = $this->composeReadingsEmail($contract, $abonentsList, $connectionsList, $readings);
        try {
            (new PhpMailerEmailSender)->send($recipientEmail, $subject, $body);
        } catch (AppException $e) {
            throw $e;
        }
    }

    private function emailToManager(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {
        // load settings
        // manager
        $managerAmperPassword = $contract['pw'];
        $recipient = (new InMemoryManagerRepository())->findByPwd($managerAmperPassword);
        if (empty($recipient) && $recipient['email'] ?? false) {
            exit;
        }
        $subject = sprintf('[lkprom] %s - %s за %s', $contract['kp'], $contract['np1'], (new DateTime('now'))->format('m.Y'));
        $body = $this->composeReadingsEmail($contract, $abonentsList, $connectionsList, $readings);
        try {
            (new PhpMailerEmailSender)->send($recipient['email'], $subject, $body);
        } catch (AppException $e) {
            throw $e;
        }
    }

    private function composeReadingsEmail(array $contract, array $abonentsList, array $connectionsList, array $readings): string
    {
        $readingsTxt = [];
        $readingsTxt[] = sprintf('Показания по договору %s - %s', $contract['kp'], $contract['np1']);
        $readingsTxt[] = sprintf('Дата и время предоставления данных: %s', (new DateTime('now'))->format('d.m.Y H:i'));
        $readingsTxt[] = '';

        /* combine abonents and connections */
        usort($abonentsList, function ($a, $b) {
            return $a['nab'] - $b['nab'];
        });
        usort($connectionsList, function ($a, $b) {
            return $a['nab'] - $b['nab'];
        });

        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList) {
            while ($abonent['nab'] === (current($connectionsList)['nab'] ?? '')) {
                $abonent['connections'][] = array_shift($connectionsList);
            }
        });
        foreach ($abonentsList as $abonent) {
            $readingsTxt[] = sprintf('абонент %s - %s', $abonent['nab'], $abonent['naim']);
            foreach ($abonent['connections'] as $connection) {
                // приводим счетчик к тому же виду что и при заполнении (readings.twig)
                $counterNo = str_replace('.', '_', 'cnt-' . $abonent['kp'] . '-' . $abonent['nab'] . '-' . $connection['nst']);
                $reading = array_filter($readings, function ($cntNo) use ($counterNo) {
                    return ($cntNo === $counterNo);
                }, ARRAY_FILTER_USE_KEY);
                $readingsTxt[] = sprintf("  т.у. %s - %s \t\t%s", $connection['naim'], $connection['nst'], $reading[$counterNo]);
            }
        }
        $readingsTxt[] = '';
        $readingsTxt[] = 'Данные переданы посредством личного кабинета www.brestenergo.by';

//        file_put_contents('d:\mailtest.txt', implode(PHP_EOL, $readingsTxt));
        return implode(PHP_EOL, $readingsTxt);
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