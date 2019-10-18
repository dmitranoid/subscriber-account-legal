<?php
declare(strict_types=1);

use App\Application\Actions\Auth\ShowLoginAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Contract\ViewContractInfoAction;
use App\Application\Actions\Contract\ListConnectionsAction;
use App\Application\Actions\Contract\ViewConnectionAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/', function (Request $request, Response $response) {
        $response = $response->withHeader('Location', '/contract/info');
        return $response;
    });

    $app->get('/login', ShowLoginAction::class)->setname('login');
    $app->post('/login', \App\Application\Actions\Auth\DoLoginAction::class);
    $app->get('/logout', \App\Application\Actions\Auth\DoLogoutAction::class);

    $app->group('/users', function (Group $group) use ($container) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/contract', function (Group $group) use ($container) {
        $group->get('/info', ViewContractInfoAction::class);
        $group->get('/connections', ListConnectionsAction::class);
        $group->get('/connection/{abtu}', ViewConnectionAction::class);
        $group->get('/sendreadings', \App\Application\Actions\Contract\ListReadingsAction::class);
        $group->post('/sendreadings', \App\Application\Actions\Contract\SendReadingsAction::class);
        $group->get('/payments', \App\Application\Actions\Contract\ViewContractPaymentsAction::class);
        $group->get('/docs', \App\Application\Actions\Contract\ViewContractDocsAction::class);
    })->add(\App\Application\Middleware\GuardedMiddleware::class);
};
