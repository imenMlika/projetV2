<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo= require './database.php' ;
require './database/security.php';

$currentUser= isLoggedin();

if (!$currentUser) {
    header('Location: /');
}

$articles = [];
$authorId = $currentUser['id'];


$statementReadUserAll = $pdo->prepare('SELECT * FROM article WHERE author=:authorId');
$statementReadUserAll->bindValue(':authorId', $authorId);
$statementReadUserAll->execute();
$articles=$statementReadUserAll->fetchAll();



?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="/public/css/profile.css">
  <title>Mon profil</title>
</head>

<body>
  <div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">

    <h1>Mon espace</h1>

      <h2>Mes informations</h2>

      <div class="info-container">
        <ul>
          <li>
            <strong>Prénom :</strong>
            <p><?= $currentUser['firstname'] ?></p>
          </li>

          <li>
            <strong>Nom :</strong>
            <p><?= $currentUser['lastname'] ?></p>
          </li>

          <li>
            <strong>Email :</strong>
            <p><?= $currentUser['email'] ?></p>
          </li>

        </ul>
      </div>

      <h2>Mes articles</h2>
      <div class="articles-list">
        <ul>
          <?php foreach ($articles as $a) : ?>
            <li>
              <span><?= $a['title'] ?></span>
          <?php endforeach; ?>

    </div>

    <?php require_once 'includes/footer.php' ?>
  </div>

</body>

</html>