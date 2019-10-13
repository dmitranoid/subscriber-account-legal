<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Domain\Contract\ContractRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => DI\autowire(InMemoryUserRepository::class),
        ContractRepository::class => DI\autowire(FoxproContractRepository::class),
    ]);
};
