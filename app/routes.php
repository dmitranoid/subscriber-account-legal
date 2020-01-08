<?php
declare(strict_types=1);

use App\Application\Actions\Admin\User\DoAddUserAction;
use App\Application\Actions\Admin\User\ListUsersAction;
use App\Application\Actions\Admin\User\ViewUserAction;
use App\Application\Actions\Auth\DoLoginAction;
use App\Application\Actions\Auth\DoLogoutAction;
use App\Application\Actions\Auth\ShowLoginAction;
use App\Application\Actions\Connection\SendReadingsAction;
use App\Application\Actions\Contract\ViewContractDocsAction;
use App\Application\Actions\Contract\ViewContractPaymentsAction;
use App\Application\Actions\Contract\ViewContractInfoAction;
use App\Application\Actions\Connection\ListConnectionsAction;
use App\Application\Actions\Connection\ViewConnectionAction;
use App\Application\Actions\Connection\ListReadingsAction;
use App\Application\Actions\Contract\ViewDocAction;
use App\Application\Middleware\CustomerGuardedMiddleware;
use App\Application\Middleware\ManagerGuardedMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/', function (Request $request, Response $response) use($container, $app) {
        // перебрасываем из корня в зависимости от группы
        $authService = new \App\Application\Service\Auth\UserAuthService(
            $container->get(\Psr\Log\LoggerInterface::class),
            $container->get(\PDO::class),
            $request->getAttribute('session', [])
        );
        if(!$authService->isUserAuthenticated()) {
            return $response->withHeader('Location', '/login');
        }
        if ($authService->isCustomer()) {
            return $response->withHeader('Location', '/contract');
        }
        if ($authService->isManager()) {
            return $response->withHeader('Location', '/admin/users');
        }
    });

 //   $app->get('/contractslist', ViewContractsListAction::class);
    $app->get('/login', ShowLoginAction::class)->setname('login');
    $app->post('/login', DoLoginAction::class);
    $app->get('/logout', DoLogoutAction::class)->setname('logout');

    $app->group('/', function (Group $group) use ($container) {
        $group->get('contract', ViewContractInfoAction::class);
        $group->get('connections', ListConnectionsAction::class);
        $group->get('connection/{abtu}', ViewConnectionAction::class);
        $group->get('sendreadings', ListReadingsAction::class);
        $group->post('sendreadings', SendReadingsAction::class);
        $group->get('payments', ViewContractPaymentsAction::class);
        $group->get('docs', ViewContractDocsAction::class);
        $group->get('viewdoc', ViewDocAction::class);
 //       $group->get('chat', ViewChatAction::class);
    })->add(CustomerGuardedMiddleware::class);

// admin panel
    $app->group('/admin', function (Group $group) use ($container) {
        $group->get('/users', ListUsersAction::class);
        $group->get('/user{/login}', ViewUserAction::class);
        $group->post('/user{/login}', DoAddUserAction::class);
    })->add(ManagerGuardedMiddleware::class);

/*
    $app->group('/users', function (Group $group) use ($container) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
*/
};
