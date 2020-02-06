<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence;


use App\Application\Exceptions\AppDbException;
use App\Application\Helpers\ArrayHelper;
use Envms\FluentPDO\Exception as FpdoException;
use Envms\FluentPDO\Query;
use \PDO;
use phpDocumentor\Reflection\Types\Integer;

class GenericSqlRepository
{
    /** @var Query */
    protected $fpdo;

    protected $tableName = '';

    protected $identity='id';

    protected $allowedFields = [];

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

    public function getAll():?array
    {
        return $this
            ->getAllSql()
            ->fetchAll();
    }

    public function getById($id):?array
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
    public function save($data)
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

    /**
     * @param $data
     * @throws AppDbException
     */
    public function update($data)
    {
        $id = $data[$this->identity];
        unset($data[$this->identity]);
        $data = ArrayHelper::filterArrayKeys($data, $this->getAllowedFields());
        try {
            $this->fpdo->update($this->tableName, $data, $data['login'])->execute();
        } catch (FpdoException $e) {
            throw new AppDbException('Ошибка обновления данных в ' . $this->tableName, $e);
        }
    }

    /**
     * @param int $id
     * @throws AppDbException
     */
    public function delete(int $id)
    {
        try {
            $this->fpdo->delete($this->tableName, $id)->execute();
        } catch (FpdoException $e) {
            throw new AppDbException('Ошибка удаления данных в ' . $this->tableName, $e);
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

}