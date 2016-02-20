<?php 

include __DIR__.'/../vendor/autoload.php';

use \bearonahill\Authentication;
use \bearonahill\Authentication\User;
use \bearonahill\Authentication\Mapper;

/**
 * Create a user with username and password.
 */
$User = new User('boris', 'secret password');

/**
 * You can pass arbitrary information to be stored with the user. It will also 
 * be available in the user's session object.
 */
$User->created_at = (new Datetime())->format(DateTime::ISO8601);

/**
 * Create a PDO object and define tablename, column names for username 
 * and password.
 */
$Pdo = new PDO('sqlite:example.sqlite');
$Mapper = new Mapper($Pdo, 'user', 'login', 'password');

$Authentication = new Authentication($Mapper);
$success = $Authentication->register($User);

if ($success === true) {
    echo 'registered'.PHP_EOL;
} else {
    echo $Authentication->getIssue()->getMessage();
}

