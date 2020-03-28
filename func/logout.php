<?php
  include 'db.php';
  setcookie('clta_session', '', 0, "/");
  setcookie('clta_session_user', '', 0, "/");
  setcookie('clta_session_pass', '', 0, "/");
  db_sessionDestroy();
?>
