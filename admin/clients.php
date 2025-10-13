<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM utilisateurs WHERE role = 'client' ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Clients - Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-4">Liste des clients</h1>

        <table class="w-full bg-white shadow rounded">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Nom</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Téléphone</th>
                    <th class="p-2">Date d’inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = $result->fetch_assoc()): ?>
                <tr class="border-t">
                    <td class="p-2"><?= $c['id'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($c['nom']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($c['email']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($c['telephone']) ?></td>
                    <td class="p-2"><?= $c['date_creation'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
