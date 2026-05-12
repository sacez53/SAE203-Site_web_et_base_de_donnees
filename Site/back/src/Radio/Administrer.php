<?php

namespace Radio;

use PDO;
use Exception;

/**
 * Administrer - Gestion de la base de données pour l'application Radio
 */
class Administrer
{
    private $myHost;
    private $myDb;
    private $myUser;
    private $myPass;
    private $debug;

    function __construct($myHost = null, $myDb = null, $myUser = null, $myPass = null)
    {
        $this->myHost = $myHost;
        $this->myDb   = $myDb;
        $this->myUser = $myUser;
        $this->myPass = $myPass;
        $this->debug  = true;
    }

    // ─────────────────────────────────────────────
    //  Connexion interne
    // ─────────────────────────────────────────────

    private function connexion()
    {
        $pdo = new PDO(
            "mysql:host=" . $this->myHost . ";dbname=" . $this->myDb . ";charset=utf8",
            $this->myUser,
            $this->myPass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->query("SET NAMES utf8");
        $pdo->query("SET CHARACTER SET 'utf8'");
        return $pdo;
    }

    // ─────────────────────────────────────────────
    //  Installation de la base de données
    // ─────────────────────────────────────────────

    public function installerBaseDeDonnees()
    {
        try {
            // Créer la base si elle n'existe pas
            $pdo = new PDO(
                "mysql:host=" . $this->myHost,
                $this->myUser,
                $this->myPass
            );
            $pdo->query(
                "CREATE DATABASE IF NOT EXISTS " . $this->myDb .
                    " DEFAULT CHARACTER SET utf8 COLLATE utf8_bin"
            );
            $pdo = null;

            $pdo = $this->connexion();

            // ── Tables ────────────────────────────────────────────────────────

            $sql = <<<SQL
            DROP TABLE IF EXISTS favoris;
            DROP TABLE IF EXISTS commentaire;
            DROP TABLE IF EXISTS reclamation;
            DROP TABLE IF EXISTS radio;
            DROP TABLE IF EXISTS style;
            DROP TABLE IF EXISTS pays;
            DROP TABLE IF EXISTS utilisateur;

            CREATE TABLE utilisateur (
                utilisateur_id    INT AUTO_INCREMENT PRIMARY KEY,
                pseudo            VARCHAR(50)  NOT NULL UNIQUE,
                email             VARCHAR(100) NOT NULL UNIQUE,
                mot_de_passe      VARCHAR(255) NOT NULL,
                date_inscription  DATETIME DEFAULT CURRENT_TIMESTAMP,
                consentement_rgpd BOOLEAN NOT NULL DEFAULT 0
            );

            CREATE TABLE pays (
                pays_id  INT AUTO_INCREMENT PRIMARY KEY,
                pays_nom VARCHAR(100) NOT NULL
            );

            CREATE TABLE style (
                style_id  INT AUTO_INCREMENT PRIMARY KEY,
                style_nom VARCHAR(50) NOT NULL UNIQUE
            );

            CREATE TABLE radio (
                radio_id    INT AUTO_INCREMENT PRIMARY KEY,
                radio_nom   VARCHAR(100) NOT NULL,
                radio_url_hq TEXT NOT NULL,
                radio_url_lq TEXT,
                radio_img   TEXT,
                description TEXT,
                style_id    INT,
                pays_id     INT,
                FOREIGN KEY (style_id) REFERENCES style(style_id)  ON DELETE SET NULL ON UPDATE CASCADE,
                FOREIGN KEY (pays_id)  REFERENCES pays(pays_id)    ON DELETE SET NULL ON UPDATE CASCADE
            );

            CREATE TABLE commentaire (
                commentaire_id   INT AUTO_INCREMENT PRIMARY KEY,
                contenu          TEXT NOT NULL,
                date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
                utilisateur_id   INT,
                radio_id         INT,
                FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (radio_id)       REFERENCES radio(radio_id)             ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE favoris (
                utilisateur_id INT,
                radio_id       INT,
                date_ajout     DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (utilisateur_id, radio_id),
                FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (radio_id)       REFERENCES radio(radio_id)             ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE reclamation (
                reclam_id    INT AUTO_INCREMENT PRIMARY KEY,
                reclam_objet VARCHAR(100),
                reclam_msg   TEXT,
                reclam_date  DATETIME DEFAULT CURRENT_TIMESTAMP,
                statut       VARCHAR(50) DEFAULT 'En attente',
                utilisateur_id INT,
                FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE SET NULL ON UPDATE CASCADE
            );
SQL;

            foreach (explode(";", $sql) as $requete) {
                $requete = trim($requete);
                if ($requete !== "") {
                    $pdo->query($requete);
                }
            }

            // ── Données initiales : styles ────────────────────────────────────

            $styles = [
                'Chill',
                'Lofi',
            ];
            $stmtStyle = $pdo->prepare("INSERT INTO style (style_nom) VALUES (?)");
            foreach ($styles as $s) {
                $stmtStyle->execute([$s]);
            }

            // ── Données initiales : pays ──────────────────────────────────────

            $pays = [
                'Royaume-Uni',
                'Allemagne',
                'Nouvelle-calédonie',
            ];
            $stmtPays = $pdo->prepare("INSERT INTO pays (pays_nom) VALUES (?)");
            foreach ($pays as $p) {
                $stmtPays->execute([$p]);
            }

            // ── Récupération des IDs ──────────────────────────────────────────

            $stylesMap = [];
            foreach ($pdo->query("SELECT style_id, style_nom FROM style") as $row) {
                $stylesMap[$row['style_nom']] = $row['style_id'];
            }

            $paysMap = [];
            foreach ($pdo->query("SELECT pays_id, pays_nom FROM pays") as $row) {
                $paysMap[$row['pays_nom']] = $row['pays_id'];
            }

            // ── Données initiales : radios ────────────────────────────────────

            $radios = [
                [
                    'nom'         => 'Chill',
                    'url_hq'      => 'https://ice-sov.musicradio.com/ChillMP3',
                    'url_lq'      => null,
                    'img'         => null,
                    'description' => null,
                    'style'       => 'Chill',
                    'pays'        => 'Royaume-Uni',
                ],
                [
                    'nom'         => 'BigFM Lofi Focus',
                    'url_hq'      => 'https://audiotainment-sw.streamabc.net/atsw-lofifocus-mp3-128-3757575?sABC=6n01r058%230%2367nrp2s2652128182n651247n558n8p0%23enqvbtneqra&aw_0_1st.playerid=radiogarden&amsparams=playerid:radiogarden;skey:1778507864',
                    'url_lq'      => null,
                    'img'         => null,
                    'description' => null,
                    'style'       => 'Lofi',
                    'pays'        => 'Allemagne',
                ],
                [
                    'nom'         => 'NIA Radio - Lofi',
                    'url_hq'      => 'https://radio.nia.nc/radio/8020/lofi-hq-stream.aac',
                    'url_lq'      => null,
                    'img'         => null,
                    'description' => null,
                    'style'       => 'Lofi',
                    'pays'        => 'Nouvelle-calédonie',
                ]
            ];

            $stmtRadio = $pdo->prepare(
                "INSERT INTO radio (radio_nom, radio_url_hq, radio_url_lq, radio_img, description, style_id, pays_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            foreach ($radios as $r) {
                $stmtRadio->execute([
                    $r['nom'],
                    $r['url_hq'],
                    $r['url_lq'],
                    $r['img'],
                    $r['description'],
                    $stylesMap[$r['style']] ?? null,
                    $paysMap[$r['pays']]   ?? null,
                ]);
            }

            $pdo = null;
        } catch (Exception $e) {
            if ($this->debug) {
                echo "Erreur : " . $e->getMessage();
            }
        }

        return $this;
    }

    // ─────────────────────────────────────────────
    //  UTILISATEURS
    // ─────────────────────────────────────────────

    /**
     * Inscrire un nouvel utilisateur
     */
    public function inscrire($pseudo, $email, $mdp, $consentement = false)
    {
        if (empty($pseudo) || empty($email) || empty($mdp)) {
            throw new Exception("Tous les champs doivent être complétés !");
        }

        $pseudo = htmlspecialchars($pseudo);
        $email  = htmlspecialchars($email);

        if (mb_strlen($pseudo) > 50) {
            throw new Exception("Votre pseudo ne doit pas dépasser 50 caractères !");
        }
        if (strlen($mdp) < 4) {
            throw new Exception("Votre mot de passe doit posséder au moins 4 caractères !");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Votre adresse email n'est pas valide !");
        }
        if (!$consentement) {
            throw new Exception("Vous devez accepter la politique de confidentialité (RGPD).");
        }

        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

        $pdo = $this->connexion();

        // Vérifier unicité pseudo
        $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        if ($stmt->fetch()) {
            throw new Exception("Ce pseudo est déjà utilisé !");
        }

        // Vérifier unicité email
        $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Cette adresse email est déjà utilisée !");
        }

        $stmt = $pdo->prepare(
            "INSERT INTO utilisateur (pseudo, email, mot_de_passe, consentement_rgpd) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$pseudo, $email, $mdpHash, $consentement ? 1 : 0]);

        $pdo = null;
    }

    /**
     * Connecter un utilisateur
     * @return array|null tableau associatif de l'utilisateur ou null
     */
    public function connecter($email, $mdp)
    {
        if (empty($email) || empty($mdp)) {
            throw new Exception("Tous les champs doivent être complétés !");
        }

        $email = htmlspecialchars($email);

        $pdo  = $this->connexion();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($mdp, $user['mot_de_passe'])) {
            throw new Exception("Email ou mot de passe incorrect !");
        }

        $pdo = null;
        return $user;
    }

    /**
     * Obtenir un utilisateur par son ID
     */
    public function obtenirUtilisateur($id)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare("SELECT utilisateur_id, pseudo, email, date_inscription FROM utilisateur WHERE utilisateur_id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo  = null;
        return $user ?: null;
    }

    /**
     * Mettre à jour le profil
     */
    public function mettreAJour($id, $newPseudo = null, $newEmail = null, $newMdp = null)
    {
        if (!$id) {
            throw new Exception("Identifiant manquant !");
        }

        $pdo    = $this->connexion();
        $erreur = "";

        if (!empty($newPseudo)) {
            $newPseudo = htmlspecialchars($newPseudo);
            $stmt = $pdo->prepare("UPDATE utilisateur SET pseudo = ? WHERE utilisateur_id = ?");
            $stmt->execute([$newPseudo, $id]);
        }

        if (!empty($newEmail)) {
            $newEmail = htmlspecialchars($newEmail);
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $erreur .= "Email invalide. ";
            } else {
                $stmt = $pdo->prepare("UPDATE utilisateur SET email = ? WHERE utilisateur_id = ?");
                $stmt->execute([$newEmail, $id]);
            }
        }

        if (!empty($newMdp)) {
            if (strlen($newMdp) < 4) {
                $erreur .= "Mot de passe trop court (minimum 4 caractères). ";
            } else {
                $hash = password_hash($newMdp, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE utilisateur_id = ?");
                $stmt->execute([$hash, $id]);
            }
        }

        $pdo = null;

        if ($erreur !== "") {
            throw new Exception($erreur);
        }

        return $this->obtenirUtilisateur($id);
    }

    /**
     * Supprimer un utilisateur (RGPD)
     */
    public function supprimerUtilisateur($id)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE utilisateur_id = ?");
        $stmt->execute([$id]);
        $pdo  = null;
    }

    // ─────────────────────────────────────────────
    //  RADIOS
    // ─────────────────────────────────────────────

    /**
     * Lister toutes les radios (avec style et pays)
     */
    public function listerRadios($styleId = null, $paysId = null, $recherche = null)
    {
        $pdo = $this->connexion();

        $sql    = "SELECT r.*, s.style_nom, p.pays_nom
                   FROM radio r
                   LEFT JOIN style s ON r.style_id = s.style_id
                   LEFT JOIN pays  p ON r.pays_id  = p.pays_id
                   WHERE 1=1";
        $params = [];

        if ($styleId) {
            $sql      .= " AND r.style_id = ?";
            $params[]  = $styleId;
        }
        if ($paysId) {
            $sql      .= " AND r.pays_id = ?";
            $params[]  = $paysId;
        }
        if ($recherche) {
            $sql      .= " AND r.radio_nom LIKE ?";
            $params[]  = "%" . $recherche . "%";
        }

        $sql .= " ORDER BY r.radio_nom ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $radios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pdo    = null;
        return $radios;
    }

    /**
     * Obtenir une radio par son ID
     */
    public function obtenirRadio($id)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare(
            "SELECT r.*, s.style_nom, p.pays_nom
             FROM radio r
             LEFT JOIN style s ON r.style_id = s.style_id
             LEFT JOIN pays  p ON r.pays_id  = p.pays_id
             WHERE r.radio_id = ?"
        );
        $stmt->execute([$id]);
        $radio = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo   = null;
        return $radio ?: null;
    }

    /**
     * Radio aléatoire
     */
    public function radioAleatoire()
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->query(
            "SELECT r.*, s.style_nom, p.pays_nom
             FROM radio r
             LEFT JOIN style s ON r.style_id = s.style_id
             LEFT JOIN pays  p ON r.pays_id  = p.pays_id
             ORDER BY RAND() LIMIT 1"
        );
        $radio = $stmt->fetch(PDO::FETCH_ASSOC);
        $pdo   = null;
        return $radio ?: null;
    }

    // ─────────────────────────────────────────────
    //  STYLES & PAYS (pour filtres)
    // ─────────────────────────────────────────────

    public function listerStyles()
    {
        $pdo    = $this->connexion();
        $stmt   = $pdo->query("SELECT * FROM style ORDER BY style_nom ASC");
        $styles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pdo    = null;
        return $styles;
    }

    public function listerPays()
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->query("SELECT * FROM pays ORDER BY pays_nom ASC");
        $pays = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pdo  = null;
        return $pays;
    }

    // ─────────────────────────────────────────────
    //  COMMENTAIRES
    // ─────────────────────────────────────────────

    /**
     * Ajouter un commentaire
     */
    public function ajouterCommentaire($contenu, $utilisateurId, $radioId)
    {
        if (empty(trim($contenu))) {
            throw new Exception("Le commentaire ne peut pas être vide !");
        }

        $contenu = htmlspecialchars($contenu);
        $pdo     = $this->connexion();
        $stmt    = $pdo->prepare(
            "INSERT INTO commentaire (contenu, utilisateur_id, radio_id) VALUES (?, ?, ?)"
        );
        $stmt->execute([$contenu, $utilisateurId, $radioId]);
        $pdo = null;
    }

    /**
     * Lister les commentaires d'une radio
     */
    public function listerCommentaires($radioId)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare(
            "SELECT c.*, u.pseudo
             FROM commentaire c
             JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
             WHERE c.radio_id = ?
             ORDER BY c.date_commentaire DESC"
        );
        $stmt->execute([$radioId]);
        $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pdo          = null;
        return $commentaires;
    }

    // ─────────────────────────────────────────────
    //  FAVORIS
    // ─────────────────────────────────────────────

    public function ajouterFavori($utilisateurId, $radioId)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO favoris (utilisateur_id, radio_id) VALUES (?, ?)"
        );
        $stmt->execute([$utilisateurId, $radioId]);
        $pdo = null;
    }

    public function supprimerFavori($utilisateurId, $radioId)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare(
            "DELETE FROM favoris WHERE utilisateur_id = ? AND radio_id = ?"
        );
        $stmt->execute([$utilisateurId, $radioId]);
        $pdo = null;
    }

    public function listerFavoris($utilisateurId)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare(
            "SELECT r.*, s.style_nom, p.pays_nom
             FROM favoris f
             JOIN radio r ON f.radio_id = r.radio_id
             LEFT JOIN style s ON r.style_id = s.style_id
             LEFT JOIN pays  p ON r.pays_id  = p.pays_id
             WHERE f.utilisateur_id = ?
             ORDER BY f.date_ajout DESC"
        );
        $stmt->execute([$utilisateurId]);
        $favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pdo     = null;
        return $favoris;
    }

    public function estFavori($utilisateurId, $radioId)
    {
        $pdo  = $this->connexion();
        $stmt = $pdo->prepare(
            "SELECT 1 FROM favoris WHERE utilisateur_id = ? AND radio_id = ?"
        );
        $stmt->execute([$utilisateurId, $radioId]);
        $result = $stmt->fetch();
        $pdo    = null;
        return (bool) $result;
    }

    // ─────────────────────────────────────────────
    //  RÉCLAMATIONS
    // ─────────────────────────────────────────────

    public function envoyerReclamation($objet, $message, $utilisateurId = null)
    {
        if (empty(trim($message))) {
            throw new Exception("Le message de réclamation ne peut pas être vide !");
        }

        $objet   = htmlspecialchars($objet ?? '');
        $message = htmlspecialchars($message);

        $pdo  = $this->connexion();
        $stmt = $pdo->prepare(
            "INSERT INTO reclamation (reclam_objet, reclam_msg, utilisateur_id) VALUES (?, ?, ?)"
        );
        $stmt->execute([$objet, $message, $utilisateurId]);
        $pdo = null;
    }
}
