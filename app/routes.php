<?php
declare(strict_types=1);

use App\Application\Actions\Auth\DoLoginAction;
use App\Application\Actions\Auth\DoLogoutAction;
use App\Application\Actions\Auth\ShowLoginAction;
use App\Application\Actions\Contract\SendReadingsAction as SendReadingsActionAlias;
use App\Application\Actions\Contract\ViewContractDocsAction;
use App\Application\Actions\Contract\ViewContractPaymentsAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Contract\ViewContractInfoAction;
use App\Application\Actions\Connection\ListConnectionsAction;
use App\Application\Actions\Connection\ViewConnectionAction;
use App\Application\Actions\Connection\ListReadingsAction;
use App\Application\Middleware\GuardedMiddleware;
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
    $app->post('/login', DoLoginAction::class);
    $app->get('/logout', DoLogoutAction::class)->setname('logout')
        ->add(GuardedMiddleware::class);
    $app->group('/users', function (Group $group) use ($container) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/contract', function (Group $group) use ($container) {
        $group->get('/info', ViewContractInfoAction::class);
        $group->get('/connections', ListConnectionsAction::class);
        $group->get('/connection/{abtu}', ViewConnectionAction::class);
        $group->get('/sendreadings', ListReadingsAction::class);
        $group->post('/sendreadings', SendReadingsActionAlias::class);
        $group->get('/payments', ViewContractPaymentsAction::class);
        $group->get('/docs', ViewContractDocsAction::class);
    })->add(GuardedMiddleware::class);
};
