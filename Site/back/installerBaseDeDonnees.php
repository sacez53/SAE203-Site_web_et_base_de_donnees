<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . "/param.inc.php");
require(__DIR__ . "/src/Radio/Administrer.php");

$admin = new Radio\Administrer(MYHOST, MYDB, MYUSER, MYPASS);
$admin->installerBaseDeDonnees();

header("Content-type: text/html; charset=UTF-8");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Installation de la base de données</title>
</head>
<body>
    <h1>Installation terminée</h1>
    <p>Les tables de la base de données <strong><?php echo MYDB; ?></strong> ont été créées et les données initiales insérées.</p>
    <ul>
        <li>Table <code>utilisateur</code></li>
        <li>Table <code>pays</code> (avec <?php echo count(['France','États-Unis','Royaume-Uni','Japon','Allemagne','Canada','International']); ?> pays)</li>
        <li>Table <code>style</code> (Metal, Rock, Jazz, Électronique, Drum &amp; Bass, Hip-Hop, Classique, Variété)</li>
        <li>Table <code>radio</code> (8 radios pré-chargées)</li>
        <li>Table <code>commentaire</code></li>
        <li>Table <code>favoris</code></li>
        <li>Table <code>reclamation</code></li>
    </ul>
    <p>
        Vérifiez sur 
        <a href="https://la-perso.univ-lemans.fr/phpmyadmin" target="_blank">phpMyAdmin</a>.
    </p>
    <p><a href="index.php">→ Aller au site</a></p>
</body>
</html>
