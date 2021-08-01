<?php
declare(strict_types=1);


namespace App\Application\Middleware;

use App\Application\Service\Auth\AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class ManagerGuardedMiddleware implements MiddlewareInterface
{


    private LoggerInterface $logger;

    private AuthService $authService;

    public function __construct(LoggerInterface $logger, AuthService $authService)
    {
        $this->logger = $logger;
        $this->authService = $authService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $account = $this->authService->getCurrentAccount();
        if (!$this->authService->isAccountAuthenticated()) {
            return (new Response())->withHeader('Location', '/login');
        }

        if (!($account->isManager() || $account->isAdmin())) {
            $resp = new Response();
            $resp->getBody()->write('Доступ запрещён');
            return $resp->withStatus(403);
        }

        return $handler->handle($request);
    }

}