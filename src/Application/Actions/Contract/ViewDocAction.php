<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ViewDocAction extends Action
{
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        $filename = $params['filename'];
        $rawContent = file_get_contents($filename);
        // convert content;
        $content = iconv('CP1251', 'UTF-8', $rawContent);
        $content = nl2br($content, false);
        return $this->view->render(
            $this->response,
            'misc/docview.twig',
            [
                'content' => $content,
            ]
        );
    }

}