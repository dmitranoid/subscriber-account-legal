<?php
declare(strict_types=1);


namespace App\Application\Service\Readings;


use App\Application\DTO\Abonent\AbonentInfoDto;
use App\Application\DTO\Connection\ConnectionDto;
use App\Application\Exceptions\AppException;
use App\Application\View\TwigTextHelper;
use App\Domain\Authentication\Account\Account;
use App\Infrastructure\Persistence\Account\InMemoryManagerRepository;
use App\Infrastructure\Service\Email\PhpMailerEmailSender;
use DateTime;
use DateTimeImmutable;
use Exception;
use JsonException;
use PDO;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ReadingsService
{
    protected PDO $pdo;

    private Account $customer;
    /** @var int день месяца когда начинается передача показаний */
    private int $readingsPeriodFirstDay = 25;
    /** @var int день месяца когда заканчивается передача показаний */
    private int $readingsPeriodLastDay = 5;

    private LoggerInterface $logger;


    public function __construct(PDO $pdo, Account $customer, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->customer = $customer;
        $this->logger = $logger;
    }

    /**
     * Публикация показаний
     *
     * @param array $contract номер договора
     * @param array $abonentsList список абонентов
     * @param array $connectionsList список точек учета
     * @param array $readings показания
     * @throws AppException
     */
    public function publishReadings(array $contract, array $abonentsList, array $connectionsList, array $readings): void
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
                //todo сделать dispatcher и передавать как то вроде $publisher->publish($readingsData);
                $publisher($contract, $abonentsList, $connectionsList, $readings);
            }
        } catch (Exception $e) {
            throw new AppException($e->getMessage(), 0, $e);
        }
        // если все обработалось хорошо, удаляем временные показания
        $this->removeTmpReadings();
    }

    public function removeTmpReadings()
    {
        $filename = sprintf("%s.json", $this->customer->getContractNo());
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/../storage/readings-tmp/";
        if (file_exists($dir . $filename)) {
            unlink($dir . $filename);
        }
    }

    public function loadPeriodReadings(DateTime $onDate): ?array
    {
        // load settings
        /** @var DateTimeImmutable $readingsPeriod */
        $readingsPeriod = $this->getPeriodForReadings(DateTimeImmutable::createFromMutable($onDate));
        $filename = sprintf('%s-%s.json', $this->customer->getContractNo(), $readingsPeriod->format('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf('/../storage/readings/%s/', $readingsPeriod->format('Ym'));

        // load
        if (!file_exists($dir . $filename)) {
            return null;
        }
        $readingsText = file_get_contents($dir . $filename);
        try {
            //  JSON_THROW_ON_ERROR @since 7.3
            //$readingsData = json_decode($readingsText, true, 512, JSON_THROW_ON_ERROR);
            $readingsData = json_decode($readingsText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // $this->logger->error(json_last_error_msg());
                return null;
            }
        } catch (JsonException $e) {
            //  JSON_THROW_ON_ERROR @since 7.3
            $this->logger->error($e->getMessage());
            return null;
        }
        return $readingsData;
    }

    /**
     * возвращает последений день месяца за который вносятся показания либо null если переданная дата не попадает в период
     * @param $date
     * @return DateTimeImmutable
     */
    private function getPeriodForReadings(DateTimeImmutable $date): DateTimeImmutable
    {
        /** @var bool признак перехода через месяц */
        $crossMonth = ($this->readingsPeriodFirstDay > $this->readingsPeriodLastDay);
        $day = (int)$date->format('d');
        // если лимиты дат передачи показаний в разных месяцах
        if ($day >= $this->readingsPeriodFirstDay && $crossMonth) {
            // текущий месяц
            return $date->modify('last day of this month');
        }
        if ($day <= $this->readingsPeriodLastDay && $crossMonth) {
            // прошлый месяц
            return $date->modify('last day of last month');
        }
        // если лимиты дат передачи показаний в одном месяце
        if ($day >= $this->readingsPeriodFirstDay && $day <= $this->readingsPeriodLastDay && !$crossMonth) {
            return $date->modify('last day of this month');
        }
        //todo непонятно, правильно ли вовращать текущий месяц если ничего не подошло
        return $date->modify('last day of this month');
    }

    private function isPeriodOpenedOnDate(DateTimeImmutable $date): bool
    {
        /** @var bool признак перехода через месяц */
        $crossMonth = ($this->readingsPeriodFirstDay > $this->readingsPeriodLastDay);
        $day = (int)$date->format('d');
        // если лимиты дат передачи показаний в разных месяцах
        if (($day >= $this->readingsPeriodFirstDay || $day <= $this->readingsPeriodLastDay) && $crossMonth) {
            return true;
        }
        // если лимиты дат передачи показаний в одном месяце
        if ($day >= $this->readingsPeriodFirstDay && $day <= $this->readingsPeriodLastDay && !$crossMonth) {
            return true;
        }
        return false;
    }

    /**
     * Сохранить временные показания
     * @param array $readingsData
     * @throws AppException
     */
    public function saveTmpReadings(array $readingsData): void
    {
        // load settings
        $filename = sprintf('%s.json', $this->customer->getContractNo());
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/../storage/readings-tmp/';
        // save
        // todo проверить, возможно заменить //ignore на //translit
        $str = json_encode($readingsData);
        try {
            file_put_contents($dir . $filename, $str);
        } catch (Exception $e) {
            throw new AppException('не удалось сохранить данные, обратитесь к администратору');
        }
    }

    /**
     * Загрузить временные показания
     * @return array|null
     */
    public function loadTmpReadings(): ?array
    {
        // load settings
        $filename = sprintf('%s.json', $this->customer->getContractNo());
        // todo взять путь из настроек
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/../storage/readings-tmp/';
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
        /** @var DateTimeImmutable */
        $readingsPeriod = $this->getPeriodForReadings(new DateTimeImmutable('now'));
        $filename = sprintf('%s-%s.json', $this->customer->getContractNo(), $readingsPeriod->format('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf('/../storage/readings/%s/', $readingsPeriod->format('Ym'));
        // save
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
//                $this->logger->error('Directory {dir} was not created', ['dir' => $dir]);
            throw new RuntimeException(sprintf('Папка "%s" не была создана', $dir));
        }
        file_put_contents($dir . $filename, json_encode($readings));
    }

    private function saveToFileCsv(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {
        // load settings
        /** @var DateTimeImmutable */
        $readingsPeriod = $this->getPeriodForReadings(new DateTimeImmutable('now'));

        $filename = sprintf('%s-%s.txt', $this->customer->getContractNo(), $readingsPeriod->format('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf('/../storage/readings-csv/%s/', $readingsPeriod->format('Ym'));
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
//                $this->logger->error('Directory {dir} was not created', ['dir' => $dir]);
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        $csvFileHandler = fopen($dir . $filename, 'w');
        foreach ($readings as $counterNo => $reading) {
            fwrite($csvFileHandler, $counterNo . '~' . $reading . PHP_EOL);
        }
        fclose($csvFileHandler);
    }

    private function emailToCustomer(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {
        // load settings
        $readingsPeriod = $this->getPeriodForReadings(new DateTimeImmutable('now'));
        // customer
        $recipientEmail = $this->customer->getEmail();
        if (empty($recipientEmail)) {
            throw new \InvalidArgumentException('Не указан email для отправки');
        }
        $subject = sprintf('показания за потребленную электроэнергию за %s', $readingsPeriod->format('m.Y'));
        $body = $this->composeReadingsEmail($contract, $abonentsList, $connectionsList, $readings);
        try {
            (new PhpMailerEmailSender)->send($recipientEmail, $subject, $body);
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
        usort($abonentsList, function (AbonentInfoDto $a, AbonentInfoDto $b) {
            return $a->abonentNo - $b->abonentNo;
        });
        usort($connectionsList, function (ConnectionDto $a, ConnectionDto $b) {
            return $a->abonentNo - $b->abonentNo;
        });

        /** @var AbonentInfoDto $abonent */
        foreach ($abonentsList as $abonent) {
            $readingsTxt[] = sprintf('абонент %s - %s', $abonent->abonentNo, $abonent->name);
            /** @var ConnectionDto $connection */
            foreach ($connectionsList as $connection) {
                if ($connection->abonentNo != $abonent->abonentNo) {
                    continue;
                }
                // приводим счетчик к тому же виду что и при заполнении (readings.twig)
                $counterNo = TwigTextHelper::counterCode($connection->contractNo, $connection->abonentNo, $connection->counter->serialNo);
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

    private function emailToManager(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {
        // load settings
        // manager
        $managerAmperPassword = $contract['pw'];
        $recipient = (new InMemoryManagerRepository())->findByPwd($managerAmperPassword);
        if (empty($recipient) && $recipient['email'] ?? false) {
            exit;
        }
        $subject = sprintf('[lkprom] %s - %s за %s', $contract['kp'], $contract['np1'], $this->getPeriodForReadings(new DateTimeImmutable('now'))->format('m.Y'));
        $body = $this->composeReadingsEmail($contract, $abonentsList, $connectionsList, $readings);
        try {
            (new PhpMailerEmailSender)->send($recipient['email'], $subject, $body);
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

    private function sendToFtp(array $contract, array $abonentsList, array $connectionsList, array $readings)
    {

    }
}