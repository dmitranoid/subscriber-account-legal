<?php
declare(strict_types=1);


namespace App\Infrastructure\Service\DocumentFinder;


use DateTime;
use Symfony\Component\Finder\Finder;

final class DocumentFinder
{
    private $docsRootPath;

    public function __construct($docsRootPath)
    {
        $this->docsRootPath = $docsRootPath;
    }

    public function getDocsByContract($contractNo, DateTime $periodStart, DateTime $periodEnd):?array
    {
        $docsList = [];
        $filenamePattern = '' . $contractNo . '_*.*';
        $finder = new Finder();
        $finder->files()->name($filenamePattern)->in($this->docsRootPath);
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                // TODO специфично разбираем путь, может поломаться в любой момент
                $path = explode(DIRECTORY_SEPARATOR, $file->getRelativePath());
                $doc = [
                    'datePath' => end($path),
                    'fullPath' => $file->getRealPath(),
                    'filename' => $file->getFilename(),
                    'modifyDate' => new DateTime('@' . $file->getMTime()),
                ];
                $docsList[] = $doc;
            }
        }
        return $docsList;
    }


}