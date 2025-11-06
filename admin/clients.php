<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// ✅ Gestion activation / désactivation
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);

    // Récupérer le statut actuel
    $stmt = $conn->prepare("SELECT statut FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        // Alterner le statut
        $newStatus = ($result['statut'] === 'actif') ? 'desactive' : 'actif';

        $stmt2 = $conn->prepare("UPDATE utilisateurs SET statut = ? WHERE id = ?");
        $stmt2->bind_param("si", $newStatus, $id);
        $stmt2->execute();

        $_SESSION['message'] = ($newStatus === 'actif')
            ? "✅ Client réactivé avec succès."
            : "✅ Client désactivé avec succès.";

        header("Location: clients.php");
        exit();
    }
}

// Message de confirmation
$message = isset($_SESSION['message']) ? $_SESSION['message'] : "";
unset($_SESSION['message']);

// Récupérer tous les clients
$result = $conn->query("SELECT * FROM utilisateurs WHERE role = 'client' ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Clients - Tableau de bord</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2e0e6ad4c.js" crossorigin="anonymous"></script>
</head>

<body class="flex bg-gray-100 font-sans">
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6 text-blue-800 flex items-center gap-2">Liste des clients</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow-sm">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded shadow">
            <table class="w-full border-collapse text-sm">
                <thead class="bg-blue-100 text-blue-800">
                    <tr>
                        <th class="p-2 border">Id</th>
                        <th class="p-2 border">Nom</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Date d'inscription</th>
                        <th class="p-2 border">Statut</th>
                        <th class="p-2 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($c = $result->fetch_assoc()): ?>
                            <tr
                                class="border-b hover:bg-gray-50 transition <?= $c['statut'] === 'desactive' ? 'opacity-50' : '' ?>">
                                <td class="p-3 font-semibold text-gray-700"><?= $c['id'] ?></td>
                                <td class="p-3"><?= htmlspecialchars($c['nom']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($c['email']) ?></td>
                                <td class="p-3 text-gray-500"><?= $c['date_creation'] ?></td>
                                <td class="p-3">
                                    <?php if ($c['statut'] === 'actif'): ?>
                                        <span
                                            class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Actif</span>
                                    <?php else: ?>
                                        <span
                                            class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Désactivé</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-center">
                                    <?php if ($c['statut'] === 'actif'): ?>
                                        <button
                                            onclick="openToggleModal(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['nom'])) ?>', 'desactiver')"
                                            class="text-orange-600 hover:text-orange-800 transition" title="Désactiver le client">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php else: ?>
                                        <button
                                            onclick="openToggleModal(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['nom'])) ?>', 'activer')"
                                            class="text-green-600 hover:text-green-800 transition" title="Réactiver le client">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-4 text-center text-red-500 italic">
                                Aucun client enregistré pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modale de confirmation -->
    <div id="toggleModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="bg-gradient-to-r from-orange-500 to-red-500 p-6 rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">Confirmation</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-700 text-lg mb-6">
                    Voulez-vous <span id="actionText" class="font-bold text-orange-600"></span> le client <span
                        id="clientName" class="font-bold text-orange-600"></span> ?
                </p>
                <div class="flex gap-3">
                    <button onclick="closeToggleModal()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-medium transition">
                        <i class="fas fa-times mr-2"></i> Annuler
                    </button>
                    <a id="confirmBtn" href="#"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-medium transition">
                        <i class="fas fa-check mr-2"></i> Confirmer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openToggleModal(id, name, action) {
            document.getElementById('clientName').textContent = name;
            document.getElementById('actionText').textContent = (action === 'desactiver') ? 'désactiver' : 'réactiver';
            document.getElementById('confirmBtn').href = "?toggle=" + id;
            document.getElementById('toggleModal').classList.remove('hidden');
        }

        function closeToggleModal() {
            document.getElementById('toggleModal').classList.add('hidden');
        }
    </script>


<script>
    let currentClientId = null;

    function openDesactivModal(id, nom) {
        currentClientId = id;
        document.getElementById('clientName').textContent = nom;
        const modal = document.getElementById('desactivModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDesactivModal() {
        const modal = document.getElementById('desactivModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentClientId = null;
    }

    function confirmDesactiv() {
        if (currentClientId) {
            window.location.href = '?desactiver=' + currentClientId;
        }
    }

    // Fermer la modale avec la touche Échap
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDesactivModal();
        }
    });
</script>

<!-- SEARCH LOGO -->
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
</body>

</html>