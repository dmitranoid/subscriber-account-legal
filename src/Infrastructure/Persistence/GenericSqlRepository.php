<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence;


use App\Application\Exceptions\AppDbException;
use Envms\FluentPDO\Exception as FpdoException;
use Envms\FluentPDO\Query;
use \PDO;

class GenericSqlRepository
{
    /** @var Query */
    protected $fpdo;

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
            try {
                $this->fpdo->insertInto($this->tableName, $data)->execute();
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
        try {
            $this->fpdo->update($this->tableName, $data, $data['login'])->execute();
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