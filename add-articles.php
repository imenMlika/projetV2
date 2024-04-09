<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo= require './database.php' ;
require './database/security.php';

//// Vérification de l'authentification de l'utilisateur
$currentUser= isLoggedin();
if (!$currentUser) {
    header('Location: /');
  }


//Gestion des erreurs
const ERROR_REQUIRED="Veuillez renseigner ce champ";
const ERROR_TITLE_SHORT= "Le titre est trop court";
const ERROR_CONTENT_SHORT='L\'article est trop court';
const ERROR_IMAGE_URL ='L\'image doit etre une url valide';
$errors=[
    'title'=>'',
    'image'=>'',
    'category'=> '',
    'content'=> '',

];
$category = '';


$statementCreateOne = $pdo->prepare('
  INSERT INTO article (
    title,
    category,
    content,
    image,
    author
  ) VALUES (
    :title,
    :category,
    :content,
    :image,
    :author
  )
');

$statementReadOne = $pdo->prepare('
    SELECT article.*, user.firstname, user.lastname 
    FROM article 
    LEFT JOIN user ON article.author = user.id 
    WHERE article.id = :id
');



$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';
if ($id) {
  $statementReadOne->bindValue(':id', $id);
  $statementReadOne->execute();
  $article = $statementReadOne->fetch();
}
$articles=[];

if ($_SERVER['REQUEST_METHOD'] =='POST'){
    
    // gerer la sécurité du formulaire
    $_POST=filter_input_array(INPUT_POST,[
        'title'=> FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'image' => FILTER_SANITIZE_URL,
        'category'=>FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'content'=> [
            'filter'=> FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'flags'=> FILTER_FLAG_NO_ENCODE_QUOTES,
        ]
        ]);

        $title=$_POST['title'] ?? '';
        $image=$_POST['image'] ?? '';
        $category=$_POST['category'] ?? '';
        $content=$_POST['content'] ?? '';

    if(!$title){
        $errors['title']= ERROR_REQUIRED;
    }elseif(mb_strlen($title)<5){
        $errors['title']= ERROR_TITLE_SHORT;
    }

    if(!$image){
        $errors['image']= ERROR_REQUIRED ;

    }elseif(!filter_var($image,FILTER_VALIDATE_URL)){
        $errors['image']=ERROR_IMAGE_URL;

    }

    if(!$category){
        $errors['category']= ERROR_REQUIRED ;

    }

    if(!$content){
        $errors['content']= ERROR_REQUIRED ;

    }elseif(mb_strlen($content)< 50){
        $errors['content']= ERROR_CONTENT_SHORT;
    }

    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
        // Insertion de l'article dans la base de donnée
            $statementCreateOne->bindValue(':title', $title);
            $statementCreateOne->bindValue(':image', $image);
            $statementCreateOne->bindValue(':category', $category);
            $statementCreateOne->bindValue(':content', $content);
            $statementCreateOne->bindValue(':content', $content);
            $statementCreateOne->bindValue(':author', $currentUser['id']);
            $statementCreateOne->execute();
            header('Location: /');

        }


}; 

?>





<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="/public/css/add-article.css">
    

    <title>Créer un Article</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php'?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1>Ecrire un Article</h1>
                <form action="/add-articles.php", method="POST">

                    <div class="form-control">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value=<?= $title ?? '' ?>>
                        <?php if($errors['title']) : ?>
                            <p class="text-error"><?= $errors['title'] ?></p>
                        <?php endif; ?>

                     </div>



                     <div class="form-control">
                        <label for="image">Image</label>
                        <input type="text" name="image" id="image" value=<?= $image ?? '' ?>>
                        <?php if($errors['image']) : ?>
                            <p class="text-error"><?= $errors['image'] ?></p>
                        <?php endif; ?>
                     </div>


                     <div class="form-control">
                        <label for="category">Catégorie</label>
                        <select name="category" id="category">
                            <option value="Associatif">Associatif</option>
                            <option value="Professionnel">Professionnel</option>
                            <option value="Loisirs">Loisirs</option>
                        </select>

                        <?php if($errors['category']) : ?>
                            <p class="text-error"><?= $errors['category'] ?></p>
                        <?php endif; ?>

                     </div>


                     <div class="form-control">
                        <label for="content">Contenu</label>
                        <input type="text" name="content" id="content" value=<?= $content ?? '' ?>>
                        <?php if($errors['content']) : ?>
                            <p class="text-error"><?= $errors['content'] ?></p>
                        <?php endif; ?>
                     </div>

                     <div class="form-action">
                        <a href="/" class="btn btn-secondary" type="button">Annuler</a>
                        <button class="btn btn-primary" type="submit">Sauvgarder</button>
                     </div>



                </form>
            </div>



        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>



</body>
</html>