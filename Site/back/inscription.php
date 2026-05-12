<?php
require(__DIR__ . "/session.inc.php");

if (estConnecte()) {
    header("Location: index.php");
    exit;
}

$erreur  = "";
$succes  = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo       = $_POST['pseudo']       ?? '';
    $email        = $_POST['email']        ?? '';
    $mdp          = $_POST['mdp']          ?? '';
    $consentement = isset($_POST['rgpd']);

    try {
        $admin->inscrire($pseudo, $email, $mdp, $consentement);
        $succes = "Inscription réussie ! Vous pouvez vous connecter.";
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}

header("Content-type: text/html; charset=UTF-8");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription – Radio</title>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a> |
        <a href="connexion.php">Connexion</a>
    </nav>

    <h1>Inscription</h1>

    <?php if ($erreur !== ""): ?>
        <p style="color:red;"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <?php if ($succes !== ""): ?>
        <p style="color:green;"><?php echo $succes; ?></p>
        <p><a href="connexion.php">Se connecter</a></p>
    <?php else: ?>
    <form action="inscription.php" method="post">
        <div>
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" required maxlength="50"
                   value="<?php echo htmlspecialchars($_POST['pseudo'] ?? ''); ?>">
        </div>
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required maxlength="100"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div>
            <label for="mdp">Mot de passe (min. 4 caractères) :</label>
            <input type="password" id="mdp" name="mdp" required minlength="4">
        </div>
        <div>
            <label>
                <input type="checkbox" name="rgpd" required>
                J'accepte que mes données soient utilisées conformément à la
                <a href="rgpd.php" target="_blank">politique de confidentialité</a> (RGPD).
            </label>
        </div>
        <div>
            <button type="submit">S'inscrire</button>
        </div>
    </form>
    <?php endif; ?>
</body>
</html>
