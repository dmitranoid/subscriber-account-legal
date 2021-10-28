<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Document\DocumentServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewDocAction extends Action
{
    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $contractNo = $auth->getCurrentAccount()->getContractNo();

        if (empty($contractNo)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }
        $params = $this->request->getQueryParams();

        // TODO тщательно очищать ввод !!!
        // TODO защиту от инъекций типа ../../../etc/passw
        $filename = $params['filename'];

        /** @var DocumentServiceInterface $docsService */
        $docsService = AppHelper::container()->get(DocumentServiceInterface::class);

        if (!$docsService->isAccessToFileAllowedForAccount($auth->getCurrentAccount(), $filename)) {
            // TODO возвращать 404 not found
            $this->logger->alert(
                'попытка доступа к запрещенному файлу {filename} от абонента {contractNo}',
                ['filename' => $filename, 'contractNo' => $contractNo]
            );
            return $this->view->render(
                $this->response,
                'front/misc/docview.twig',
                [
                    'content' => 'файл не найден',
                ]
            );
        }
        $content = $docsService->getDocumentContent($filename);
        $content = nl2br($content, false);
        return $this->view->render(
            $this->response,
            'front/misc/docview.twig',
            [
                'content' => $content,
            ]
        );
    }

}