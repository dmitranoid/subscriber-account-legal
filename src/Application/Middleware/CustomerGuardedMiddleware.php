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

class CustomerGuardedMiddleware implements MiddlewareInterface
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
        if ($this->authService->isAccountAuthenticated()) {
            $account = $this->authService->getCurrentAccount();
            if (!$account->isCustomer()) {
                $response = new Response();
                $response->getBody()->write('Доступ запрещён');
                return $response->withStatus(403);
            }
        } else {
            return (new Response())->withHeader('Location', '/login');
        }
        return $handler->handle($request);
    }

}