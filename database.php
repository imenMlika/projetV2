<?php

$dns='mysql:host=localhost;dbname=blog';
$user='root';
$pwd='Ayimem123456789.';

try{
$pdo= new PDO($dns,$user,$pwd,[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO:: ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC


]);


} catch(PDOException $e){
    echo "error" . $e->getMessage();
}

return $pdo;

?>
