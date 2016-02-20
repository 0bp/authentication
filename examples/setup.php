<?php 

include __DIR__.'/../vendor/autoload.php';

$Pdo = new PDO('sqlite:example.sqlite');
$Pdo->exec("CREATE TABLE IF NOT EXISTS `user` (`login` TEXT, `password` TEST, `created_at` TEXT)");
$Pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS 'uniquelogin' ON `user` (`login`)");
