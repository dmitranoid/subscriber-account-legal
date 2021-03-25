<?php
declare(strict_types=1);

use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Document\DocumentServiceInterface;
use App\Application\Service\Finance\FinanceServiceInterface;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Persistence\Account\SqlAccountRepository;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use App\Regionally\Bares\Application\Service\Abonent\AbonentService;
use App\Regionally\Bares\Application\Service\Connection\ConnectionService;
use App\Regionally\Bares\Application\Service\Contract\ContractInfoService;
use App\Regionally\Bares\Application\Service\Document\DocumentService;
use App\Regionally\Bares\Application\Service\Finance\FinanceService;
use DI\ContainerBuilder;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
// repositories
            AccountRepositoryInterface::class => DI\autowire(SqlAccountRepository::class),
// services
            ContractInfoServiceInterface::class => DI\autowire(ContractInfoService::class),
            ConnectionServiceInterface::class => DI\autowire(ConnectionService::class),
            AbonentServiceInterface::class => DI\autowire(AbonentService::class),
            FinanceServiceInterface::class => DI\autowire(FinanceService::class),
            AuthService::class => DI\autowire(AuthService::class),
            DocumentServiceInterface::class => DI\factory(function (array $settings) {
                // TODO брать путь к документам из настроек
                //$documentsStorageRoot = 'F:/lkprom-docs/';  // под windows буква диска с большой буквы !!!
//                $documentsStorageRoot = 'D:/work/legal-docs/';  // под windows буква диска с большой буквы !!!
                $documentsStorageRoot = $settings['documentsStorageRoot'];
                return new DocumentService(
                    new DocumentFinder($documentsStorageRoot),
                    $documentsStorageRoot
                );
            })->parameter('settings', DI\get('settings')),
        ]
    );
};
