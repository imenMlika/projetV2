<?php
function isLoggedIn(): array|false
  {
      
      $pdo= require dirname(__DIR__) . '/database.php' ;
      // récupération des cookies
      $sessionId = $_COOKIE['session'] ?? '';
      if ($sessionId) {
        
        //vérifier si la session est valide
          $statementSession = $pdo->prepare('SELECT * from session WHERE id=:id');
          $statementSession->bindValue(':id', $sessionId);
          $statementSession->execute();
          $session = $statementSession->fetch();

          //Vérifie si une session correspondante a été trouvée dans la base de données
          if ($session) {
              $statementUser = $pdo->prepare('SELECT * FROM user WHERE id=:id');
              $statementUser->bindValue(':id', $session['userid']);
              $statementUser->execute();
              $user = $statementUser->fetch();
              
              if (is_array($user)) {
                  return $user;
              }
          }
      }
      return false;
  }
  
?>