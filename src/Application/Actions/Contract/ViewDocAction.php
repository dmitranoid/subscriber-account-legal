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
        // TODO тщательно очищать ввод !!!
        $params = $this->request->getQueryParams();
        $contractNo = $_SESSION['contract'];
        $filename = $params['filename'];
        // TODO брать путь к документам из настроек
        $documentsStorageRoot = 'D:/work/legal-docs/';

        $filenameFull = $documentsStorageRoot . $filename;

        if (!$this->accessToFileIsAllowed($contractNo, $params['filename']) || !file_exists($filenameFull)) {
            // TODO возвращать 404 not found
            $this->logger->alert(
                'попытка доступа к запрещенному файлу filename от абонента contractNo',
                ['filename' => $filename, 'contractNo' => $contractNo]
            );
            return $this->view->render(
                $this->response,
                'misc/docview.twig',
                [
                    'content' => 'файл не найден',
                ]
            );
        }
        $rawContent = file_get_contents($filenameFull);
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

    private function accessToFileIsAllowed($contractNo, $filename)
    {
        // $filename = D:\work\legal-docs\2019\09-2019\802_счет.888 ; $contractNo= 802
        // $filename = D:\work\legal-docs\2019\09-2019\802_ндс.888 ; $contractNo= 802
        return (false !== strpos($filename, '\\' . $contractNo . '_'));
    }

}