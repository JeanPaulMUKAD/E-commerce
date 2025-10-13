<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

// Ajouter un produit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $description = trim($_POST["description"]);
    $prix = floatval($_POST["prix"]);
    $quantite = intval($_POST["quantite"]);
    $categorie = trim($_POST["categorie"]);

    // Upload d'image
    $image = null;
    if (!empty($_FILES["image"]["name"])) {
        $image = "uploads/" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("INSERT INTO produits (nom, description, prix, quantite, categorie, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiis", $nom, $description, $prix, $quantite, $categorie, $image);

    if ($stmt->execute()) {
        $message = "Produit ajouté avec succès ✅";
    } else {
        $message = "Erreur lors de l’ajout du produit ❌";
    }
}

// Liste des produits
$result = $conn->query("SELECT * FROM produits ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits - Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">

    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-4">Gestion des produits</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $message ?></div>
        <?php endif; ?>

        <!-- Formulaire d’ajout -->
        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-lg font-semibold mb-3">Ajouter un produit</h2>
            <input type="text" name="nom" placeholder="Nom" class="border p-2 rounded w-full mb-2" required>
            <textarea name="description" placeholder="Description" class="border p-2 rounded w-full mb-2"></textarea>
            <input type="number" step="0.01" name="prix" placeholder="Prix" class="border p-2 rounded w-full mb-2" required>
            <input type="number" name="quantite" placeholder="Quantité" class="border p-2 rounded w-full mb-2" required>
            <input type="text" name="categorie" placeholder="Catégorie" class="border p-2 rounded w-full mb-2">
            <input type="file" name="image" class="border p-2 rounded w-full mb-3">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Publier</button>
        </form>

        <!-- Liste des produits -->
        <table class="w-full bg-white shadow rounded">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Nom</th>
                    <th class="p-2">Prix</th>
                    <th class="p-2">Quantité</th>
                    <th class="p-2">Catégorie</th>
                    <th class="p-2">Image</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $result->fetch_assoc()): ?>
                <tr class="border-t">
                    <td class="p-2"><?= $p['id'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['nom']) ?></td>
                    <td class="p-2"><?= $p['prix'] ?> $</td>
                    <td class="p-2"><?= $p['quantite'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['categorie']) ?></td>
                    <td class="p-2"><img src="<?= $p['image'] ?>" alt="" class="w-12 h-12 object-cover"></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
