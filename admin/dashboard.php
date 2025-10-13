<?php
session_start();
require_once "../config/database.php";

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Exemple de récupération rapide de statistiques
// Total des produits
$result = $conn->query("SELECT COUNT(*) as total_produits FROM produits");
$produits = $result->fetch_assoc()['total_produits'];

// Total des réservations
$result = $conn->query("SELECT COUNT(*) as total_reservations FROM reservations");
$reservations = $result->fetch_assoc()['total_reservations'];

// Total des utilisateurs clients
$result = $conn->query("SELECT COUNT(*) as total_clients FROM utilisateurs WHERE role='client'");
$clients = $result->fetch_assoc()['total_clients'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex h-screen bg-gray-100 font-sans">

    <?php include "includes/sidebar.php"; ?>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-auto">
        <h2 class="text-3xl font-bold mb-6">Bienvenue, <?= htmlspecialchars($_SESSION['admin_nom']) ?></h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat Produits -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-gray-500 text-sm font-medium">Produits</h3>
                <p class="mt-2 text-2xl font-bold"><?= $produits ?></p>
            </div>

            <!-- Stat Réservations -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-gray-500 text-sm font-medium">Réservations</h3>
                <p class="mt-2 text-2xl font-bold"><?= $reservations ?></p>
            </div>

            <!-- Stat Clients -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-gray-500 text-sm font-medium">Clients</h3>
                <p class="mt-2 text-2xl font-bold"><?= $clients ?></p>
            </div>
        </div>

        <!-- Section d’exemple supplémentaire -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Dernières actions</h3>
            <p class="text-gray-600">Ici tu peux ajouter des graphiques, listes de produits récents, réservations ou alertes.</p>
        </div>
    </main>

</body>
</html>
