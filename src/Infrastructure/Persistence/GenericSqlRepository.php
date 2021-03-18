<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence;


use App\Application\Exceptions\AppDbException;
use App\Application\Helpers\ArrayHelper;
use Envms\FluentPDO\Exception as FpdoException;
use Envms\FluentPDO\Queries\Select;
use Envms\FluentPDO\Query;
use PDO;

class GenericSqlRepository
{

    protected Query $fpdo;

    protected string $tableName = '';

    protected $identity = 'id';

    protected array $allowedFields = [];

    public function __construct(PDO $pdo)
    {
        $this->fpdo = new Query($pdo);
    }

    public function getAll(): ?array
    {
        return $this
            ->getAllSql()
            ->fetchAll();
    }

    protected function getAllSql(): Select
    {
        return $this->fpdo
            ->from($this->getTableName())
            ->select('*');
    }

    protected function getTableName(): string
    {
        return $this->tableName;
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

    /**
     * @param $data
     * @throws AppDbException
     */
    public function save($data): void
    {
        {
            $data = ArrayHelper::filterArrayKeys($data, $this->getAllowedFields());
            try {
                $query = $this->fpdo->insertInto($this->tableName, $data);
                $query->execute();
            } catch (FpdoException $e) {
                throw new AppDbException('Ошибка вставки данных в ' . $this->tableName, $e);
            }
        }
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

    /**
     * @param $data
     * @throws AppDbException
     */
    public function update($data): void
    {
        $id = $data[$this->identity];
        unset($data[$this->identity]);
        $data = ArrayHelper::filterArrayKeys($data, $this->getAllowedFields());
        try {
            $this->fpdo->update($this->tableName, $data, (int)$id)->execute();
        } catch (FpdoException $e) {
            throw new AppDbException('Ошибка обновления данных в ' . $this->tableName, $e);
        }
    }

    /**
     * @param $id
     * @throws AppDbException
     */
    public function delete($id)
    {
        try {
            $this->fpdo->delete($this->tableName, $id)->execute();
        } catch (FpdoException $e) {
            throw new AppDbException('Ошибка удаления данных в ' . $this->tableName, $e);
        }
    }

}