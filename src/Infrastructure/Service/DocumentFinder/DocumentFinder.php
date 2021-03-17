<?php
declare(strict_types=1);


namespace App\Infrastructure\Service\DocumentFinder;


use DateTimeImmutable;
use Symfony\Component\Finder\Finder;

final class DocumentFinder
{
    private $docsRootPath;

    public function __construct($docsRootPath)
    {
        $this->docsRootPath = $docsRootPath;
    }

    public function getDocsByContract($contractNo, \DateTimeImmutable $periodStart, \DateTimeImmutable $periodEnd): ?array
    {
        $docsList = [];
        $filenamePattern = '' . $contractNo . '_*.*';
        $finder = new Finder();
        $finder->files()->name($filenamePattern)->in($this->docsRootPath);
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                // TODO специфично разбираем путь, может поломаться в любой момент
                $path = explode(DIRECTORY_SEPARATOR, $file->getRelativePath());
                try {

                    $doc = [
                        'datePath' => end($path),
                        'fullPath' => $file->getRealPath(),
                        'filename' => $file->getFilename(),
                        'modifyDate' => new DateTimeImmutable('@' . $file->getMTime()),
                    ];
                } catch (\Exception $e) {
                    //todo сделать логгирование ошибок
                    // $this->logger->error($e.message);
                    $doc = null;
                }
                if ($doc) {
                    $docsList[] = $doc;
                }
            }
        }
        return $docsList;
    }

}