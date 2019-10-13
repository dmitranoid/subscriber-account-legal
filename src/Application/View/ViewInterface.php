<?php


namespace App\Application\View;


use Psr\Http\Message\ResponseInterface as Response;

interface ViewInterface
{
    public function render(Response $response, $template, $data):Response;
}