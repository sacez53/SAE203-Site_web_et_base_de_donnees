<?php
require(__DIR__ . "/session.inc.php");

if (!estConnecte()) {
    header("Location: connexion.php");
    exit;
}

$favoris = $admin->listerFavoris(utilisateurId());

header("Content-type: text/html; charset=UTF-8");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes favoris – Radio</title>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a> |
        <a href="profil.php">Mon profil</a> |
        <a href="deconnexion.php">Déconnexion</a>
    </nav>

    <h1>⭐ Mes radios favorites</h1>

    <?php if (empty($favoris)): ?>
        <p>Vous n'avez pas encore de favoris. <a href="index.php">Découvrez nos radios !</a></p>
    <?php else: ?>
        <ul>
            <?php foreach ($favoris as $r): ?>
            <li>
                <a href="radio.php?id=<?php echo $r['radio_id']; ?>">
                    <?php echo htmlspecialchars($r['radio_nom']); ?>
                </a>
                — <?php echo htmlspecialchars($r['style_nom'] ?? ''); ?>
                / <?php echo htmlspecialchars($r['pays_nom'] ?? ''); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
