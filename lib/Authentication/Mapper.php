<?php 

declare(strict_types=1);

namespace bearonahill\Authentication;

use \PDO;
use \bearonahill\Helper\PDOHelper;

class Mapper
{
    private $table;
    private $userColumn;
    private $passwordColumn;
    private $pdo;

    public function __construct(PDO $pdo, $table, $userColumn, $passwordColumn)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->table = $table;
        $this->userColumn = $userColumn;
        $this->passwordColumn = $passwordColumn;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getUserColumn()
    {
        return $this->userColumn;
    }

    public function getPasswordColumn()
    {
        return $this->passwordColumn;
    }

    public function getDatabase()
    {
        return $this->pdo;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getSQLColumns()
    {
        $list = $this->getListOfColumns();
        return implode(',', PDOHelper::backtickItems($list));
    }

    public function getSQLValues()
    {
        $list = $this->getListOfColumns();
        return implode(',', PDOHelper::prependPlaceholder($list));
    }

    private function getListOfColumns():array
    {
        return array_merge(
            [$this->userColumn, $this->passwordColumn],
            array_keys($this->user->getProperties())
        );
    }

    public function getValues()
    {
        $list = array_merge(
            [
                $this->userColumn => $this->user->getLogin(), 
                $this->passwordColumn => $this->user->getPassword()
            ],
            $this->user->getProperties()
        );
        return $this->sqlKeys($list);
    }

    private function sqlKeys(array $list)
    {
        foreach ($list as $key => $value) {
            unset($list[$key]);
            $list[':'.$key] = $value;
        }
        return $list;        
    }

    public function getSqlCredentials()
    {
        return $this->sqlKeys([
            $this->userColumn => $this->user->getLogin(), 
            $this->passwordColumn => $this->user->getPassword()
        ]);
    }

}