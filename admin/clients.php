<?php
    session_start();
    require_once "../config/database.php";

    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();
    }

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
<body class="flex bg-gray-100" style="font-family: 'DM Sans', sans-serif;">
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <i class="fas fa-users text-blue-600"></i> Liste des clients
        </h1>

        <div class="bg-white p-6 rounded shadow">
            <table class="w-full border-collapse text-sm">
                <thead class="bg-blue-100 text-blue-800">
                    <tr>
                        <th class="p-2 border">Id</th>
                        <th class="p-2 border">Nom</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Téléphone</th>
                        <th class="p-2 border">Date d’inscription</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($c = $result->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-3 font-semibold text-gray-700"><?= $c['id'] ?></td>
                                <td class="p-3"><?= htmlspecialchars($c['nom']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($c['email']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($c['telephone']) ?></td>
                                <td class="p-3 text-gray-500"><?= $c['date_creation'] ?></td>
                                <td class="p-3 text-center flex justify-center gap-3">
                                    <a href="?edit=<?= $c['id'] ?>" class="text-blue-600 hover:text-blue-800 transition">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="?supprimer=<?= $c['id'] ?>" 
                                       onclick="return confirm('Supprimer ce client ?')" 
                                       class="text-red-600 hover:text-red-800 transition">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
