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
    <title>RADIOSITE</title>
    <style>
        /* ── Fonts ─────────────────────────────────────────────── */
        @font-face {
            font-family: 'PixelCrash';
            src: url('fonts/PixelCrash.otf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'BitstreamVeraSansMono';
            src: url('fonts/VeraMono.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'BitstreamVeraSansMono';
            src: url('fonts/VeraMoBd.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        /* ── Reset & Base ───────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:       #FF3B3B;
            --red-dark:  #C42020;
            --red-dim:   rgba(255,59,59,0.12);
            --bg:        #0D0D12;
            --bg2:       #111118;
            --bg3:       #161620;
            --muted:     #6B2525;
            --border:    #FF3B3B;
            --player-h:  110px;
            --mono: 'BitstreamVeraSansMono', 'Courier New', monospace;
            --title: 'PixelCrash', 'Courier New', monospace;
        }

        html, body {
            height: 100%;
            background: var(--bg);
            color: var(--red);
            font-family: var(--mono);
            overflow-x: hidden;
        }

        /* ── Kanji BG ───────────────────────────────────────────── */
        #kanji-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            opacity: 0.15;
            font-family: var(--mono);
            font-size: 17px;
            line-height: 1.55;
            color: var(--red);
            word-break: break-all;
            padding: 6px 10px;
            letter-spacing: 3px;
            user-select: none;
        }

        /* scan-line overlay */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 9999;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 3px,
                rgba(0,0,0,0.07) 3px,
                rgba(0,0,0,0.07) 4px
            );
        }

        /* ── App wrapper ────────────────────────────────────────── */
        #app {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding-bottom: var(--player-h);
        }

        /* ── HEADER ─────────────────────────────────────────────── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 32px;
            border-bottom: 1px solid var(--muted);
            flex-shrink: 0;
        }

        .logo {
            font-family: var(--title);
            font-size: clamp(26px, 4vw, 50px);
            color: var(--red);
            text-decoration: none;
            letter-spacing: 6px;
            text-shadow: 3px 0 0 #8B0000, 5px 0 0 rgba(139,0,0,0.25);
            animation: glitch 8s infinite;
        }

        @keyframes glitch {
            0%,92%,100% {
                text-shadow: 3px 0 0 #8B0000, 5px 0 0 rgba(139,0,0,0.25);
                transform: none;
            }
            93% {
                text-shadow: -4px 0 0 #FF0000, 4px 0 0 #550000;
                clip-path: inset(5% 0 85% 0);
                transform: translateX(-3px);
            }
            94% {
                text-shadow: 4px 0 0 #FF0000, -4px 0 0 #8B0000;
                clip-path: inset(55% 0 15% 0);
                transform: translateX(3px);
            }
            95% {
                text-shadow: 3px 0 0 #8B0000, 5px 0 0 rgba(139,0,0,0.25);
                clip-path: none;
                transform: none;
            }
        }

        nav {
            display: flex;
            align-items: center;
            gap: 36px;
        }

        nav a, .nav-user {
            color: var(--red);
            text-decoration: none;
            font-family: var(--mono);
            font-size: 13px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: opacity .15s;
        }
        nav a:hover { opacity: .55; }
        .nav-user { opacity: .55; pointer-events: none; }

        /* ── FILTER BAR ─────────────────────────────────────────── */
        .filter-bar {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 0;
            border-bottom: 1px solid var(--muted);
        }

        .filter-form {
            display: flex;
            flex: 1;
            align-items: stretch;
        }

        .search-wrap {
            position: relative;
            flex: 1.5;
            border-right: 1px solid var(--muted);
        }

        .search-wrap input {
            width: 100%;
            height: 48px;
            background: transparent;
            border: none;
            color: var(--red);
            font-family: var(--mono);
            font-size: 13px;
            padding: 0 40px 0 18px;
            outline: none;
            letter-spacing: 1px;
        }
        .search-wrap input::placeholder { color: var(--muted); }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            color: var(--red);
            pointer-events: none;
        }

        .filter-select {
            flex: 1;
            height: 48px;
            background: transparent;
            border: none;
            border-left: 1px solid var(--muted);
            color: var(--red);
            font-family: var(--mono);
            font-size: 13px;
            padding: 0 16px;
            outline: none;
            cursor: pointer;
            letter-spacing: 1px;
            -webkit-appearance: none;
            appearance: none;
        }
        .filter-select option { background: var(--bg); color: var(--red); }
        .filter-select:focus { background: var(--red-dim); }

        .btn-filter {
            background: transparent;
            border: none;
            border-left: 1px solid var(--muted);
            color: var(--red);
            font-family: var(--mono);
            font-size: 13px;
            padding: 0 22px;
            cursor: pointer;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            height: 48px;
            transition: background .15s;
        }
        .btn-filter:hover { background: var(--red-dim); }

        .btn-reset {
            display: flex;
            align-items: center;
            height: 48px;
            padding: 0 22px;
            border-left: 1px solid var(--muted);
            color: var(--muted);
            font-family: var(--mono);
            font-size: 13px;
            text-decoration: none;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: color .15s, background .15s;
            flex-shrink: 0;
        }
        .btn-reset:hover { color: var(--red); background: var(--red-dim); }

        .btn-random {
            background: transparent;
            border: none;
            border-left: 1px solid var(--muted);
            color: var(--muted);
            font-family: var(--mono);
            font-size: 13px;
            padding: 0 22px;
            cursor: pointer;
            letter-spacing: 1.5px;
            height: 48px;
            flex-shrink: 0;
            transition: color .15s, background .15s;
        }
        .btn-random:hover { color: var(--red); background: var(--red-dim); }

        /* ── CONTENT ────────────────────────────────────────────── */
        .content {
            flex: 1;
            display: grid;
            grid-template-columns: 320px 1fr;
            min-height: 0;
        }

        /* ── LEFT PANEL ─────────────────────────────────────────── */
        .now-playing-panel {
            border-right: 1px solid var(--muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 36px 24px 24px;
            gap: 20px;
            overflow: hidden;
        }

        /* Red bracket frame with clipped corner */
        .cover-frame {
            position: relative;
            width: 218px;
            height: 218px;
            background: var(--red);
            display: flex;
            align-items: center;
            justify-content: center;
            /* clip bottom-right corner */
            clip-path: polygon(0 0, 100% 0, 100% 75%, 75% 100%, 0 100%);
        }

        .cover-inner {
            width: 190px;
            height: 190px;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            clip-path: polygon(0 0, 100% 0, 100% 78%, 78% 100%, 0 100%);
        }

        .cover-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .now-playing-name {
            font-family: var(--mono);
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            color: var(--red);
            text-align: center;
            word-break: break-word;
        }

        /* ── RADIO LIST (right) ──────────────────────────────────── */
        .radio-list-panel {
            overflow-y: auto;
            margin: 18px 20px;
            border: 1px solid var(--red);
            flex: 1;
        }
        .radio-list-panel::-webkit-scrollbar { width: 5px; }
        .radio-list-panel::-webkit-scrollbar-track { background: transparent; }
        .radio-list-panel::-webkit-scrollbar-thumb { background: var(--red-dark); }

        .radio-item {
            display: block;
            padding: 17px 28px;
            border-bottom: 1px solid rgba(255,59,59,0.12);
            cursor: pointer;
            text-decoration: none;
            color: var(--red);
            transition: background .1s;
        }
        .radio-item:last-child { border-bottom: none; }
        .radio-item:hover, .radio-item.active { background: rgba(255,59,59,0.1); }

        .radio-item-text {
            font-family: var(--mono);
            font-size: clamp(12px, 1.4vw, 16px);
            letter-spacing: 1.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .sep { color: var(--muted); margin: 0 6px; }

        .no-result {
            padding: 24px 28px;
            color: var(--muted);
            font-size: 13px;
            letter-spacing: 1px;
        }

        /* ── PLAYER BAR ─────────────────────────────────────────── */
        #player-bar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: var(--player-h);
            background: var(--bg2);
            border-top: 2px solid var(--red);
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 0 28px;
            z-index: 1000;
        }

        audio { display: none; }

        .ctrl-btn {
            background: none;
            border: none;
            color: var(--red);
            cursor: pointer;
            font-size: 18px;
            padding: 4px 2px;
            line-height: 1;
            transition: opacity .15s;
            font-family: var(--mono);
        }
        .ctrl-btn:hover { opacity: .5; }
        .ctrl-btn.fav { font-size: 22px; }

        .player-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .player-meta {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }

        .player-name {
            font-family: var(--mono);
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 2px;
            color: var(--red);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .player-status {
            font-size: 10px;
            letter-spacing: 1.5px;
            color: var(--muted);
        }

        .time-section {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .time-lbl {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 1px;
            flex-shrink: 0;
            font-variant-numeric: tabular-nums;
        }

        .progress {
            flex: 1;
            height: 3px;
            background: var(--muted);
            position: relative;
            cursor: pointer;
        }
        .progress-fill {
            height: 100%;
            background: var(--red);
            width: 0%;
        }
        .progress-thumb {
            position: absolute;
            top: 50%;
            left: 0%;
            width: 11px;
            height: 11px;
            background: var(--bg);
            border: 2px solid var(--red);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        /* EQ bars */
        .eq {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 30px;
            flex-shrink: 0;
        }
        .eq-b {
            width: 5px;
            background: var(--red);
            border-radius: 1px 1px 0 0;
            animation: eqA var(--d,.6s) ease-in-out infinite alternate;
        }
        @keyframes eqA {
            from { height: var(--lo,4px); opacity:.55; }
            to   { height: var(--hi,24px); opacity:1; }
        }
        .eq-b:nth-child(1){ --d:.5s;  --lo:5px;  --hi:18px; }
        .eq-b:nth-child(2){ --d:.7s;  --lo:12px; --hi:26px; }
        .eq-b:nth-child(3){ --d:.42s; --lo:20px; --hi:10px; }
        .eq-b:nth-child(4){ --d:.9s;  --lo:7px;  --hi:22px; }
        .eq-b.off { animation-play-state: paused; height: 3px !important; }

        /* ── Responsive ─────────────────────────────────────────── */
        @media (max-width: 800px) {
            .content { grid-template-columns: 1fr; }
            .now-playing-panel { display: none; }
            header { padding: 12px 16px; }
            nav { gap: 18px; }
            .btn-random { display: none; }
            #player-bar { gap: 14px; padding: 0 14px; }
            .player-name { max-width: 90px; }
        }
    </style>
</head>
<body>

<div id="kanji-bg" aria-hidden="true"></div>

<div id="app">

    <!-- HEADER -->
    <header>
        <a href="index.php" class="logo">RADIOSITE</a>
        <nav>
            <?php if (estConnecte()): ?>
                <span class="nav-user"><?php echo htmlspecialchars(utilisateurPseudo()); ?></span>
                <a href="profil.php">Profil</a>
                <a href="favoris.php">Favoris</a>
                <a href="reclamation.php">Réclamation</a>
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="reclamation.php">Réclamation</a>
                <a href="inscription.php">Inscription</a>
                <a href="connexion.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- FILTER BAR -->
    <div class="filter-bar" role="search">
        <form class="filter-form" action="index.php" method="get">
            <div class="search-wrap">
                <input type="text" name="recherche" id="recherche"
                       placeholder="Recherche..."
                       value="<?php echo htmlspecialchars($_GET['recherche'] ?? ''); ?>"
                       autocomplete="off"
                       aria-label="Rechercher une radio">
                <span class="search-icon" aria-hidden="true">&#128269;</span>
            </div>

            <select name="style_id" id="style_id" class="filter-select"
                    aria-label="Filtrer par style" onchange="this.form.submit()">
                <option value="">Style...</option>
                <?php foreach ($styles as $s): ?>
                    <option value="<?php echo (int)$s['style_id']; ?>"
                        <?php echo ($styleId === (int)$s['style_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($s['style_nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="pays_id" id="pays_id" class="filter-select"
                    aria-label="Filtrer par pays" onchange="this.form.submit()">
                <option value="">Pays...</option>
                <?php foreach ($pays as $p): ?>
                    <option value="<?php echo (int)$p['pays_id']; ?>"
                        <?php echo ($paysId === (int)$p['pays_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['pays_nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-filter">Filtrer</button>
            <a href="index.php" class="btn-reset">Réinitialiser</a>
        </form>

        <form action="index.php" method="get">
            <button type="submit" name="aleatoire" value="1" class="btn-random"
                    aria-label="Radio aléatoire">&#127922; Aléatoire</button>
        </form>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- Left: now playing -->
        <aside class="now-playing-panel" aria-label="Radio en cours de lecture">
            <div class="cover-frame">
                <div class="cover-inner" id="cover-inner">
                    <!-- image injected by JS -->
                </div>
            </div>
            <div class="now-playing-name" id="now-playing-name" aria-live="polite">
                <?php
                if ($radioAleatoire) {
                    echo htmlspecialchars($radioAleatoire['radio_nom']);
                } else {
                    echo '— Aucune lecture —';
                }
                ?>
            </div>
        </aside>

        <!-- Right: list -->
        <main>
            <div class="radio-list-panel" role="list" aria-label="Liste des radios">
                <?php if (empty($radios)): ?>
                    <div class="no-result">Aucune radio trouvée.</div>
                <?php else: ?>
                    <?php foreach ($radios as $radio): ?>
                    <a href="radio.php?id=<?php echo (int)$radio['radio_id']; ?>"
                       class="radio-item"
                       role="listitem"
                       data-id="<?php echo (int)$radio['radio_id']; ?>"
                       data-url="<?php echo htmlspecialchars($radio['radio_url_hq']); ?>"
                       data-name="<?php echo htmlspecialchars($radio['radio_nom']); ?>"
                       data-img="<?php echo htmlspecialchars($radio['radio_img'] ?? ''); ?>"
                       onclick="playRadio(event, this)"
                       aria-label="<?php echo htmlspecialchars($radio['radio_nom']); ?>">
                        <span class="radio-item-text">
                            <?php echo htmlspecialchars($radio['radio_nom']); ?><span class="sep">:</span><?php echo htmlspecialchars($radio['pays_nom'] ?? 'N/A'); ?><span class="sep">:</span><?php echo htmlspecialchars($radio['style_nom'] ?? 'N/A'); ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>

    </div>
</div>

<!-- PLAYER BAR -->
<div id="player-bar" role="region" aria-label="Lecteur audio">
    <audio id="audio-el" preload="none"></audio>

    <div class="player-controls">
        <button class="ctrl-btn fav" id="btn-fav" onclick="toggleFav()"
                aria-label="Favori" title="Ajouter aux favoris">&#9733;</button>
        <button class="ctrl-btn" id="btn-prev" onclick="prevRadio()"
                aria-label="Radio précédente" title="Précédente">&#9664;&#9664;</button>
        <button class="ctrl-btn" id="btn-play" onclick="togglePlay()"
                aria-label="Lecture / Pause" title="Lecture">&#9654;</button>
        <button class="ctrl-btn" id="btn-next" onclick="nextRadio()"
                aria-label="Radio suivante" title="Suivante">&#9654;&#9654;</button>
    </div>

    <div class="player-meta">
        <div class="player-name" id="player-name" aria-live="polite">—</div>
        <div class="player-status" id="player-status">EN ATTENTE</div>
    </div>

    <div class="time-section">
        <span class="time-lbl" id="t-cur">00:00</span>
        <div class="progress" id="progress" onclick="seekBar(event)"
             role="slider" aria-label="Progression de lecture">
            <div class="progress-fill" id="prog-fill"></div>
            <div class="progress-thumb" id="prog-thumb"></div>
        </div>
        <span class="time-lbl" id="t-tot">00:00</span>
    </div>

    <div class="eq" aria-hidden="true">
        <div class="eq-b off"></div>
        <div class="eq-b off"></div>
        <div class="eq-b off"></div>
        <div class="eq-b off"></div>
    </div>
</div>

<script>
/* ── Kanji background ─────────────────────────────────── */
(function buildKanji() {
    var chars = '史をけの年月秀賞出症と神や走杯っ引裂。場ら駒本産行のでいとかにう牧担のはまさたり見頭物りか廃続牡代たあ典離後にを発症集めだ安競馬炎が届0123456789重馬力さ';
    var el = document.getElementById('kanji-bg');
    var cols = Math.ceil(window.innerWidth / 20);
    var rows = Math.ceil(window.innerHeight / 28);
    var str = '';
    for (var r = 0; r < rows; r++) {
        for (var c = 0; c < cols; c++) {
            str += chars[Math.floor(Math.random() * chars.length)];
        }
        str += '\n';
    }
    el.textContent = str;
}());

/* ── State ────────────────────────────────────────────── */
var radios = [];
var currentIndex = -1;
var isPlaying = false;
var audio = document.getElementById('audio-el');

/* Collect radio data from DOM */
(function collectRadios() {
    var items = document.querySelectorAll('.radio-item[data-url]');
    for (var i = 0; i < items.length; i++) {
        radios.push({
            id:   items[i].dataset.id,
            url:  items[i].dataset.url,
            name: items[i].dataset.name,
            img:  items[i].dataset.img,
            el:   items[i]
        });
    }
}());

/* ── Playback ─────────────────────────────────────────── */
function playRadio(event, el) {
    event.preventDefault();
    var idx = -1;
    for (var i = 0; i < radios.length; i++) {
        if (radios[i].el === el) { idx = i; break; }
    }
    if (idx !== -1) loadAndPlay(idx);
}

function loadAndPlay(idx) {
    if (idx < 0 || idx >= radios.length) return;

    /* Deselect old */
    if (currentIndex >= 0) radios[currentIndex].el.classList.remove('active');

    currentIndex = idx;
    var r = radios[idx];
    r.el.classList.add('active');

    /* Cover art */
    var ci = document.getElementById('cover-inner');
    ci.innerHTML = '';
    if (r.img) {
        var img = document.createElement('img');
        img.src = r.img;
        img.alt = r.name;
        ci.appendChild(img);
    }

    /* Labels */
    document.getElementById('now-playing-name').textContent = r.name;
    document.getElementById('player-name').textContent = r.name;
    document.getElementById('player-status').textContent = 'CHARGEMENT...';

    /* Audio */
    audio.src = r.url;
    audio.load();
    audio.play().then(function() {
        isPlaying = true;
        updatePlayBtn();
        document.getElementById('player-status').textContent = 'EN DIRECT';
        setEq(true);
    }).catch(function() {
        document.getElementById('player-status').textContent = 'ERREUR';
        isPlaying = false;
        setEq(false);
        updatePlayBtn();
    });
}

function togglePlay() {
    if (currentIndex === -1) {
        if (radios.length > 0) loadAndPlay(0);
        return;
    }
    if (isPlaying) {
        audio.pause();
        isPlaying = false;
        document.getElementById('player-status').textContent = 'PAUSE';
        setEq(false);
    } else {
        audio.play();
        isPlaying = true;
        document.getElementById('player-status').textContent = 'EN DIRECT';
        setEq(true);
    }
    updatePlayBtn();
}

function prevRadio() {
    var idx = currentIndex - 1;
    if (idx < 0) idx = radios.length - 1;
    loadAndPlay(idx);
}

function nextRadio() {
    var idx = currentIndex + 1;
    if (idx >= radios.length) idx = 0;
    loadAndPlay(idx);
}

function updatePlayBtn() {
    document.getElementById('btn-play').textContent = isPlaying ? '⏸' : '▶';
}

function setEq(on) {
    var bars = document.querySelectorAll('.eq-b');
    for (var i = 0; i < bars.length; i++) {
        if (on) bars[i].classList.remove('off');
        else    bars[i].classList.add('off');
    }
}

/* Favori (cosmétique ici, vrai POST sur radio.php) */
function toggleFav() {
    if (currentIndex === -1) return;
    var btn = document.getElementById('btn-fav');
    btn.style.opacity = btn.style.opacity === '0.35' ? '1' : '0.35';
}

/* Progress bar */
audio.addEventListener('timeupdate', function() {
    if (!audio.duration || !isFinite(audio.duration)) return;
    var pct = (audio.currentTime / audio.duration) * 100;
    document.getElementById('prog-fill').style.width  = pct + '%';
    document.getElementById('prog-thumb').style.left  = pct + '%';
    document.getElementById('t-cur').textContent = fmtTime(audio.currentTime);
    document.getElementById('t-tot').textContent = fmtTime(audio.duration);
});

function seekBar(event) {
    var bar  = document.getElementById('progress');
    var rect = bar.getBoundingClientRect();
    var pct  = (event.clientX - rect.left) / rect.width;
    if (audio.duration && isFinite(audio.duration)) {
        audio.currentTime = pct * audio.duration;
    }
}

function fmtTime(s) {
    s = Math.floor(s);
    var m = Math.floor(s / 60);
    s = s % 60;
    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
}

audio.addEventListener('error', function() {
    document.getElementById('player-status').textContent = 'ERREUR FLUX';
    isPlaying = false;
    setEq(false);
    updatePlayBtn();
});

/* Auto-play si radio aléatoire */
<?php if ($radioAleatoire): ?>
(function autoPlay() {
    var targetId = '<?php echo (int)$radioAleatoire['radio_id']; ?>';
    var idx = -1;
    for (var i = 0; i < radios.length; i++) {
        if (radios[i].id == targetId) { idx = i; break; }
    }
    if (idx !== -1) loadAndPlay(idx);
}());
<?php endif; ?>
</script>

</body>
</html>