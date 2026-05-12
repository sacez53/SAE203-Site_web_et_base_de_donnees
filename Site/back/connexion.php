<?php
require(__DIR__ . "/session.inc.php");

if (estConnecte()) {
    header("Location: index.php");
    exit;
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mdp   = $_POST['mdp']   ?? '';

    try {
        $user = $admin->connecter($email, $mdp);
        $_SESSION['utilisateur_id'] = $user['utilisateur_id'];
        $_SESSION['pseudo']         = $user['pseudo'];
        $_SESSION['email']          = $user['email'];
        header("Location: index.php");
        exit;
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
    <title>Connexion – Radio</title>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a> |
        <a href="inscription.php">Inscription</a>
    </nav>

    <h1>Connexion</h1>

    <?php if ($erreur !== ""): ?>
        <p style="color:red;"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <form action="connexion.php" method="post">
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div>
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>
        </div>
        <div>
            <button type="submit">Se connecter</button>
        </div>
    </form>

    <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
</body>
</html>
