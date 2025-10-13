<!-- sidebar.php -->
<!-- Importation de Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="w-64 bg-green-700 text-white flex flex-col min-h-screen">
    <!-- Logo -->
    <div class="p-5 border-b border-green-600">
        <h2 class="text-2xl font-bold text-center">E-Commerce</h2>
        <p class="text-sm text-center text-green-200 mt-1">Tableau de bord</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <a href="dashboard.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-green-600 transition">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="produits.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-green-600 transition">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Produits</span>
        </a>

        <a href="reservations.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-green-600 transition">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Réservations</span>
        </a>

        <a href="clients.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-green-600 transition">
            <i class="fa-solid fa-users"></i>
            <span>Clients</span>
        </a>
    </nav>

    <!-- Pied de la sidebar -->
    <div class="p-4 border-t border-green-600">
        <a href="deconnexion.php" class="flex items-center justify-center gap-2 py-2 px-4 rounded hover:bg-red-700 transition">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</div>
