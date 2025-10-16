<?php
session_start();
require_once "../config/database.php";

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Déterminer le mois sélectionné (par défaut : mois actuel)
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// Fonction pour obtenir le nom du mois
function getMonthName($num) {
    $mois = [
        1 => "Janvier", 2 => "Février", 3 => "Mars", 4 => "Avril", 5 => "Mai", 6 => "Juin",
        7 => "Juillet", 8 => "Août", 9 => "Septembre", 10 => "Octobre", 11 => "Novembre", 12 => "Décembre"
    ];
    return $mois[$num] ?? "";
}

// Requêtes filtrées par mois sélectionné
$stmt = $conn->prepare("SELECT COUNT(*) as total_produits FROM produits WHERE MONTH(date_creation) = ?");
$stmt->bind_param("i", $selected_month);
$stmt->execute();
$produits = $stmt->get_result()->fetch_assoc()['total_produits'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_reservations FROM reservations WHERE MONTH(date_reservation) = ?");
$stmt->bind_param("i", $selected_month);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_assoc()['total_reservations'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_clients FROM utilisateurs WHERE role='client' AND MONTH(date_creation) = ?");
$stmt->bind_param("i", $selected_month);
$stmt->execute();
$clients = $stmt->get_result()->fetch_assoc()['total_clients'];

// Fréquence sur 6 derniers mois
$produits_freq = $conn->query("SELECT MONTH(date_creation) as mois, COUNT(*) as total 
                               FROM produits 
                               WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                               GROUP BY MONTH(date_creation) ORDER BY mois ASC")->fetch_all(MYSQLI_ASSOC);

$reservations_freq = $conn->query("SELECT MONTH(date_reservation) as mois, COUNT(*) as total 
                                   FROM reservations 
                                   WHERE date_reservation >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                   GROUP BY MONTH(date_reservation) ORDER BY mois ASC")->fetch_all(MYSQLI_ASSOC);

$clients_freq = $conn->query("SELECT MONTH(date_creation) as mois, COUNT(*) as total 
                              FROM utilisateurs 
                              WHERE role='client' AND date_creation >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                              GROUP BY MONTH(date_creation) ORDER BY mois ASC")->fetch_all(MYSQLI_ASSOC);

function formatFrequency($data) {
    $labels = [];
    $values = [];
    foreach ($data as $row) {
        $labels[] = date("M", mktime(0,0,0,$row['mois'],1));
        $values[] = $row['total'];
    }
    return ['labels'=>$labels, 'values'=>$values];
}

$produits_chart = formatFrequency($produits_freq);
$reservations_chart = formatFrequency($reservations_freq);
$clients_chart = formatFrequency($clients_freq);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="flex h-screen bg-gray-100 font-sans">

    <?php include "includes/sidebar.php"; ?>

    <main class="flex-1 p-8 overflow-auto">

        <!-- Bandeau de filtrage -->
        <div class="bg-white shadow-sm rounded-lg p-5 mb-8 flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-purple-600"></i>
                Tableau de bord — <span class="text-purple-700"><?= getMonthName($selected_month) ?></span>
            </h2>

            <form method="GET" class="flex items-center gap-3">
                <label for="month" class="font-medium text-gray-700">Filtrer par mois :</label>
                <select name="month" id="month" onchange="this.form.submit()" 
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 shadow-sm focus:ring-2 focus:ring-purple-500">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == $selected_month ? 'selected' : '' ?>>
                            <?= getMonthName($m) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <!-- Grille principale -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Cartes statistiques -->
            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Produits -->
                <div class="bg-white shadow-lg rounded-xl p-6 border-t-4 border-purple-600 hover:shadow-xl transition">
                    <div class="flex items-center gap-4">
                        <div class="p-4 bg-purple-100 text-purple-700 rounded-full">
                            <i class="fa-solid fa-boxes-stacked fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="text-gray-500 text-sm uppercase tracking-wider">Produits enregistrés</h3>
                            <p class="mt-2 text-3xl font-bold text-gray-800"><?= $produits ?></p>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-400 text-sm">
                        Fréquence (6 derniers mois) :
                        <span class="block mt-1 text-xs text-gray-500">
                            <?php foreach($produits_chart['labels'] as $i => $month): ?>
                                <?= $month ?> (<?= $produits_chart['values'][$i] ?>)
                                <?= $i < count($produits_chart['labels'])-1 ? '· ' : '' ?>
                            <?php endforeach; ?>
                        </span>
                    </p>
                </div>

                <!-- Réservations -->
                <div class="bg-white shadow-lg rounded-xl p-6 border-t-4 border-green-600 hover:shadow-xl transition">
                    <div class="flex items-center gap-4">
                        <div class="p-4 bg-green-100 text-green-700 rounded-full">
                            <i class="fa-solid fa-calendar-check fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="text-gray-500 text-sm uppercase tracking-wider">Réservations faites</h3>
                            <p class="mt-2 text-3xl font-bold text-gray-800"><?= $reservations ?></p>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-400 text-sm">
                        Fréquence (6 derniers mois) :
                        <span class="block mt-1 text-xs text-gray-500">
                            <?php foreach($reservations_chart['labels'] as $i => $month): ?>
                                <?= $month ?> (<?= $reservations_chart['values'][$i] ?>)
                                <?= $i < count($reservations_chart['labels'])-1 ? '· ' : '' ?>
                            <?php endforeach; ?>
                        </span>
                    </p>
                </div>

                <!-- Clients -->
                <div class="bg-white shadow-lg rounded-xl p-6 border-t-4 border-blue-600 hover:shadow-xl transition">
                    <div class="flex items-center gap-4">
                        <div class="p-4 bg-blue-100 text-blue-700 rounded-full">
                            <i class="fa-solid fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="text-gray-500 text-sm uppercase tracking-wider">Nouveaux clients</h3>
                            <p class="mt-2 text-3xl font-bold text-gray-800"><?= $clients ?></p>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-400 text-sm">
                        Fréquence (6 derniers mois) :
                        <span class="block mt-1 text-xs text-gray-500">
                            <?php foreach($clients_chart['labels'] as $i => $month): ?>
                                <?= $month ?> (<?= $clients_chart['values'][$i] ?>)
                                <?= $i < count($clients_chart['labels'])-1 ? '· ' : '' ?>
                            <?php endforeach; ?>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Profil Admin -->
            <div class="bg-white shadow-lg rounded-xl p-6 flex flex-col items-center text-center border-t-4 border-purple-700">
                <div class="w-24 h-24 mb-4 rounded-full border-2 border-purple-700 flex items-center justify-center bg-gray-50 text-purple-700 text-5xl">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700"><?= htmlspecialchars($_SESSION['admin_nom']) ?></h3>
                <p class="text-gray-500 mb-2"><?= htmlspecialchars($_SESSION['user_role']) ?></p>
                <p class="text-gray-400 text-sm mb-4">Connecté depuis <?= date("d/m/Y") ?></p>
                <a href="connexion/logout.php" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg transition">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i> Déconnexion
                </a>
            </div>

        </div>
    </main>

           <!-- SEARCH LOGO -->
    <script>
        let a = 0;
        let masque = document.createElement('div');
        let logo = document.createElement('img');
        let cercle = document.createElement('div');

        let angle = 0;
        let scale = 1;
        let opacityLogo = 1;

        window.addEventListener('load', () => {
            a = 1;

            // Le cercle et le logo commencent à bouger immédiatement
            anime = setInterval(() => {
                angle += 10; // Vitesse de rotation du cercle
                cercle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;

                // Zoom progressif du logo
                scale += 0.005;
                opacityLogo -= 0.005;

                logo.style.transform = `scale(${scale})`;
                logo.style.opacity = opacityLogo;

            }, 20);

            // Après 1 seconde, on arrête l'animation
            setTimeout(() => {
                clearInterval(anime);
                masque.style.opacity = '0';
            }, 1000);

            setTimeout(() => {
                masque.style.visibility = 'hidden';
            }, 1500);
        });

        // Création du masque
        masque.style.width = '100%';
        masque.style.height = '100vh';
        masque.style.zIndex = 100000;
        masque.style.background = '#ffffff';
        masque.style.position = 'fixed';
        masque.style.top = '0';
        masque.style.left = '0';
        masque.style.opacity = '1';
        masque.style.transition = '0.5s ease';
        masque.style.display = 'flex';
        masque.style.justifyContent = 'center';
        masque.style.alignItems = 'center';
        document.body.appendChild(masque);

        // Création du logo
        logo.setAttribute('src', 'https://previews.123rf.com/images/lightstudio/lightstudio1907/lightstudio190700204/126519016-real-estate-construction-logo-design-vector-template-house-and-building-with-blue-grey-color.jpg');
        logo.style.width = '10vh';
        logo.style.height = '10vh';
        logo.style.position = 'relative';
        logo.style.zIndex = '2';
        logo.style.transition = '0.2s'; // Transition pour plus de fluidité
        masque.appendChild(logo);

        // Création du cercle autour du logo
        cercle.style.width = '15vh';
        cercle.style.height = '15vh';
        cercle.style.border = '3px solid #2F1C6A';
        cercle.style.borderTop = '3px solid #977aecff;';
        cercle.style.borderRadius = '50%';
        cercle.style.position = 'absolute';
        cercle.style.top = '50%';
        cercle.style.left = '50%';
        cercle.style.transform = 'translate(-50%, -50%)';
        cercle.style.boxSizing = 'border-box';
        cercle.style.zIndex = '1';
        masque.appendChild(cercle);

        // Variables de l'animation
        let anime;
    </script>
</body>
</html>
