<?php
require(__DIR__ . "/session.inc.php");

$styleId   = isset($_GET['style_id'])  && $_GET['style_id']  !== '' ? (int)$_GET['style_id']  : null;
$paysId    = isset($_GET['pays_id'])   && $_GET['pays_id']   !== '' ? (int)$_GET['pays_id']    : null;
$recherche = isset($_GET['recherche']) && $_GET['recherche'] !== '' ? trim($_GET['recherche']) : null;

$radioAleatoire = null;
if (isset($_GET['aleatoire'])) {
    $radioAleatoire = $admin->radioAleatoire();
}

$radios = $admin->listerRadios($styleId, $paysId, $recherche);
$styles = $admin->listerStyles();
$pays   = $admin->listerPays();

header("Content-type: text/html; charset=UTF-8");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Radio – Accueil</title>
</head>
<body>
    <header>
        <h1>🎙 Radios Internet</h1>
        <nav>
            <?php if (estConnecte()): ?>
                Bonjour <strong><?php echo htmlspecialchars(utilisateurPseudo()); ?></strong> |
                <a href="profil.php">Mon profil</a> |
                <a href="favoris.php">Mes favoris</a> |
                <a href="reclamation.php">Réclamation</a> |
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php">Connexion</a> |
                <a href="inscription.php">Inscription</a> |
                <a href="reclamation.php">Réclamation</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>

        <section>
            <form action="index.php" method="get">
                <label for="recherche">Recherche :</label>
                <input type="text" id="recherche" name="recherche"
                       value="<?php echo htmlspecialchars($_GET['recherche'] ?? ''); ?>"
                       placeholder="Nom de la radio…">

                <label for="style_id">Style :</label>
                <select id="style_id" name="style_id">
                    <option value="">Tous les styles</option>
                    <?php foreach ($styles as $s): ?>
                        <option value="<?php echo $s['style_id']; ?>"
                            <?php echo ($styleId === (int)$s['style_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['style_nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="pays_id">Pays :</label>
                <select id="pays_id" name="pays_id">
                    <option value="">Tous les pays</option>
                    <?php foreach ($pays as $p): ?>
                        <option value="<?php echo $p['pays_id']; ?>"
                            <?php echo ($paysId === (int)$p['pays_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['pays_nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Filtrer</button>
                <a href="index.php">Réinitialiser</a>
            </form>

            <form action="index.php" method="get">
                <button type="submit" name="aleatoire" value="1">🎲 Radio aléatoire</button>
            </form>
        </section>

        <?php if ($radioAleatoire): ?>
        <section>
            <h2>Radio aléatoire</h2>
            <p>
                <a href="radio.php?id=<?php echo $radioAleatoire['radio_id']; ?>">
                    <?php echo htmlspecialchars($radioAleatoire['radio_nom']); ?>
                </a>
                — <?php echo htmlspecialchars($radioAleatoire['style_nom'] ?? ''); ?>
                / <?php echo htmlspecialchars($radioAleatoire['pays_nom'] ?? ''); ?>
            </p>
        </section>
        <?php endif; ?>

        <section>
            <h2>
                <?php
                if ($recherche || $styleId || $paysId) {
                    echo "Résultats (" . count($radios) . ")";
                } else {
                    echo "Toutes les radios (" . count($radios) . ")";
                }
                ?>
            </h2>

            <?php if (empty($radios)): ?>
                <p>Aucune radio trouvée.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($radios as $radio): ?>
                    <li>
                        <?php if ($radio['radio_img']): ?>
                            <img src="<?php echo htmlspecialchars($radio['radio_img']); ?>"
                                 alt="Logo <?php echo htmlspecialchars($radio['radio_nom']); ?>"
                                 width="40" height="40">
                        <?php endif; ?>
                        <a href="radio.php?id=<?php echo $radio['radio_id']; ?>">
                            <?php echo htmlspecialchars($radio['radio_nom']); ?>
                        </a>
                        — <?php echo htmlspecialchars($radio['style_nom'] ?? 'N/A'); ?>
                        / <?php echo htmlspecialchars($radio['pays_nom'] ?? 'N/A'); ?>
                        <?php if (!estConnecte()): ?>
                            <small>(<a href="connexion.php">Connectez-vous</a> pour les favoris &amp; commentaires)</small>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

    </main>
</body>
</html>
