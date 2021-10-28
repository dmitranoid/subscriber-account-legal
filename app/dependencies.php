<?php
declare(strict_types=1);

use App\Application\Service\Auth\AuthService;
use App\Application\Service\Readings\ReadingsService;
use App\Application\Service\User\UserService;
use App\Application\Service\User\UserServiceInterface;
use App\Application\ServiceProviders\MonologServiceProvider;
use App\Application\ServiceProviders\PdoAdministrativeServiceProvider;
use App\Application\ServiceProviders\TwigServiceProvider;
use App\Application\View\TwigView;
use App\Application\View\ViewInterface;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Persistence\Account\SqlAccountRepository;
use App\Infrastructure\Service\Session\SessionInterface;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => DI\factory([MonologServiceProvider::class, 'register']),
        SessionInterface::class => DI\create(App\Infrastructure\Service\Session\Session::class),
        Twig::class => DI\factory([TwigServiceProvider::class, 'register']),
        ViewInterface::class => DI\create(TwigView::class),
        'PdoAdministrative' => DI\factory([PdoAdministrativeServiceProvider::class, 'register']),
        Slim\Flash\Messages::class => DI\create(Slim\Flash\Messages::class),
        // app services
        AuthService::class => DI\create(AuthService::class),
        ReadingsService::class => DI\autowire(ReadingsService::class),
        UserServiceInterface::class => DI\autowire(UserService::class),
        // repositories
        AccountRepositoryInterface::class => DI\autowire(SqlAccountRepository::class)
            ->constructorParameter(PDO::class, DI\get('PdoAdministrative')),
    ]);
};
