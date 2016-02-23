<?php 

declare(strict_types=1);

namespace bearonahill;

use bearonahill\Authentication\Mapper;
use bearonahill\Authentication\User;
use PDO;
use PDOException;

class Authentication
{
    const SESSION_KEY = 'authenticated_user';

    private $mapper;
    private $issue;

    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function register(User $user):bool
    {
        $this->mapper->setUser($user);
        $this->issue = null;
        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $this->mapper->getTable(),
            $this->mapper->getSQLColumns(),
            $this->mapper->getSQLValues()
        );

        try {
            $query = $this->mapper->getDatabase()->prepare($sql);            
            $query->execute($this->mapper->getValues());
        } catch (PDOException $e) {
            $this->issue = $e;
            return false;
        }
        return true;
    }

    public function getIssue():PDOException
    {
        return $this->issue;
    }

    private function init()
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
    }

    public function login(User $user):bool
    {
        $this->init();
        $this->mapper->setUser($user);

        $userColumn = $this->mapper->getUserColumn();
        $passwordColumn = $this->mapper->getPasswordColumn();

        $sql = sprintf(
            "SELECT %s FROM `%s` WHERE `%s`=:%s",
            $this->mapper->getSQLColumns(),
            $this->mapper->getTable(),
            $userColumn,
            $userColumn
        );

        $query = $this->mapper->getDatabase()->prepare($sql);
        $query->execute([':'.$userColumn => $user->getLogin()]);

        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!isset($result[$passwordColumn])) {
            return false;
        }

        $dbUser = $this->getUserFromArray($result);
        $verified = $user->verify($dbUser);
        if ($verified) {
            $this->setSession($result);
        }
        return $verified;
    }

    public function getUserFromArray(array $user):User
    {
        $userColumn = $this->mapper->getUserColumn();
        $passwordColumn = $this->mapper->getPasswordColumn();

        if (!isset($user[$userColumn]) || !isset($user[$passwordColumn])) {
            return new User();
        }

        $dbUser = new User($user[$userColumn]);
        $dbUser->setHash($user[$passwordColumn]);

        return $dbUser;
    }

    private function setSession(array $user)
    {
        $this->init();
        $_SESSION[self::SESSION_KEY] = $user;
    }

    public function isValid():bool
    {
        $this->init();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        if (!is_array($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        $user = $this->getUserFromArray($_SESSION[self::SESSION_KEY]);
        return $this->login($user);
    }

    public function getUser():array
    {
        if (isset($_SESSION[self::SESSION_KEY])){
            return [];
        }
        return $_SESSION[self::SESSION_KEY];
    }
}