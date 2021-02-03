<?php
declare(strict_types=1);

use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Regionally\Bares\Application\Service\Abonent\AbonentService;
use App\Regionally\Bares\Application\Service\Connection\ConnectionService;
use App\Regionally\Bares\Application\Service\Contract\ContractInfoService;
use DI\ContainerBuilder;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(array(
        ContractInfoServiceInterface::class => DI\autowire(ContractInfoService::class),
        ConnectionServiceInterface::class => DI\autowire(ConnectionService::class),
        AbonentServiceInterface::class => DI\autowire(AbonentService::class),
    ));
};
