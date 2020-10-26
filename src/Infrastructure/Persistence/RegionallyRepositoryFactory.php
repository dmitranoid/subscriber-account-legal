<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence;


final class RegionallyRepositoryFactory
{
    private string $region;
    private string $classnameTemplate;

    public function __construct(string $region)
    {
        $this->region = $region;
        $this->classnameTemplate = 'Regionally\\%s\\Infrastructure\\Persistence\\%s';
    }

    public function get(string $name)
    {
        $classname = sprintf($this->classnameTemplate, $this->region, $name);
        if (!class_exists($classname)) {
            throw new \InvalidArgumentException('Class ' . $classname . ' not found');
        }
        return new $classname;
    }
}