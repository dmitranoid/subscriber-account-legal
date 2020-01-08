<?php


namespace App\Application\View;


use Psr\Http\Message\ResponseInterface as Response;

interface ViewInterface
{
    /**
     * @param Response $response
     * @param $template
     * @param $data
     * @return Response
     */
    public function render(Response $response, $template, $data):Response;
}