<?php 

include __DIR__.'/../vendor/autoload.php';

use \bearonahill\Authentication;
use \bearonahill\Authentication\User;
use \bearonahill\Authentication\Mapper;

/**
 * Create a user with username and password
 */
$User = new User('boris', 'secret password');

/**
 * Create a PDO object and define tablename, column names for username 
 * and password.
 */
$Pdo = new PDO('sqlite:example.sqlite');
$Mapper = new Mapper($Pdo, 'user', 'login', 'password');

$Authentication = new Authentication($Mapper);
$authenticatedUser = $Authentication->login($User);

/**
 * Check if user information is valid and fetch the user data from database.
 */
if ($Authentication->isValid()) {
    var_dump($Authentication->getUser());
}