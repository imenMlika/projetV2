<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);



$pdo= require './database.php' ;
require './database/security.php';
$currentUser= isLoggedin();


$statement = $pdo->prepare('SELECT * FROM article');                                                                                                                                     
$statement->execute();
$articles= $statement->fetchAll();

$categories=[];



if(count($articles)){
    
    $cattmp= array_map(fn($a) => $a['category'], $articles); // tableau contenant uniquement les catégories
    $categories=array_reduce($cattmp,function($acc,$cat){
        if(isset($acc[$cat])){
            $acc[$cat] ++;

        }else{
            $acc[$cat]=1;

        }
        return $acc;

    },[]);

    $articlePerCategories=array_reduce($articles, function($acc,$article){
        if(isset($acc[$article['category']])) {
            $acc[$article['category']]= [...$acc[$article['category']],$article];
        }else{
            $acc[$article['category']]=[$article];
        }
        return $acc;

    },[]);

}



?>



<!DOCTYPE html>
<html lang="en">

<head>
    <?php require 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <title>Blog</title>
</head>

<body>
    <div class="container">
        <?php require 'includes/header.php'?>
        <div class="content">
            <div class="category-container">
                <?php foreach($categories as $cat =>$num): ?>
                    <h2><?= $cat ?></h2>
                    <div class="acticles-container">
                        <?php foreach($articlePerCategories[$cat] as $a): ?>
                            <a href="/affiche-article.php?id=<?= htmlspecialchars($a['id']) ?>" class="article block" >

                                <div class="overflow" >
                                    <div class="img-container" style="background-image: url(<?= $a['image'] ?>" ></div>
                                        <h3><?= htmlspecialchars($a['title']) ?></h3>
                                        
                                </div>
                                
                            

                            </a >
                        <?php endforeach ;?>
                    </div>
                <?php endforeach ;?>
            </div>
     

        </div>
        <?php require 'includes/footer.php' ?>
    </div>



</body>
</html>