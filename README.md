# SAE203-Site_web_et_base_de_donnees | RadioWeb 🎧

> Application web de radios internet avec avis utilisateurs — SAÉ BUT MMI

RadioWeb est une application web qui permet de sélectionner et d’écouter des radios internet, de les classer par catégories, et de permettre aux utilisateurs de laisser des avis, commentaires et notations.  
Ce projet a été réalisé dans le cadre d’une SAÉ du BUT MMI à l’Université du Mans.

---

## 📌 Fonctionnalités principales

- Sélection et lecture de radios internet à partir d’une base de données de plus d’une dizaine de stations réparties dans au moins trois catégories.  
- Inscription et connexion des utilisateurs pour laisser des avis (likes, commentaires, éventuelles notes).  
- Affichage différencié : certaines pages accessibles sans connexion, d’autres réservées aux utilisateurs authentifiés.  
- Gestion des données utilisateurs conforme au **RGPD** (consentement explicite, droit à l’information, gestion des données personnelles).  
- Interface **responsive**, testée sur mobile et bureau.  

---

## 🛠 Technologies utilisées

### Frontend
- HTML5 (balisage sémantique)  
- CSS — Tailwind CSS ou feuilles de style classiques  
- JavaScript côté client  
  - Pas de fonctions anonymes  
  - Pas d’imbrication de fonctions  
  - Utilisation de `for ... of` pour parcourir les collections  
  - Utilisation du mot‑clé `this` dans les écouteurs d’événements  

### Backend
- PHP  
  - gestion de session  
  - requêtes HTTP `GET` / `POST`  
  - sécurisation contre les injections SQL via **PDO**  
- SQL / MySQL  
  - structure de la base de données définie et testée via phpMyAdmin  

### Hébergement
- Déploiement sur `https://la-perso.univ-lemans.fr`  
- Liens relatifs pour permettre l’export vers un autre serveur  

---

## 🗃 Base de données

La base de données est composée des tables suivantes :

| Table           | Rôle principal |
|-----------------|----------------|
| `utilisateurs`  | Gestion des comptes, mots de passe hachés, consentement RGPD |
| `categories`    | Liste des catégories de radios (ex. musique, info, sport) |
| `radios`        | Liste des stations, URL du flux, catégorie associée |
| `avis`          | Liens utilisateur → radio, likes, commentaires, éventuelles notes |

Le **modèle de données (MCD)** a été généré via la vue Concepteur de phpMyAdmin et est fourni dans le projet.  
Toutes les requêtes SQL nécessaires ont été testées dans phpMyAdmin.

---

## 🧩 Bonnes pratiques appliquées

- Respect des normes du **W3C** et des règles d’**accessibilité** (images avec attributs `alt`, structure sémantique, contraste, navigation au clavier, etc.).  
- Respect des contraintes de la SAÉ :  
  - Pas de CMS.  
  - Interdiction de fonctions anonymes et d’imbrication de fonctions en JavaScript.  
  - Utilisation de `for ... of` et de `this` dans les écouteurs.  
  - Utilisation de **PDO** et de requêtes préparées côté PHP.  
  - Conformité **RGPD** (consentement explicite, gestion des données personnelles, droit à l’information).  

Une fiche **qualité / accessibilité / référencement / adaptabilité aux tailles d’écran** a été complétée et jointe au dossier de projet.

---

## 🚀 Installation et déploiement

### Environnement local

1. Installer un serveur local avec PHP et MySQL (ex. XAMPP, WAMP, MAMP).  
2. Créer une base de données via phpMyAdmin et exécuter les scripts SQL fournis.  
3. Placer les fichiers du projet dans le dossier racine du serveur (ex. `htdocs`).  
4. Adapter éventuellement les paramètres de connexion PDO (`config.php`, `base.inc.php`, etc.).  
5. Accéder via `http://localhost/radioweb` (ou le chemin choisi).

### Déploiement sur la‑perso.univ-lemans.fr

1. Se connecter à `la-perso.univ-lemans.fr` via FTP avec comme mot de passe les 6 derniers caractères de ton INE.  
2. Copier le contenu du projet dans le dossier destiné.  
3. Créer la base de données via `https://la-perso.univ-lemans.fr/phpmyadmin/`.  
4. Mettre à jour les paramètres de connexion si nécessaire.  
5. Tester l’application sur mobile et bureau.

---

## 📜 Licence

Ce projet est conçu pour un usage pédagogique dans le cadre du BUT MMI.  
Les droits d’auteur appartiennent aux étudiants auteurs du projet.  
Toute utilisation hors contexte pédagogique doit être explicitement mentionnée et validée.

---

## 👥 Équipe & encadrement

- **Auteurs** :  
  - [Nom 1]  
  - [Nom 2]  
  - [Nom 3]  
- **Formation** : BUT MMI – IUT du Mans  
- **Enseignants** :  
  - 💬 Mme Puizillout – HTML/CSS/JS, accessibilité  
  - 💻 M. Corbière – PHP, base de données, hébergement
