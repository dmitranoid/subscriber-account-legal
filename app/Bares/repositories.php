<?php
declare(strict_types=1);

use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Document\DocumentServiceInterface;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use App\Regionally\Bares\Application\Service\Abonent\AbonentService;
use App\Regionally\Bares\Application\Service\Connection\ConnectionService;
use App\Regionally\Bares\Application\Service\Contract\ContractInfoService;
use App\Regionally\Bares\Application\Service\Document\DocumentService;
use DI\ContainerBuilder;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
            ContractInfoServiceInterface::class => DI\autowire(ContractInfoService::class),
            ConnectionServiceInterface::class => DI\autowire(ConnectionService::class),
            AbonentServiceInterface::class => DI\autowire(AbonentService::class),
            DocumentServiceInterface::class => function () {
                // TODO брать путь к документам из настроек
                $documentsStorageRoot = 'F:/lkprom-docs/';  // под windows буква диска с большой буквы !!!
                //$documentsStorageRoot = 'D:/work/legal-docs/';  // под windows буква диска с большой буквы !!!
                return new DocumentService(
                    new DocumentFinder($documentsStorageRoot),
                    $documentsStorageRoot
                );
            },
        ]
    );
};
