<?php
require 'includes/db.php';
$pdo->exec("UPDATE products SET image_url = 'assets/images/diamond_tennis_bracelet.png' WHERE name LIKE '%Tennis Bracelet%'");
unlink(__FILE__);
