<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence;


use Envms\FluentPDO\Query;
use \PDO;

class GenericSqlRepository
{
    /** @var Query */
    private $fpdo;

    protected $tableName = '';

    protected $identity='id';

    public function __construct(PDO $pdo)
    {
        $this->fpdo = new Query($pdo);
    }

    /**
     * @return string
     */
    private function getTableName(): string
    {
        return $this->tableName;
    }

    public function getAll():array
    {
        return $this->fpdo
            ->from($this->getTableName())
            ->select('*')
            ->fetch();
    }

    public function getById($id):array
    {
            $data = $this->fpdo
            ->from($this->getTableName())
            ->select('*')
            ->where($this->identity, $id)
            ->fetch();

        return $data;
    }

}