<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Domain\Contract\ContractRepository;
use App\Infrastructure\Persistence\User\InMemoryManagerRepository;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => DI\autowire(InMemoryManagerRepository::class),
        ContractRepository::class => DI\autowire(SqlContractRepository::class),

    ]);
};
