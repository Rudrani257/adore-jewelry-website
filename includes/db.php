<?php
// includes/db.php
$host = '127.0.0.1';
$db   = 'aura_luxe_db';
$user = 'root'; // XAMPP default
$pass = '';     // XAMPP default
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown database') === false) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>
