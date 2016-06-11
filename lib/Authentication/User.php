<?php 

declare(strict_types=1);

namespace bearonahill\Authentication;

class User
{
    private $login;
    private $password;
    private $hash;
    private $algorithm = PASSWORD_DEFAULT;
    private $properties = [];

    public function __construct($login = null, $password = null)
    {
        $this->login = $login;
        if ($password === null) {
            return;
        }
        $this->password = $password;
        $this->hash = password_hash($password, $this->algorithm);
    }

    public function __set($var, $value)
    {
        if (!preg_match('/^[a-z0-9_]+$/i', $var)) {
            throw new \Exception('Invalid property key ([a-z0-9_]+)');
        }
        $this->properties[$var] = $value;
    }

    public function __get($var)
    {
        if (isset($this->properties[$var])) {
            return $this->properties[$var];
        }
        return $this->{$var};
    }

    public function __isset($var)
    {
        return isset($this->properties[$var]);
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->hash;
    }
                        
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function verify(User $user)
    {
        if (!isset($this->password) && isset($this->hash)) {
            if($user->getPassword() == $this->hash) {
                return true;
            }
            return false; 
        }
        return password_verify($this->password, $user->getPassword());
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getProperty($key)
    {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }
        throw new \Exception("Unknown user property (".$key.")");   
    }
}