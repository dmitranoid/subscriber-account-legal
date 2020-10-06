<?php
declare(strict_types=1);

use App\Application\Actions\Admin\User\DeleteUserAction;
use App\Application\Actions\Admin\User\EditUserAction;
use App\Application\Actions\Admin\User\NewUserAction;
use App\Application\Actions\Admin\User\ViewUserAction;
use App\Application\Actions\Admin\User\ViewUsersListAction;
use App\Application\Actions\Auth\LoginAction;
use App\Application\Actions\Auth\LogoutAction;
use App\Application\Actions\Auth\ShowLoginAction;
use App\Application\Actions\Connection\SaveReadingsAction;
use App\Application\Actions\Connection\SendReadingsAction;
use App\Application\Actions\Connection\ViewConnectionAction;
use App\Application\Actions\Connection\ViewConnectionsListAction;
use App\Application\Actions\Connection\ViewListReadingsAction;
use App\Application\Actions\Contract\ViewContractDocsAction;
use App\Application\Actions\Contract\ViewContractInfoAction;
use App\Application\Actions\Contract\ViewContractPaymentsAction;
use App\Application\Actions\Contract\ViewDocAction;
use App\Application\Middleware\CustomerGuardedMiddleware;
use App\Application\Middleware\ManagerGuardedMiddleware;
use App\Application\Service\Auth\UserAuthService;
use App\Infrastructure\Service\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    /** @var ContainerInterface $container */
    $container = $app->getContainer();
    $settings = $container->get('settings');
    $routePrefix = '';
    $session = $container->get(SessionInterface::class);
    $app->group($routePrefix, function (Group $group) use ($container, $app, $session) {
        $group->get('/', function (Request $request, Response $response) use ($container, $app, $session) {
            // перебрасываем из корня в зависимости от группы
            $authService = new UserAuthService(
                $container->get(LoggerInterface::class),
                $container->get(PDO::class),
                $session
            );
            if (!$authService->isUserAuthenticated()) {
                return $response->withHeader('Location', '/login');
            }
            if ($authService->isCustomer()) {
                return $response->withHeader('Location', '/contract');
            }
            if ($authService->isManager() || $authService->isAdmin()) {
                return $response->withHeader('Location', '/admin/users');
            }
        });
        $group->get('/login', ShowLoginAction::class)->setname('login');
        $group->post('/login', LoginAction::class);
        $group->any('/logout', LogoutAction::class)->setname('logout');

        $group->group('/', function (Group $group) use ($container) {
            $group->get('contract', ViewContractInfoAction::class)->setName('front.contract');
            $group->get('connections', ViewConnectionsListAction::class)->setName('front.conections');
            $group->get('connection/{abtu}', ViewConnectionAction::class)->setName('front.connection');
            $group->get('sendreadings', ViewListReadingsAction::class)->setName('front.sendreadings');
            $group->post('sendreadings', SendReadingsAction::class);
            $group->post('savereadings', SaveReadingsAction::class);

            $group->get('payments', ViewContractPaymentsAction::class)->setName('front.payments');
            $group->get('docs', ViewContractDocsAction::class)->setName('front.docs');
            $group->get('viewdoc', ViewDocAction::class)->setName('viewdoc');
            //       $group->get('chat', ViewChatAction::class);
        })->add(CustomerGuardedMiddleware::class);

// admin panel
        $group->group('/admin', function (Group $group) use ($container) {
            $group->get('/users', ViewUsersListAction::class)->setName('admin.users');
            $group->get('/user[/{login}]', ViewUserAction::class)->setName('admin.user.show');
            $group->post('/user/{login}', EditUserAction::class)->setName('admin.user.edit');
            $group->post('/user', NewUserAction::class)->setName('admin.user.create');
            //fixme delete -> post only !!!!
            $group->any('/user/{login}/delete', DeleteUserAction::class)->setName('admin.user.delete');
//        $group->post('/user/{login}', DoEditUserAction::class);
        })->add(ManagerGuardedMiddleware::class);

        /*
            $app->group('/users', function (Group $group) use ($container) {
                $group->get('', ListUsersAction::class);
                $group->get('/{id}', ViewUserAction::class);
            });
        */
    });
};
