<?php 

include __DIR__.'/../vendor/autoload.php';

use \bearonahill\Authentication;
use \bearonahill\Authentication\User;
use \bearonahill\Authentication\Mapper;

class RegisterTest extends PHPUnit_Framework_TestCase
{
    const SQLITE_FILE = __DIR__.'/data/test.sqlite';

    public function testRegisterSuccess()
    {
        $this->init();
        
        $User = new User('boris', 'secret password');
        $User->created_at = (new Datetime())->format(DateTime::ISO8601);

        $Pdo = new PDO('sqlite:'.self::SQLITE_FILE);
        $Mapper = new Mapper($Pdo, 'user', 'login', 'password');

        $Authentication = new Authentication($Mapper);
        $success = $Authentication->register($User);

        $this->assertTrue($success);
    }

    public function testRegisterFail()
    {
        $User = new User('boris', 'secret password 2');
        $User->created_at = (new Datetime())->format(DateTime::ISO8601);

        $Pdo = new PDO('sqlite:'.self::SQLITE_FILE);
        $Mapper = new Mapper($Pdo, 'user', 'login', 'password');

        $Authentication = new Authentication($Mapper);
        $success = $Authentication->register($User);

        $this->assertFalse($success);        
    }

    public function testLoginSuccess()
    {
        $User = new User('boris', 'secret password');

        $Pdo = new PDO('sqlite:'.self::SQLITE_FILE);
        $Mapper = new Mapper($Pdo, 'user', 'login', 'password');

        $Authentication = new Authentication($Mapper);
        $authenticatedUser = $Authentication->login($User);

        // Check Valid User
        $this->assertTrue($Authentication->isValid());

        // Check if login names are equal
        $authenticatedUser = $Authentication->getUser();        
        $this->assertEquals($User->getLogin(), $authenticatedUser['login']);

        // Check equal passwords
        $UserCheck = $Authentication->getUserFromArray($authenticatedUser);
        $this->assertTrue($User->verify($UserCheck));
    }

    public function testLoginFail()
    {
        $User = new User('boris', 'wrong password');

        $Pdo = new PDO('sqlite:'.self::SQLITE_FILE);
        $Mapper = new Mapper($Pdo, 'user', 'login', 'password');

        $Authentication = new Authentication($Mapper);
        $authenticatedUser = $Authentication->login($User);

        $this->assertFalse($Authentication->isValid());
    }

    private function init()
    {
        if (file_exists(self::SQLITE_FILE)) {
            unlink(self::SQLITE_FILE);
        }
        $Pdo = new PDO('sqlite:'.self::SQLITE_FILE);
        $Pdo->exec("CREATE TABLE IF NOT EXISTS `user` (`login` TEXT, `password` TEST, `created_at` TEXT)");
        $Pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS 'uniquelogin' ON `user` (`login`)");
    }
}
