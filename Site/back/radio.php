<?php
require(__DIR__ . "/session.inc.php");

$radioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$radio   = $admin->obtenirRadio($radioId);

if (!$radio) {
    header("Location: index.php");
    exit;
}

$erreurCommentaire = "";
$succesCommentaire = "";
$erreurFavori      = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentaire'])) {
    if (!estConnecte()) {
        $erreurCommentaire = "Vous devez être connecté pour commenter.";
    } else {
        try {
            $admin->ajouterCommentaire($_POST['commentaire'], utilisateurId(), $radioId);
            $succesCommentaire = "Commentaire ajouté !";
        } catch (Exception $e) {
            $erreurCommentaire = $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_favori'])) {
    if (!estConnecte()) {
        $erreurFavori = "Vous devez être connecté pour gérer vos favoris.";
    } else {
        if ($_POST['action_favori'] === 'ajouter') {
            $admin->ajouterFavori(utilisateurId(), $radioId);
        } elseif ($_POST['action_favori'] === 'retirer') {
            $admin->supprimerFavori(utilisateurId(), $radioId);
        }
    }
}

$commentaires = $admin->listerCommentaires($radioId);
$estFavori    = estConnecte() ? $admin->estFavori(utilisateurId(), $radioId) : false;

$qualite  = isset($_GET['qualite']) && $_GET['qualite'] === 'lq' ? 'lq' : 'hq';
$urlFlux  = ($qualite === 'lq' && $radio['radio_url_lq']) ? $radio['radio_url_lq'] : $radio['radio_url_hq'];

header("Content-type: text/html; charset=UTF-8");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($radio['radio_nom']); ?> – Radio</title>
</head>
<body>
    <nav>
        <a href="index.php">← Retour à l'accueil</a>
        <?php if (estConnecte()): ?>
            | <a href="profil.php">Mon profil</a>
            | <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            | <a href="connexion.php">Connexion</a>
        <?php endif; ?>
    </nav>

    <main>
        <h1>
            <?php if ($radio['radio_img']): ?>
                <img src="<?php echo htmlspecialchars($radio['radio_img']); ?>"
                     alt="Logo <?php echo htmlspecialchars($radio['radio_nom']); ?>"
                     width="60" height="60">
            <?php endif; ?>
            <?php echo htmlspecialchars($radio['radio_nom']); ?>
        </h1>

        <p>
            <strong>Style :</strong> <?php echo htmlspecialchars($radio['style_nom'] ?? 'N/A'); ?> |
            <strong>Pays :</strong>  <?php echo htmlspecialchars($radio['pays_nom']  ?? 'N/A'); ?>
        </p>

        <?php if ($radio['description']): ?>
            <p><?php echo htmlspecialchars($radio['description']); ?></p>
        <?php endif; ?>

        <section>
            <h2>🎧 Écouter</h2>

            <?php if ($radio['radio_url_lq']): ?>
                <p>
                    Qualité :
                    <a href="radio.php?id=<?php echo $radioId; ?>&qualite=hq"
                       <?php echo $qualite === 'hq' ? 'style="font-weight:bold"' : ''; ?>>Haute</a>
                    |
                    <a href="radio.php?id=<?php echo $radioId; ?>&qualite=lq"
                       <?php echo $qualite === 'lq' ? 'style="font-weight:bold"' : ''; ?>>Basse</a>
                </p>
            <?php endif; ?>

            <audio controls autoplay style="width:100%; max-width:500px;">
                <source src="<?php echo htmlspecialchars($urlFlux); ?>" type="audio/mpeg">
                Votre navigateur ne supporte pas la lecture audio.
            </audio>

            <p><small>Flux : <code><?php echo htmlspecialchars($urlFlux); ?></code></small></p>
        </section>

        <section>
            <h2>⭐ Favoris</h2>
            <?php if ($erreurFavori !== ""): ?>
                <p style="color:red;"><?php echo $erreurFavori; ?></p>
            <?php endif; ?>

            <?php if (estConnecte()): ?>
                <form action="radio.php?id=<?php echo $radioId; ?>" method="post">
                    <?php if ($estFavori): ?>
                        <button type="submit" name="action_favori" value="retirer">
                            ★ Retirer des favoris
                        </button>
                    <?php else: ?>
                        <button type="submit" name="action_favori" value="ajouter">
                            ☆ Ajouter aux favoris
                        </button>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <p><a href="connexion.php">Connectez-vous</a> pour ajouter cette radio à vos favoris.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>💬 Commentaires (<?php echo count($commentaires); ?>)</h2>

            <?php if (estConnecte()): ?>
                <?php if ($erreurCommentaire !== ""): ?>
                    <p style="color:red;"><?php echo $erreurCommentaire; ?></p>
                <?php endif; ?>
                <?php if ($succesCommentaire !== ""): ?>
                    <p style="color:green;"><?php echo $succesCommentaire; ?></p>
                <?php endif; ?>

                <form action="radio.php?id=<?php echo $radioId; ?>" method="post">
                    <div>
                        <label for="commentaire">Votre commentaire :</label><br>
                        <textarea id="commentaire" name="commentaire" rows="3" cols="50" required></textarea>
                    </div>
                    <button type="submit">Publier</button>
                </form>
            <?php else: ?>
                <p><a href="connexion.php">Connectez-vous</a> pour laisser un commentaire.</p>
            <?php endif; ?>

            <?php if (empty($commentaires)): ?>
                <p>Soyez le premier à commenter cette radio !</p>
            <?php else: ?>
                <?php foreach ($commentaires as $c): ?>
                    <article>
                        <p>
                            <strong><?php echo htmlspecialchars($c['pseudo']); ?></strong>
                            <em><?php echo $c['date_commentaire']; ?></em>
                        </p>
                        <p><?php echo htmlspecialchars($c['contenu']); ?></p>
                    </article>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

    </main>
</body>
</html>
