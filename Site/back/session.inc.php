<?php
session_start();

require_once(__DIR__ . "/param.inc.php");
require_once(__DIR__ . "/src/Radio/Administrer.php");

$admin = new Radio\Administrer(MYHOST, MYDB, MYUSER, MYPASS);

function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

function utilisateurId() {
    return $_SESSION['utilisateur_id'] ?? null;
}

function utilisateurPseudo() {
    return $_SESSION['pseudo'] ?? null;
}
?>
