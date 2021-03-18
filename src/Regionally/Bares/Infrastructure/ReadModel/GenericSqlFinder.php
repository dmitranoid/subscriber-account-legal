<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\ReadModel;


use Envms\FluentPDO\Queries\Select;
use Envms\FluentPDO\Query;
use PDO;

class GenericSqlFinder
{
    protected Query $fpdo;

    protected string $tableName = '';

    /** @var string|array */
    protected $identity = 'id';

    protected array $allowedFields = [];

    public function __construct(PDO $pdo)
    {
        $this->fpdo = new Query($pdo);
    }

    protected function getTableName(): string
    {
        return $this->tableName;
    }

    protected function getAllSql(): Select
    {
        return $this->fpdo
            ->from($this->getTableName())
            ->select('*');
    }

    public function getAll(): ?array
    {
        return $this
            ->getAllSql()
            ->fetchAll();
    }

    public function getById($id): ?array
    {
        $data = $this->getAllSql()
            ->where($this->identity, $id)
            ->fetch();
        if (!$data) {
            return null;
        }
        return $data;
    }


    private function getAllowedFields()
    {
        if (!empty($this->allowedFields)) {
            return $this->allowedFields;
        }
        //todo cache table schema data
        $tableSchema = $this->fpdo->getPdo()
            ->query("PRAGMA table_info(" . $this->getTableName() . ")")
            ->fetchAll(PDO::FETCH_ASSOC);
        $allowedFields = [];
        foreach ($tableSchema as $item) {
            $allowedFields[] = $item['name'];
        }
        return $allowedFields;
    }

}