<?php
    session_start();
    require_once "../config/database.php";

    // Vérifie si l'admin est connecté
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();
    }

    // Vérifie si le dossier uploads existe
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    $message = "";

    // === AJOUT D’UN PRODUIT ===
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajouter'])) {
        $nom = trim($_POST["nom"]);
        $description = trim($_POST["description"]);
        $prix = floatval($_POST["prix"]);
        $quantite = intval($_POST["quantite"]);
        $categorie = trim($_POST["categorie"]);

        $image = null;
        if (!empty($_FILES["image"]["name"])) {
            $image = "uploads/" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        }

        $stmt = $conn->prepare("INSERT INTO produits (nom, description, prix, quantite, categorie, image, date_creation) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssdss", $nom, $description, $prix, $quantite, $categorie, $image);

        if ($stmt->execute()) {
            $message = "✅ Produit ajouté avec succès.";
        } else {
            $message = "❌ Erreur lors de l’ajout du produit.";
        }
    }

    // === MODIFICATION D’UN PRODUIT ===
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['modifier'])) {
        $id = intval($_POST["id"]);
        $nom = trim($_POST["nom"]);
        $description = trim($_POST["description"]);
        $prix = floatval($_POST["prix"]);
        $quantite = intval($_POST["quantite"]);
        $categorie = trim($_POST["categorie"]);

        $image = $_POST["image_actuelle"];
        if (!empty($_FILES["image"]["name"])) {
            $image = "uploads/" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        }

        // ✅ Bonne syntaxe
        $stmt = $conn->prepare("UPDATE produits SET nom=?, description=?, prix=?, quantite=?, categorie=?, image=?, date_modification=NOW() WHERE id=?");
        $stmt->bind_param("sssdssi", $nom, $description, $prix, $quantite, $categorie, $image, $id);

        if ($stmt->execute()) {
            $message = "✅ Produit modifié avec succès.";
        } else {
            $message = "❌ Erreur lors de la modification du produit.";
        }
    }

    // === SUPPRESSION D’UN PRODUIT ===
    if (isset($_GET['supprimer'])) {
        $id = intval($_GET['supprimer']);
        $conn->query("DELETE FROM produits WHERE id=$id");
        $message = "🗑️ Produit supprimé avec succès.";
    }

    // === LISTE DES PRODUITS ===
    $result = $conn->query("SELECT * FROM produits ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Produits - Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2d9b2e6c1.js" crossorigin="anonymous"></script>
</head>

<body class="flex bg-gray-100" style="font-family: 'DM Sans', sans-serif;">

    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Gestion des produits</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $message ?></div>
        <?php endif; ?>

        <!-- Formulaire d’ajout -->
        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-lg font-semibold mb-3 text-blue-700">➕ Ajouter un produit</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <input type="text" name="nom" placeholder="Nom du produit"
                    class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500" required>
                <input type="text" name="categorie" placeholder="Catégorie"
                    class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500">
                <textarea name="description" placeholder="Description"
                    class="border p-2 rounded w-full md:col-span-2 focus:ring-2 focus:ring-blue-500"></textarea>
                <input type="number" min="1" step="0.01" name="prix" placeholder="Prix ($)"
                    class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500" required>
                <input type="number" min="1" name="quantite" placeholder="Quantité"
                    class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500" required>
                <input type="file" name="image"
                    class="border p-2 rounded w-full md:col-span-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" name="ajouter"
                class="mt-4 bg-bue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Publier le produit
            </button>
        </form>

        <!-- Liste des produits -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-lg font-semibold mb-3 text-blue-700">📦 Liste des produits</h2>
            <table class="w-full border-collapse text-sm">
                <thead class="bg-blue-100 text-blue-800">
                    <tr>
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Nom</th>
                        <th class="p-2 border">Prix</th>
                        <th class="p-2 border">Qté</th>
                        <th class="p-2 border">Catégorie</th>
                        <th class="p-2 border">Image</th>
                        <th class="p-2 border">Créé le</th>
                        <th class="p-2 border">Modifié le</th>
                        <th class="p-2 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = $result->fetch_assoc()): ?>
                        <tr class="border hover:bg-gray-50">
                            <td class="p-2 border text-center"><?= $p['id'] ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($p['nom']) ?></td>
                            <td class="p-2 border"><?= $p['prix'] ?> $</td>
                            <td class="p-2 border"><?= $p['quantite'] ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($p['categorie']) ?></td>
                            <td class="p-2 border text-center">
                                <img src="<?= $p['image'] ?>" alt="" class="w-12 h-12 object-cover mx-auto rounded">
                            </td>
                            <td class="p-2 border"><?= $p['date_creation'] ?></td>
                            <td class="p-2 border"><?= $p['date_modification'] ?: '-' ?></td>
                            <td class="p-2 border text-center">
                                <a href="?edit=<?= $p['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button onclick="openDeleteModal(<?= $p['id'] ?>)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Formulaire de modification -->
        <?php if (isset($_GET['edit'])):
            $id = intval($_GET['edit']);
            $prod = $conn->query("SELECT * FROM produits WHERE id=$id")->fetch_assoc();
            ?>
            <div class="mt-8 bg-yellow-50 border border-yellow-300 p-6 rounded shadow-lg">
                <h2 class="text-lg font-semibold mb-4 text-yellow-700">✏️ Modifier le produit</h2>
                <form method="POST" enctype="multipart/form-data" class="space-y-3">
                    <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                    <input type="hidden" name="image_actuelle" value="<?= $prod['image'] ?>">

                    <div class="grid md:grid-cols-2 gap-4">
                        <input type="text" name="nom" value="<?= htmlspecialchars($prod['nom']) ?>"
                            class="border p-2 rounded w-full focus:ring-2 focus:ring-yellow-400" required>
                        <input type="text" name="categorie" value="<?= htmlspecialchars($prod['categorie']) ?>"
                            class="border p-2 rounded w-full focus:ring-2 focus:ring-yellow-400">
                        <textarea name="description"
                            class="border p-2 rounded w-full md:col-span-2 focus:ring-2 focus:ring-yellow-400"><?= htmlspecialchars($prod['description']) ?></textarea>
                        <input type="number" min="1" step="0.01" name="prix" value="<?= $prod['prix'] ?>"
                            class="border p-2 rounded w-full focus:ring-2 focus:ring-yellow-400" required>
                        <input type="number" min="1" name="quantite" value="<?= $prod['quantite'] ?>"
                            class="border p-2 rounded w-full focus:ring-2 focus:ring-yellow-400" required>
                        <input type="file" name="image"
                            class="border p-2 rounded w-full md:col-span-2 focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <button type="submit" name="modifier"
                            class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600 transition">
                            Mettre à jour
                        </button>
                        <a href="produits.php" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left"></i>
                            Annuler</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </div>
    <!-- 🗑️ Modale de confirmation de suppression -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Confirmation</h2>
            <p class="text-gray-600 mb-6">Voulez-vous vraiment supprimer ce produit ? Cette action est irréversible.</p>

            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 font-medium">
                    Annuler
                </button>
                <a id="confirmDeleteLink" href="#"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-semibold">
                    Supprimer
                </a>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        function openDeleteModal(id) {
            const modal = document.getElementById("deleteModal");
            const link = document.getElementById("confirmDeleteLink");
            link.href = "?supprimer=" + id;
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }

        function closeDeleteModal() {
            const modal = document.getElementById("deleteModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    </script>


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