<?php
declare(strict_types=1);

use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Document\DocumentServiceInterface;
use App\Application\Service\Finance\FinanceServiceInterface;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use App\Regionally\Bares\Application\Service\Abonent\AbonentService;
use App\Regionally\Bares\Application\Service\Connection\ConnectionService;
use App\Regionally\Bares\Application\Service\Contract\ContractInfoService;
use App\Regionally\Bares\Application\Service\Document\CachedDocumentService;
use App\Regionally\Bares\Application\Service\Document\DocumentService;
use App\Regionally\Bares\Application\Service\Finance\FinanceService;
use App\Regionally\Bares\Application\ServiceProviders\PdoPromProvider;
use DI\ContainerBuilder;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(array(
            'PdoBarProm' => DI\factory(array(PdoPromProvider::class, 'register')),
// services
            ContractInfoServiceInterface::class => DI\autowire(ContractInfoService::class)
                ->constructorParameter(PDO::class, DI\get('PdoBarProm')),
            ConnectionServiceInterface::class => DI\autowire(ConnectionService::class)
                ->constructorParameter(PDO::class, DI\get('PdoBarProm')),
            AbonentServiceInterface::class => DI\autowire(AbonentService::class)
                ->constructorParameter(PDO::class, DI\get('PdoBarProm')),
            FinanceServiceInterface::class => DI\autowire(FinanceService::class)
                ->constructorParameter(PDO::class, DI\get('PdoBarProm')),
            DocumentServiceInterface::class => DI\factory(function (array $settings) {
                $documentsStorageRoot = $settings['documentsStorageRoot'];
                $docsListCache = $settings['docsListCache'];
                return new CachedDocumentService(
                    new \App\Infrastructure\Service\Cache\FileCache($docsListCache),
                    new DocumentService(
                        new DocumentFinder($documentsStorageRoot),
                        $documentsStorageRoot
                    )
                );
            })->parameter('settings', DI\get('localSettings')),
        )
    );
};
