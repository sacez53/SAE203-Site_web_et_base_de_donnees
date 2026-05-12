<?php
require(__DIR__ . "/session.inc.php");

if (!estConnecte()) {
    header("Location: connexion.php");
    exit;
}

$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPseudo = $_POST['pseudo']   ?? '';
    $newEmail  = $_POST['email']    ?? '';
    $newMdp    = $_POST['mdp']      ?? '';

    if (isset($_POST['supprimer_compte'])) {
        $admin->supprimerUtilisateur(utilisateurId());
        $_SESSION = array();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    try {
        $user = $admin->mettreAJour(utilisateurId(), $newPseudo, $newEmail, $newMdp);
        $_SESSION['pseudo'] = $user['pseudo'];
        $_SESSION['email']  = $user['email'];
        $succes = "Profil mis à jour avec succès.";
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}

$user = $admin->obtenirUtilisateur(utilisateurId());

header("Content-type: text/html; charset=UTF-8");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon profil – Radio</title>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a> |
        <a href="favoris.php">Mes favoris</a> |
        <a href="deconnexion.php">Se déconnecter</a>
    </nav>

    <h1>Profil de <?php echo htmlspecialchars(utilisateurPseudo()); ?></h1>

    <?php if ($erreur !== ""): ?>
        <p style="color:red;"><?php echo $erreur; ?></p>
    <?php endif; ?>
    <?php if ($succes !== ""): ?>
        <p style="color:green;"><?php echo $succes; ?></p>
    <?php endif; ?>

    <form action="profil.php" method="post">
        <div>
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" maxlength="50"
                   value="<?php echo htmlspecialchars($user['pseudo']); ?>">
        </div>
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" maxlength="100"
                   value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        <div>
            <label for="mdp">Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" id="mdp" name="mdp" minlength="4">
        </div>
        <div>
            <button type="submit">Mettre à jour mon profil</button>
        </div>
    </form>

    <hr>
    <h2>Supprimer mon compte</h2>
    <p>Cette action est irréversible et supprimera toutes vos données (RGPD).</p>
    <form action="profil.php" method="post"
          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?');">
        <button type="submit" name="supprimer_compte">Supprimer mon compte</button>
    </form>
</body>
</html>
