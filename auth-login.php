<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo= require_once './database.php' ;

//gestion des erreurs
const ERROR_REQUIRED = 'Veuillez renseigner ce champ';
const ERROR_PASSWORD_MISMATCH = 'Le mot de passe n\'est pas valide';
const ERROR_EMAIL_INVALID = 'L\'email n\'est pas valide';
const ERROR_EMAIL_UNKOWN = 'L\'email n\'est pas enregistrée';
const ERROR_PASSWORD_TOO_SHORT= 'L\'email est trop court';
$errors=[   
    'email'=> '',
    'password'=> '',
];

if ($_SERVER['REQUEST_METHOD'] =='POST'){

    $input = filter_input_array(INPUT_POST, [
        'email' => FILTER_SANITIZE_EMAIL,
      ]);
   
    $email = $input['email'] ?? '';
    $password = $_POST['password'] ?? '';


    if (!$email) {
        $errors['email'] = ERROR_REQUIRED;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = ERROR_EMAIL_INVALID;
    }
          
    if (!$password) {
        $errors['password'] = ERROR_REQUIRED;
    } elseif (mb_strlen($password) < 6) {
        $errors['password'] = ERROR_PASSWORD_TOO_SHORT;
    }
          


//Vérifier qu'aucune erreur est détectée
    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
        $statementUser = $pdo->prepare('SELECT * FROM user WHERE email=:email');
        $statementUser->bindValue(':email', $email);
        $statementUser->execute();
        $user = $statementUser->fetch();

        if (!$user) {
            $errors['email'] = ERROR_EMAIL_UNKOWN;
        } else{
            //création d'une nouvelle session et un cookie de session pour l'utilisateur
            $statementSession = $pdo->prepare('INSERT INTO session VALUES (
                DEFAULT,
                :userid
              )');
              $statementSession->bindValue(':userid', $user['id']);
              $statementSession->execute();
              $sessionId = $pdo->lastInsertId();
              setcookie('session', $sessionId, time() + 60 * 60 * 24 * 14, '', '', false, true);
              header('Location: /');
            }

        }

}

?>



<!DOCTYPE html>
<html lang="fr">

<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="/public/css/auth-register.css">
  <title>Connexion</title>
</head>

<body>
  <div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
        <div class="block p-20 form-container">
                    <h1>Connexion</h1>
                    <form action="/auth-login.php", method="POST">

               

                        <div class="form-control">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value=<?= $email ?? '' ?>>
                            <?php if($errors['email']) : ?>
                                <p class="text-error"><?= $errors['email'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form-control">
                            <label for="password">Mot de passe</label>
                            <input type="password" name="password" id="password">
                            <?php if($errors['password']) : ?>
                                <p class="text-error"><?= $errors['password'] ?></p>
                            <?php endif; ?>
                        </div>

                        


                        <div class="form-action">
                            <a href="/" class="btn btn-secondary" type="button">Annuler</a>
                            <button class="btn btn-primary" type="submit">Connexion</button>
                        </div>



                    </form>
        </div>

    </div>
    <?php require_once 'includes/footer.php' ?>
  </div>

</body>

</html>