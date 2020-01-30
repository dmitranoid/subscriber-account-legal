<?php
declare(strict_types=1);


namespace App\Application\Middleware;


use App\Application\View\TwigFash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use TheSeer\Tokenizer\Exception;

class TwigFlashMessageMiddleware implements MiddlewareInterface
{

    /** @var Messages  */
    private $flash;
    /**
     * @var Twig
     */
    private $twig;

    public function __construct(Twig $twig, Messages $flash)
    {
        $this->flash = $flash;
        $this->twig = $twig;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->twig->getEnvironment()->addGlobal('flash', new TwigFash($this->flash->getMessages()));
        $this->flash->clearMessages();

        $response = $handler->handle($request);
        return $response;
    }

}