<?php

$pdo= require './database.php' ;

$statement = $pdo->prepare('
SELECT article.*, user.firstname, user.lastname 
FROM article 
LEFT JOIN user ON article.author = user.id 
WHERE article.id = :id
');

//récupérer et nettoyer l'id de l'article passé
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if(!$id){
    header('Location: /');
}else{
   $statement-> bindValue(':id',$id);
   $statement-> execute();
   $article= $statement-> fetch();

}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="/public/css/affiche-article.css">
    

    <title>Créer un Article</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php'?>
        <div class="content">

            <a href="/" class="back">Retour</a>
            <div class="article-cover-img" style="background-image: url(<?= $article['image'] ?>"></div>
            <h1 class="article-title"><?= $article['title'] ?> </h1>
            <div class="separator"></div>
            <p class="article-content"><?= $article['content'] ?> 
                <?php if ($article['author']) : ?>
                        <div class="article-author">
                            <p><?= $article['firstname'] . ' ' . $article['lastname']  ?></p>
                        </div>
                <?php endif; ?>
            </p>


        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>
</body>
</html>