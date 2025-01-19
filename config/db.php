<?php
$host = $_ENV['MYSQL_HOST'];
$dbase = $_ENV['MYSQL_DATABASE'];
$username = $_ENV['MYSQL_USER'];
$password = $_ENV['MYSQL_PASSWORD'];
return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host=$host;dbname=$dbase",
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
