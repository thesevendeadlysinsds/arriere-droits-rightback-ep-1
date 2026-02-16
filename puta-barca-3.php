<?php
$start_time = microtime(true);
set_time_limit(600); // On laisse un peu plus de temps car on va mettre des mini-pauses

// --- CONFIGURATION SAFE ---
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 25;
$baseUrl = "https://www.transfermarkt.fr/spieler-statistik/wertvollstespieler/marktwertetop/mw/spielerposition_id/5";
$uas = [
    // --- WINDOWS (Chrome, Edge, Firefox, Opera) ---
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:122.0) Gecko/20100101 Firefox/122.0",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 OPR/106.0.0.0",
    "Mozilla/5.0 (Windows NT 11.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",

    // --- MAC (Safari, Chrome, Firefox) ---
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3 Safari/605.1.15",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 14_2_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:122.0) Gecko/20100101 Firefox/122.0",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 13_6_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2.1 Safari/605.1.15",

    // --- LINUX (Ubuntu, Fedora, ChromeOS) ---
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
    "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:122.0) Gecko/20100101 Firefox/122.0",
    "Mozilla/5.0 (X11; CrOS x86_64 14541.0.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
    "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:121.0) Gecko/20100101 Firefox/121.0",

    // --- IOS (iPhone, iPad) ---
    "Mozilla/5.0 (iPhone; CPU iPhone OS 17_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (iPad; CPU OS 17_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.3 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 17_2_1 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko) CriOS/121.0.6167.138 Mobile/15E148 Safari/537.36",

    // --- ANDROID (Samsung, Pixel, Xiaomi) ---
    "Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.6167.143 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 13; SM-S911B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.6167.143 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 13; SM-A546B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 13; Redmi Note 12) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.6167.143 Mobile Safari/537.36",

    // --- AUTRES / TABLETTES ---
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 OPR/107.0.0.0",
    "Mozilla/5.0 (Linux; Android 12; Lenovo TB-125FU) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.6167.143 Safari/537.36",
    "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 17_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/122.0 Mobile/15E148 Safari/605.1.15",
    "Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.6167.143 Mobile Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36"
];

// --- FONCTIONS DE NETTOYAGE ---
if (!function_exists('parseMarketValue')) {
    function parseMarketValue($value) {
        $value = str_replace(['m€', 'k€', '€'], ['000000', '000', ''], $value);
        $value = str_replace(',', '.', $value);
        return floatval($value);
    }
}

if (!function_exists('emmanuel_macron_parse')) {
    function emmanuel_macron_parse($html) {
        $res = ["DateOfBirth" => "N/A", "Pied" => "N/A", "equipmnt" => "N/A", "img" => "", "nationalite" => "N/A", "club_img" => "", "noms_pays" => [], "flags" => []];
        if (!$html || strlen($html) < 2000) return $res; 
        
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        
        $queries = [
            "DateOfBirth" => "//span[contains(text(), 'Naissance (âge):')]/following-sibling::span[1]",
            "Pied" => "//span[contains(text(), 'Pied:')]/following-sibling::span[1]",
            "equipmnt" => "//span[contains(text(), 'Équipementier:')]/following-sibling::span[1]"
        ];
        
        foreach ($queries as $key => $q) {
            $node = $xpath->query($q);
            if ($node && $node->length > 0) $res[$key] = trim($node->item(0)->textContent);
        }
        
        // Image joueur
        $imgNode = $xpath->query("//div[@id='modal-1-content']//img | //img[@class='data-header__profile-image']");
        if ($imgNode && $imgNode->length > 0) $res['img'] = str_replace(["header", "small", "medium"], "big", $imgNode->item(0)->getAttribute('src'));
        
        // Image club
        $clubImgNode = $xpath->query("//a[contains(@class, 'data-header__box__club-link')]//img");
        if ($clubImgNode && $clubImgNode->length > 0) {
            $src = $clubImgNode->item(0)->getAttribute('srcset');
            $res['club_img'] = $src ? explode(' ', trim($src))[0] : $clubImgNode->item(0)->getAttribute('src');
        }
        $res["club_img"] = str_replace("small", "normquad", $res["club_img"]);
        
        // Nationalités & Drapeaux
        $natNode = $xpath->query("//span[contains(text(), 'Nationalité:')]/following-sibling::span[1]");
        if ($natNode && $natNode->length > 0) {
            $span = $natNode->item(0);
            foreach ($xpath->query(".//text()[normalize-space()]", $span) as $t) $res['noms_pays'][] = trim($t->textContent);
            foreach ($xpath->query(".//img", $span) as $img) $res['flags'][] = str_replace("tiny", "begegnungslider", $img->getAttribute('src'));
        }
        return $res;
    }
}

if (!function_exists('get_safe_curl')) {
    function get_safe_curl($url, $ua) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, ""); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            "Accept-Language: fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3",
            "Referer: https://www.google.com/"
        ]);
        return $ch;
    }
}

// --- VAGUE 1 : RÉCUPÉRATION DE LA LISTE ---
// --- VAGUE 1 : RÉCUPÉRATION DE LA LISTE ---
function vague1($limit, $baseUrl, $uas) {
    $players = []; // <--- AJOUTE CETTE LIGNE ICI
    $nbPagesNeeded = ceil($limit / 25); 

    for ($p = 1; $p <= $nbPagesNeeded; $p++) {
        // Optionnel : on vérifie si on a déjà assez de joueurs
        if (count($players) >= $limit) break; 

        $url = ($p === 1) ? $baseUrl : $baseUrl . "/ajax/yw1/page/" . $p;
        $ch = get_safe_curl($url, $uas[array_rand($uas)]);
        $htmlList = curl_exec($ch);
        curl_close($ch);

        if (!$htmlList) continue;

        $dom = new DOMDocument();
        @$dom->loadHTML($htmlList);
        $xpath = new DOMXPath($dom);
        $rows = $xpath->query("//table[contains(@class,'items')]/tbody/tr[not(@class='spacer')]");

        if ($rows) {
            foreach ($rows as $row) {
                if (count($players) >= $limit) break; 
                $cols = $xpath->query("td", $row);
                if ($cols->length < 6) continue;

                $nameNode = $xpath->query(".//td[contains(@class,'hauptlink')]//a", $row)->item(0);
                if (!$nameNode) continue;

                $pUrl = "https://www.transfermarkt.fr" . $nameNode->getAttribute('href');
                
                // On évite les doublons
                if (isset($players[$pUrl])) continue;

                preg_match('/\/(\d+)$/', $pUrl, $matches);
                $valNode = $cols->item(5);
                $raw_val = ($valNode !== null) ? trim($valNode->textContent) : '0 €';

                $players[$pUrl] = [
                    'id' => intval($matches[1] ?? 0),
                    'name' => trim($nameNode->textContent),
                    'age' => ($cols->item(2) !== null) ? intval(trim($cols->item(2)->textContent)) : 0,
                    'club' => ($xpath->query(".//a/img", $cols->item(4))->item(0) !== null) ? $xpath->query(".//a/img", $cols->item(4))->item(0)->getAttribute('title') : 'N/A',
                    'market_value' => $raw_val,
                    'value_numeric' => parseMarketValue($raw_val),
                    'url' => $pUrl
                ];
            }
        }
        usleep(500000); 
    }
    return $players; 
}

// --- VAGUE 2 : DÉTAILS (Note le & devant $players pour modifier le tableau original) ---
function vague2(&$players, $uas, $limit) {
    if (empty($players)) return;
    
    $chunks = array_chunk(array_keys($players), 15); 
    foreach ($chunks as $urlBatch) {
        $mh = curl_multi_init();
        $batch_handles = [];

        foreach ($urlBatch as $pUrl) {
            $ch = get_safe_curl($pUrl, $uas[array_rand($uas)]);
            curl_multi_add_handle($mh, $ch);
            $batch_handles[$pUrl] = $ch;
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
            curl_multi_select($mh);
        } while ($active && $mrc == CURLM_OK);

        foreach ($batch_handles as $url => $ch) {
            $html = curl_multi_getcontent($ch);
            $details = emmanuel_macron_parse($html);
            // On fusionne les détails dans le tableau original
            $players[$url] = array_merge($players[$url], $details);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);
        usleep($limit > 100 ? 600000 : 200000); 
    }
}

// --- 1. CONFIGURATION ET CHARGEMENT ---
$cache_file = 'data_rightbacks.json';
$players = [];

// On regarde si on a déjà les données
if (file_exists($cache_file)) {
    $players = json_decode(file_get_contents($cache_file), true);
}

if (empty($players) || isset($_GET['refresh'])) {
    $players_map = vague1($limit, $baseUrl, $uas);
    vague2($players_map, $uas, $limit);
    $players = array_values($players_map);
    file_put_contents($cache_file, json_encode($players));
}

$allowedSorts = ['id', 'name', 'age', 'value_numeric', 'DateOfBirth'];
$sort = in_array($_GET['sort'] ?? '', $allowedSorts) ? $_GET['sort'] : 'value_numeric';

if (!isset($_GET['order'])) {
    $order = ($sort === 'value_numeric' || $sort === 'DateOfBirth') ? 'desc' : 'asc';
} else {
    $order = ($_GET['order'] === 'desc') ? 'desc' : 'asc';
}

$nextOrder = ($order === 'desc') ? 'asc' : 'desc';

usort($players, function ($a, $b) use ($sort, $order) {
    if ($sort === 'DateOfBirth') {
        $fr = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
        $en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $valA = strtotime(str_replace($fr, $en, trim(explode(' (', $a['DateOfBirth'])[0]))) ?: 0;
        $valB = strtotime(str_replace($fr, $en, trim(explode(' (', $b['DateOfBirth'])[0]))) ?: 0;
        return ($order === 'asc') ? $valA <=> $valB : $valB <=> $valA;
    } 
    if (is_numeric($a[$sort] ?? 0) && is_numeric($b[$sort] ?? 0)) {
        $res = $a[$sort] <=> $b[$sort];
    } else {
        $res = strnatcasecmp($a[$sort] ?? '', $b[$sort] ?? '');
    }
    return ($order === 'asc') ? $res : -$res;
});

// $players = array_values($players);

// --- RÉPONSE JSON (S'adapte parfaitement à limit, sort et order) ---
if (isset($_GET['json'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "count" => count($players), 
        "data" => $players
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// AFFICHAGE DE LA CARTE RANDOM
if (isset($_GET["random_rightback"])) {
    $limit_val = isset($_GET['limit']) ? intval($_GET['limit']) : 500;

    if (!empty($players)) {
        $players_limited = array_slice($players, 0, $limit_val);
        $player = $players_limited[array_rand($players_limited)];
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <style>
                body { background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .player-card { border: 1px solid #ddd; border-radius: 15px; padding: 20px; width: 320px; text-align: center; font-family: 'Segoe UI', sans-serif; box-shadow: 0 10px 25px rgba(0,0,0,0.2); background: white; }
                .player-img { width: 200px; border-radius: 12px; margin-bottom: 15px; border: 1px solid #eee; }
                .club-img { width: 45px; vertical-align: middle; margin-right: 8px; }
                .market-value { color: #28a745; font-weight: bold; font-size: 1.5em; display: block; margin: 15px 0; }
                .btn-back { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="player-card"><br><br><br>
                <form method="GET" action="">
                    <?php if(isset($_GET['random_rightback'])): ?>
                        <input type="hidden" name="random_rightback" value="1">
                    <?php endif; ?>

                    <label for="limit-select">Afficher :</label>
                    <select name="limit" id="limit-select" onchange="this.form.submit()" style="margin-top: 10px; padding: 5px; border-radius: 5px;">
                        <?php 
                        // On s'assure que $limit_val existe pour le test 'selected'
                        $current_limit = isset($_GET['limit']) ? intval($_GET['limit']) : 500;
                        
                        for ($i = 25; $i <= 500; $i += 25) { ?>
                            <option value="<?= $i ?>" <?= $current_limit == $i ? 'selected' : '' ?>>
                                Top <?= $i ?> joueurs
                            </option>
                        <?php } ?>
                    </select>
                </form>
                <img src="<?= $player['img'] ?>" class="player-img" alt="<?= $player['name'] ?>">
                
                <h2 style="margin: 5px 0;"><?= $player['name'] ?></h2>

                <p>
                    <img src="<?= $player['club_img'] ?>" class="club-img" title="<?= $player['club'] ?>"> 
                    <strong><?= $player['club'] ?></strong>
                </p>

                <div style="text-align: left; font-size: 0.9em; margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 8px;">
                    <p>🎂 <strong>Naissance :</strong> <?= str_replace(" (".$player['age'].")" , "", $player['DateOfBirth']) ?> (<?= $player['age'] ?> ans)</p>
                    <p>🦶 <strong>Pied :</strong> <?= ucfirst($player['Pied']) ?></p>
                    <p>👟 <strong>Équipement :</strong> <?= $player['equipmnt'] ?></p>
                    
                    <p>🌍 <strong>Nationalité :</strong><br>
                        <?php foreach ($player['flags'] as $index => $flag): ?>
                            <span style="display: inline-flex; align-items: center; margin-right: 5px; background: #eee; padding: 2px 5px; border-radius: 4px; margin-top: 5px;">
                                <img src="<?= $flag ?>" style="width: 20px; margin-right: 5px; border: 1px solid #ccc;">
                                <?= $player['noms_pays'][$index] ?>
                            </span>
                        <?php endforeach; ?>
                    </p>
                </div>

                <span class="market-value"><?= $player['market_value'] ?></span>

                <a href="<?= $player['url'] ?>" target="_blank" class="btn-back">Voir le profil complet</a>
                
                <br>
                <a href="?random_rightback&limit=<?= $limit_val ?>" 
                style="display: inline-block; 
                        margin-top: 20px; 
                        padding: 12px 25px; 
                        background-color: #007bff; 
                        color: white; 
                        text-decoration: none; 
                        border-radius: 10px; 
                        font-weight: bold; 
                        font-family: sans-serif;
                        transition: background 0.3s ease;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
                onmouseover="this.style.backgroundColor='#0056b3'" 
                onmouseout="this.style.backgroundColor='#007bff'">
                    🔄 Tirer un autre joueur (Top <?= $limit_val ?>)
                </a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Aucun joueur trouvé.";
    }
    exit; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Top Arrières Droits - Full Multi-cURL</title>
    <style>
        table { border-collapse: collapse; width: 98%; margin: 20px auto; font-family: Arial, sans-serif; font-size: 13px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { background-color: #0073e6; color: white; }
        th a { color: white; text-decoration: none; display: block; width: 100%; height: 100%; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .player-img { width: 70px; height: auto; border-radius: 4px; border: 1px solid #ddd; }
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 0 10px; display: inline-block; color: white; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Top Arrières Droits (Full cURL Multi)</h1>

<div style="text-align:center; margin: 20px;">
    <a href="" class="btn" style="background: #0073e6;">🔄 Rafraîchir</a>
    <a href="?refresh" class="btn" style="background: #0073e6;">Rafraichir pour de bon</a>
    <a href="?json&sort=<?= $sort ?>&order=<?= $order ?>&limit=<?= $limit ?>" target="_blank" class="btn" style="background: #0073e6;">📄 Voir le JSON</a>
</div>

<div style="text-align:center; margin-bottom: 20px;">
    <label for="limit">Afficher les : </label>
    <select id="limit" 
            onchange="const urlParams = new URLSearchParams(window.location.search); 
                      urlParams.set('limit', this.value); 
                      window.location.search = urlParams.toString();" 
            style="background-color: skyblue;">
        <?php
        $currentLimit = $_GET['limit'] ?? 25;
        for ($i = 25; $i <= 500; $i += 25) {
            $selected = ($currentLimit == $i) ? 'selected' : '';
            echo "<option style='background-color: skyblue;' value='$i' $selected>$i joueurs</option>";
        }
        ?>
    </select>
</div>

<?php
$end_time = microtime(true);
$execution_time = round($end_time - $start_time, 2);
?>

<p style="text-align:center; color: #666; font-style: italic;">
    🚀 Page générée en <?= $execution_time ?> secondes. Soit <?= round($execution_time / count($players), 4) ?> secondes par arrière-droit.
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Photo</th>
            <th><a href="?sort=id&order=<?= ($sort == 'id') ? $nextOrder : 'asc' ?>&limit=<?= $limit ?>">ID</a></th>
            <th>Nom</th>
            <th><a href="?sort=DateOfBirth&order=<?= ($sort == 'DateOfBirth') ? $nextOrder : 'desc' ?>&limit=<?= $limit ?>">Date of birth</a></th>
            <th>Nationalité</th>
            <th>Pied</th>
            <th><a href="?sort=age&order=<?= ($sort == 'age') ? $nextOrder : 'asc' ?>&limit=<?= $limit ?>">Âge</a></th>
            <th>Club</th>
            <th>Équipement</th>
            <th><a href="?sort=value_numeric&order=<?= ($sort == 'value_numeric') ? $nextOrder : 'desc' ?>&limit=<?= $limit ?>">Valeur</a></th>
        </tr>
    </thead>
    <tbody>
        <?php $rank = 1; foreach ($players as $p): ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><img src="<?= $p['img'] ?>" class="player-img"></td>
                <td><?= $p['id'] ?></td>
                <td><a href="<?= $p['url'] ?>" target="_blank" style="color: blue; text-decoration: none;"><b><?= htmlspecialchars($p['name']) ?></b></a></td>
                <td><?= htmlspecialchars($p['DateOfBirth']) ?></td>
                <td>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                        <?php 
                        $totalFlags = count($p['flags'] ?? []); 
                        foreach (($p['flags'] ?? []) as $i => $flag): 
                        ?>
                            <span>
                                <?= htmlspecialchars($p['noms_pays'][$i] ?? '') ?> 
                                <img src="<?= $flag ?>" style="width: 30px; vertical-align: middle; border: 1px solid #eee;">
                            </span>
                            <?php if ($i < $totalFlags - 1): ?>
                                <hr style="width: 100%; border: 0; border-top: 1px solid black; opacity: 1; margin: 2px 0;">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </td>
                <td><?= htmlspecialchars($p['Pied']) ?></td>
                <td><?= $p['age'] ?> ans</td>
                <td><?= htmlspecialchars($p['club']) ?><hr><img src="<?= $p['club_img'] ?>" style="height: 60px;"></td>
                <td><?= htmlspecialchars($p['equipmnt']) ?></td>
                <td style="color: green; font-weight: bold;"><?= $p['market_value'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>