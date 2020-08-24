<?php
declare(strict_types=1);


namespace App\Application\Middleware;


use App\Application\Service\Auth\UserAuthService;
use App\Infrastructure\Service\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class CustomerGuardedMiddleware implements MiddlewareInterface
{
    private \PDO $pdo;

    private LoggerInterface $logger;

    private SessionInterface $session;

    public function __construct(LoggerInterface $logger, \PDO $pdo, SessionInterface $session)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->session = $session;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authService = new UserAuthService($this->logger, $this->pdo, $this->session);

        if ($authService->isUserAuthenticated()) {
            if (!$authService->isCustomer()) {
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