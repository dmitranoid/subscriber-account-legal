<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractDocsAction extends Action
{
    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractRepository = new FoxproContractRepository();
        $contractInfo = $contractRepository->getByNomArr($contractNo);

        // date, period, doctype, docno, sumInv, sumPay
        $docList = [];
        $docs = [];

        foreach ($docs as $doc) {
            $paydocs[] = [
                'date' => '',
                'period' => '',
                'docName' => '',
                'docPath' => '',
            ];
        }

        $docList[] = [
            'date' => '2019/10/01',
            'period' => '2019/09',
            'docName' => 'сч/ф Энергия',
            'docPath' => '/test',
        ];

        return $this->view->render(
            $this->response,
            'contract/docs.twig',
            [
                'contract' => $contractInfo, 
                'docList' => $docList
            ]
        );
    }

}
