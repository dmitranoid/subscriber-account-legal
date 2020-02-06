<?php
declare(strict_types=1);


namespace App\Application\Middleware;


use App\Application\Service\Auth\UserAuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class ManagerGuardedMiddleware implements MiddlewareInterface
{

    /**
     * @var \PDO
     */
    private $pdo;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, \PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authService = new UserAuthService($this->logger, $this->pdo, $request->getAttribute('session') ?? []);

        if (!$authService->isUserAuthenticated()) {
            return (new Response())->withHeader('Location', '/login');
        } else {
            if (!($authService->isManager() || $authService->isAdmin())) {
                $resp = new Response();
                $resp->getBody()->write('Доступ запрещён');
                return $resp->withStatus(403);
            }
        }

        return $handler->handle($request);
    }

}