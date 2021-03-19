<?php
declare(strict_types=1);


namespace App\Application\View;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class TwigView implements ViewInterface
{
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function render(ResponseInterface $response, $template, $data): Response
    {
        return $this->twig->render($response, $template, $data);
    }

}