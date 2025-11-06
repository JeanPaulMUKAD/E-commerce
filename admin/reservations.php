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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="flex bg-gray-100" style="font-family: 'Arial', sans-serif;">
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6 text-blue-800 flex items-center gap-3">
            Liste des Réservations Clients
        </h1>

        <div class="bg-white p-6 rounded shadow">
            <table class="w-full border-collapse text-sm">
                <thead class="bg-blue-100 text-blue-800">
                    <tr>
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Client</th>
                        <th class="p-2 border">Produit</th>
                        <th class="p-2 border">Quantité</th>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Statut</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($r = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-gray-700 font-medium"><?= $r['id'] ?></td>
                            <td class="p-4 text-gray-800"><?= htmlspecialchars($r['client_nom']) ?></td>
                            <td class="p-4 text-gray-800"><?= htmlspecialchars($r['produit_nom']) ?></td>
                            <td class="p-4 text-gray-600"><?= $r['quantite'] ?></td>
                            <td class="p-4 text-gray-500"><?= date('d/m/Y', strtotime($r['date_reservation'])) ?></td>

                            <!-- Statut coloré -->
                            <td class="p-4">
                                <?php
                                $statut = strtolower($r['statut']);
                                $color = match ($statut) {
                                    'confirmée', 'confirmé' => 'bg-green-100 text-green-700',
                                    'en attente' => 'bg-yellow-100 text-yellow-700',
                                    'annulée', 'annulé' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                    <?= ucfirst($r['statut']) ?>
                                </span>
                            </td>

                            <!-- Boutons d’action -->
                            <td class="p-4 text-center">
                                <a href="?edit=<?= $r['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-800 mx-2" 
                                   title="Modifier">
                                   <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="?supprimer=<?= $r['id'] ?>" 
                                   onclick="return confirm('Supprimer cette réservation ?')" 
                                   class="text-red-600 hover:text-red-800 mx-2" 
                                   title="Supprimer">
                                   <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                   
                </tbody>
            </table>
        </div>

        <!-- Message de suppression stylisé -->
        <?php if (isset($_GET['deleted'])): ?>
            <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-md" role="alert">
                <strong class="font-bold">Succès ! </strong>
                <span class="block sm:inline">La réservation a été supprimée avec succès.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" onclick="this.parentElement.parentElement.remove()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Fermer</title>
                        <path d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 10-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z"/>
                    </svg>
                </span>
            </div>
        <?php endif; ?>
    </div>

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
