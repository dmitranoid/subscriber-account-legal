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
    protected function getTableName(): string
    {
        return $this->tableName;
    }

    protected function getAllSql()
    {
        return $this->fpdo
            ->from($this->getTableName())
            ->select('*');
    }

    public function getAll():array
    {
        return $this
            ->getAllSql()
            ->fetchAll();
    }

    public function getById($id):array
    {
        $data = $this->getAllSql()
            ->where($this->identity, $id)
            ->fetch();
        return $data;
    }

}