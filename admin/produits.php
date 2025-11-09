<?php declare(strict_types=1); 
    session_start();
    require_once "../config/database.php";

    // Vérifie si l'admin est connecté
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();
    }

    // Fonction de conversion USD vers FC (Francs Congolais)
    function convertirUsdVersFc($prixUsd) {
        $tauxChange = 2500; // 1 USD = 2,500 FC
        return $prixUsd * $tauxChange;
    }

    // Fonction de conversion FC vers USD
    function convertirFcVersUsd($prixFc) {
        $tauxChange = 2500; // 1 USD = 2,500 FC
        return $prixFc / $tauxChange;
    }

    function formaterPrix($prix) {
        // Conversion en float pour s'assurer que c'est un nombre
        $prix = floatval($prix);
        return number_format($prix, 0, ',', ' ');
    }

    // Vérifie si le dossier uploads existe
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    $message = "";

    // === AJOUT D'UN PRODUIT ===
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajouter'])) {
        $nom = trim($_POST["nom"]);
        $description = trim($_POST["description"]);
        $prix = floatval($_POST["prix"]);
        $devise = $_POST["devise"] ?? 'USD';
        
        // Stocker le prix TEL QUEL avec sa devise
        $quantite = intval($_POST["quantite"]);
        $categorie = trim($_POST["categorie"]);

        $image = null;
        if (!empty($_FILES["image"]["name"])) {
            $image = "uploads/" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        }

        $stmt = $conn->prepare("INSERT INTO produits (nom, description, prix, devise, quantite, categorie, image, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssdsdss", $nom, $description, $prix, $devise, $quantite, $categorie, $image);

        if ($stmt->execute()) {
            $message = "✅ Produit ajouté avec succès.";
        } else {
            $message = "❌ Erreur lors de l'ajout du produit.";
        }
    }

    // === MODIFICATION D'UN PRODUIT ===
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['modifier'])) {
        $id = intval($_POST["id"]);
        $nom = trim($_POST["nom"]);
        $description = trim($_POST["description"]);
        $prix = floatval($_POST["prix"]);
        $devise = $_POST["devise"] ?? 'USD';
        
        // Stocker le prix TEL QUEL avec sa devise
        $quantite = intval($_POST["quantite"]);
        $categorie = trim($_POST["categorie"]);

        $image = $_POST["image_actuelle"];
        if (!empty($_FILES["image"]["name"])) {
            $image = "uploads/" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        }

        $stmt = $conn->prepare("UPDATE produits SET nom=?, description=?, prix=?, devise=?, quantite=?, categorie=?, image=?, date_modification=NOW() WHERE id=?");
        $stmt->bind_param("ssdsdssi", $nom, $description, $prix, $devise, $quantite, $categorie, $image, $id);

        if ($stmt->execute()) {
            $message = "✅ Produit modifié avec succès.";
        } else {
            $message = "❌ Erreur lors de la modification du produit.";
        }
    }

    // === SUPPRESSION D'UN PRODUIT ===
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

<body class="flex bg-gray-100" style="font-family: 'Arial', sans-serif;">

    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Gestion des produits</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $message ?></div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-lg font-semibold mb-3 text-blue-700">➕ Ajouter un produit</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <input type="text" name="nom" placeholder="Nom du produit"
                    class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500" required>
                <input type="text" name="categorie" placeholder="Catégorie"
                    class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500">
                <textarea name="description" placeholder="Description"
                    class="border p-2 rounded w-full md:col-span-2 focus:ring-2 focus:ring-blue-500"></textarea>
                <div class="md:col-span-2">
                    <div class="flex gap-2">
                        <select name="devise" id="devise" class="border p-2 rounded focus:ring-2 focus:ring-blue-500" onchange="updatePricePlaceholder()">
                            <option value="USD">USD ($)</option>
                            <option value="FC">FC</option>
                        </select>
                        <input type="number" min="1" step="0.01" name="prix" id="prix" placeholder="Prix en USD ($)"
                            class="border p-2 rounded flex-1 focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        <span id="conversion-info">💡 Le prix sera enregistré en Dollars (USD)</span>
                    </div>
                    <div id="conversion-display" class="mt-1 text-sm text-blue-600 hidden">
                        <span id="conversion-text"></span>
                    </div>
                </div>
                <input type="number" min="1" name="quantite" placeholder="Quantité"
                    class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500" required>
                <input type="file" name="image"
                    class="border p-2 rounded w-full md:col-span-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" name="ajouter"
                class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
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
                        <th class="p-2 border">Devise</th>
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
                            <td class="p-2 border">
                                <div class="text-sm">
                                    <!-- Afficher le prix stocké -->
                                    <div class="font-semibold text-green-600">
                                        <?= formaterPrix($p['prix']) ?> 
                                        <?= $p['devise'] === 'USD' ? '$' : 'FC' ?>
                                    </div>
                                    <!-- Afficher la conversion -->
                                    <div class="text-gray-600">
                                        <?php if ($p['devise'] === 'USD'): ?>
                                            <?= formaterPrix(convertirUsdVersFc($p['prix'])) ?> FC
                                        <?php else: ?>
                                            <?= formaterPrix(convertirFcVersUsd($p['prix'])) ?> $
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="p-2 border text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold <?= $p['devise'] === 'USD' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= $p['devise'] ?>
                                </span>
                            </td>
                            <td class="p-2 border"><?= $p['quantite'] ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($p['categorie']) ?></td>
                            <td class="p-2 border text-center">
                                <?php if ($p['image']): ?>
                                    <img src="<?= $p['image'] ?>" alt="" class="w-12 h-12 object-cover mx-auto rounded">
                                <?php else: ?>
                                    <span class="text-gray-400">Aucune</span>
                                <?php endif; ?>
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
                        <div class="md:col-span-2">
                            <div class="flex gap-2">
                                <!-- Pré-sélectionner la devise stockée -->
                                <select name="devise" id="devise-edit" class="border p-2 rounded focus:ring-2 focus:ring-yellow-400" onchange="updatePricePlaceholderEdit()">
                                    <option value="USD" <?= $prod['devise'] === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                    <option value="FC" <?= $prod['devise'] === 'FC' ? 'selected' : '' ?>>FC</option>
                                </select>
                                <!-- Afficher le prix stocké -->
                                <input type="number" min="1" step="0.01" name="prix" id="prix-edit" value="<?= floatval($prod['prix']) ?>"
                                    class="border p-2 rounded flex-1 focus:ring-2 focus:ring-yellow-400" required>
                            </div>
                            <div class="mt-1 text-sm text-gray-600">
                                <!-- Afficher les conversions pour information -->
                                <?php if ($prod['devise'] === 'USD'): ?>
                                    <span class="font-semibold text-green-600">Stocké : <?= formaterPrix($prod['prix']) ?> $</span>
                                    <span class="mx-2">|</span>
                                    <span class="text-gray-600">Conversion : <?= formaterPrix(convertirUsdVersFc($prod['prix'])) ?> FC</span>
                                <?php else: ?>
                                    <span class="font-semibold text-green-600">Stocké : <?= formaterPrix($prod['prix']) ?> FC</span>
                                    <span class="mx-2">|</span>
                                    <span class="text-gray-600">Conversion : <?= formaterPrix(convertirFcVersUsd($prod['prix'])) ?> $</span>
                                <?php endif; ?>
                            </div>
                            <div id="conversion-display-edit" class="mt-1 text-sm text-blue-600 hidden">
                                <span id="conversion-text-edit"></span>
                            </div>
                        </div>
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


   <script>
    
    let a = 0;
    let masque = document.createElement('div');
    let cercle = document.createElement('div');

    let angle = 0;

    window.addEventListener('load', () => {
        a = 1;

        // Le cercle commence à tourner immédiatement
        anime = setInterval(() => {
            angle += 10; // Vitesse de rotation du cercle
            cercle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;
        }, 20);

        // Après 1 seconde, on arrête l'animation et on fait disparaître le masque
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

    // Création du cercle (réduit)
    cercle.style.width = '40px';  // Au lieu de 15vh
    cercle.style.height = '40px'; // Au lieu de 15vh
    cercle.style.border = '2px solid #f3f3f3'; // Bordure plus fine
    cercle.style.borderTop = '2px solid #2F1C6A';
    cercle.style.borderRadius = '50%';
    cercle.style.position = 'absolute';
    cercle.style.top = '50%';
    cercle.style.left = '50%';
    cercle.style.transform = 'translate(-50%, -50%)';
    cercle.style.boxSizing = 'border-box';
    cercle.style.zIndex = '1';
    masque.appendChild(cercle);

    // Variable de l'animation
    let anime;

   </script>

    <!-- Script pour la conversion de devises -->
    <script>
        const TAUX_CHANGE = 2500; // 1 USD = 2,500 FC

        function updatePricePlaceholder() {
            const devise = document.getElementById('devise').value;
            const prixInput = document.getElementById('prix');
            const conversionInfo = document.getElementById('conversion-info');
            const conversionDisplay = document.getElementById('conversion-display');
            
            if (devise === 'USD') {
                prixInput.placeholder = 'Prix en USD ($)';
                conversionInfo.textContent = '💡 Le prix sera enregistré en Dollars (USD)';
            } else {
                prixInput.placeholder = 'Prix en FC';
                conversionInfo.textContent = '💡 Le prix sera enregistré en Francs Congolais (FC)';
            }
            
            // Cacher l'affichage de conversion
            conversionDisplay.classList.add('hidden');
            
            // Ajouter l'événement de conversion en temps réel
            prixInput.oninput = function() {
                convertPrice('prix', 'conversion-text', 'conversion-display');
            };
        }

        function updatePricePlaceholderEdit() {
            const devise = document.getElementById('devise-edit').value;
            const prixInput = document.getElementById('prix-edit');
            const conversionDisplay = document.getElementById('conversion-display-edit');
            
            // Ajouter l'événement de conversion en temps réel
            prixInput.oninput = function() {
                convertPrice('prix-edit', 'conversion-text-edit', 'conversion-display-edit');
            };
        }

        function convertPrice(inputId, textId, displayId) {
            const prixInput = document.getElementById(inputId);
            const devise = document.getElementById(inputId === 'prix' ? 'devise' : 'devise-edit').value;
            const prix = parseFloat(prixInput.value);
            const conversionText = document.getElementById(textId);
            const conversionDisplay = document.getElementById(displayId);
            
            if (isNaN(prix) || prix <= 0) {
                conversionDisplay.classList.add('hidden');
                return;
            }
            
            let convertedPrice;
            let convertedCurrency;
            
            if (devise === 'USD') {
                convertedPrice = prix * TAUX_CHANGE;
                convertedCurrency = 'FC';
            } else {
                convertedPrice = prix / TAUX_CHANGE;
                convertedCurrency = 'USD';
            }
            
            conversionText.textContent = `≈ ${formatNumber(convertedPrice)} ${convertedCurrency}`;
            conversionDisplay.classList.remove('hidden');
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(num));
        }

        // Initialiser les événements au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            updatePricePlaceholder();
            if (document.getElementById('devise-edit')) {
                updatePricePlaceholderEdit();
            }
        });
    </script>
</body>

</html>