<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("
    SELECT r.*, u.nom AS client_nom, p.nom AS produit_nom
    FROM reservations r
    JOIN utilisateurs u ON r.utilisateur_id = u.id
    JOIN produits p ON r.produit_id = p.id
    ORDER BY r.id DESC
");


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Réservations - Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex bg-gray-100">
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-4">Réservations clients</h1>

        <table class="w-full bg-white shadow rounded">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Client</th>
                    <th class="p-2">Produit</th>
                    <th class="p-2">Quantité</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $result->fetch_assoc()): ?>
                    <tr class="border-t">
                        <td class="p-2"><?= $r['id'] ?></td>
                        <td class="p-2"><?= htmlspecialchars($r['client_nom']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($r['produit_nom']) ?></td>
                        <td class="p-2"><?= $r['quantite'] ?></td>
                        <td class="p-2"><?= $r['date_reservation'] ?></td>
                        <td class="p-2"><?= $r['statut'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>