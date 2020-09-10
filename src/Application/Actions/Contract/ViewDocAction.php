<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ViewDocAction extends Action
{
    protected function action(): Response
    {
        // TODO тщательно очищать ввод !!!
        // TODO защиту от инъекций типа ../../../etc/passw
        $auth = new UserAuthService($this->logger, $this->pdo, new Session());
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }
        $params = $this->request->getQueryParams();
        $filename = $params['filename'];
        // TODO брать путь к документам из настроек
        $documentsStorageRoot = 'f:/lkprom-docs/';

        $filenameFull = $documentsStorageRoot . $filename;
        if (!($this->isAccessToFileAllowed($contractNom, $params['filename']) && file_exists($filenameFull))) {
            // TODO возвращать 404 not found
            $this->logger->alert(
                'попытка доступа к запрещенному файлу {filename} от абонента {contractNo}',
                ['filename' => $filename, 'contractNo' => $contractNom]
            );
            return $this->view->render(
                $this->response,
                'front/misc/docview.twig',
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
            'front/misc/docview.twig',
            [
                'content' => $content,
            ]
        );
    }

    private function isAccessToFileAllowed($contractNo, $filename)
    {
        // $filename = D:\work\legal-docs\2019\09-2019\802_счет.888 ; $contractNo= 802
        // $filename = D:\work\legal-docs\2019\09-2019\802_ндс.888 ; $contractNo= 802
        return (false !== strpos($filename, DIRECTORY_SEPARATOR . $contractNo . '_'));
    }

}