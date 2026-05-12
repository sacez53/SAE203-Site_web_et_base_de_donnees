<?php
require(__DIR__ . "/session.inc.php");
header("Content-type: text/html; charset=UTF-8");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Politique de confidentialité – Radio</title>
</head>
<body>
    <nav><a href="index.php">← Accueil</a></nav>

    <h1>Politique de confidentialité (RGPD)</h1>

    <p>Conformément au <strong>Règlement Général sur la Protection des Données (RGPD)</strong>,
    et notamment à l'article 7, nous recueillons votre consentement explicite avant tout traitement
    de vos données personnelles.</p>

    <h2>Données collectées</h2>
    <ul>
        <li>Pseudo (pseudonyme choisi)</li>
        <li>Adresse email</li>
        <li>Mot de passe (stocké de façon sécurisée, jamais en clair)</li>
        <li>Date d'inscription</li>
        <li>Commentaires et favoris liés à votre compte</li>
    </ul>

    <h2>Finalité du traitement</h2>
    <p>Vos données sont utilisées exclusivement pour le fonctionnement de l'application
    (authentification, partage d'avis, gestion des favoris). Elles ne sont pas transmises à des tiers.</p>

    <h2>Vos droits</h2>
    <p>Vous pouvez à tout moment consulter, modifier ou supprimer votre compte depuis la page
    <a href="profil.php">Mon profil</a>. La suppression du compte efface l'ensemble de vos données
    personnelles (droit à l'effacement, article 17 du RGPD).</p>

    <h2>Contact</h2>
    <p>Pour toute question relative à vos données, utilisez notre
    <a href="reclamation.php">formulaire de réclamation</a>.</p>
</body>
</html>
