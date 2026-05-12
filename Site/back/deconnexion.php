<?php
require(__DIR__ . "/session.inc.php");

$_SESSION = array();
session_destroy();

header("Location: connexion.php");
exit;
?>
