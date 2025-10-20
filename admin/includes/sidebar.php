<!-- sidebar.php -->
<!-- Importation Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="flex" style="font-family: 'DM Sans', sans-serif; background-color: #F4F5FF;">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 text-white flex flex-col min-h-screen transition-all duration-300" style="background-color: #673DE6;">
        <!-- Logo -->
        <div class="p-5 border-b border-blue-600">
            <h2 class="text-2xl font-bold text-center">E-Commerce</h2>
            <p class="text-sm text-center text-blue-200 mt-1">Tableau de bord</p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2">
            <a href="dashboard.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-blue-800 transition <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-900 ' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Tableau de bord</span>
            </a>

            <a href="produits.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-blue-800  transition <?= basename($_SERVER['PHP_SELF']) == 'produits.php' ? 'bg-blue-900 ' : '' ?>">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Produits</span>
            </a>

            <a href="reservations.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-blue-800  transition <?= basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'bg-blue-900 ' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Réservations</span>
            </a>

            <a href="clients.php" class="flex items-center gap-3 py-2.5 px-4 rounded hover:bg-blue-800  transition <?= basename($_SERVER['PHP_SELF']) == 'clients.php' ? 'bg-blue-900 ' : '' ?>">
                <i class="fa-solid fa-users"></i>
                <span>Clients</span>
            </a>
        </nav>

        <!-- Pied de la sidebar -->
        <div class="p-4 border-t border-blue-800">
            <a href="deconnexion.php" class="flex items-center justify-center gap-2 py-2 px-4 rounded hover:bg-red-700 transition">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="flex-1 p-6 bg-gray-100 min-h-screen">
        <!-- Ici tu incluras le contenu spécifique de chaque page -->
    </div>
</div>
