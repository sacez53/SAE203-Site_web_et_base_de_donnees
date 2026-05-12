<?php
require(__DIR__ . "/session.inc.php");

$erreur = "";
$succes = "";

$types = [
    'Lien mort',
    'Erreur de catégorie',
    'Contenu inapproprié',
    'Problème de qualité audio',
    'Autre',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $objet   = $_POST['objet']   ?? '';
    $message = $_POST['message'] ?? '';
    $uid     = estConnecte() ? utilisateurId() : null;

    try {
        $admin->envoyerReclamation($objet, $message, $uid);
        $succes = "Votre réclamation a bien été envoyée. Merci !";
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
    <title>Réclamation – Radio</title>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a>
        <?php if (estConnecte()): ?>
            | <a href="profil.php">Mon profil</a>
            | <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            | <a href="connexion.php">Connexion</a>
        <?php endif; ?>
    </nav>

    <h1>📩 Envoyer une réclamation</h1>

    <?php if ($erreur !== ""): ?>
        <p style="color:red;"><?php echo $erreur; ?></p>
    <?php endif; ?>

    <?php if ($succes !== ""): ?>
        <p style="color:green;"><?php echo $succes; ?></p>
        <p><a href="index.php">Retour à l'accueil</a></p>
    <?php else: ?>
    <form action="reclamation.php" method="post">
        <div>
            <label for="objet">Type de problème :</label>
            <select id="objet" name="objet">
                <?php foreach ($types as $t): ?>
                    <option value="<?php echo htmlspecialchars($t); ?>"
                        <?php echo (($_POST['objet'] ?? '') === $t) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($t); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="message">Message :</label><br>
            <textarea id="message" name="message" rows="5" cols="60" required><?php
                echo htmlspecialchars($_POST['message'] ?? '');
            ?></textarea>
        </div>
        <?php if (!estConnecte()): ?>
            <p><small>Vous n'êtes pas connecté. Votre réclamation sera envoyée de façon anonyme.</small></p>
        <?php endif; ?>
        <div>
            <button type="submit">Envoyer la réclamation</button>
        </div>
    </form>
    <?php endif; ?>
</body>
</html>
