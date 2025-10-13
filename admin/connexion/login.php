<?php
session_start();
require_once "../config/database.php"; // fichier de connexion MySQL

$message = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);

    if (!empty($email) && !empty($mot_de_passe)) {
        // Préparer la requête sécurisée
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // Vérifier le mot de passe haché
            if (password_verify($mot_de_passe, $admin['mot_de_passe'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nom'] = $admin['nom'];
                $_SESSION['user_role'] = $admin['role'];

                header("Location: dashboard.php");
                exit();
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Aucun administrateur trouvé avec cet e-mail.";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>
    <!-- Lien tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen" style="font-family: verdana, sans-serif;">

    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md">
        <div>
            <img src="https://previews.123rf.com/images/lightstudio/lightstudio1907/lightstudio190700204/126519016-real-estate-construction-logo-design-vector-template-house-and-building-with-blue-grey-color.jpg" alt="Logo" class="mx-auto mb-4 w-28 h-28">
        </div>
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Connexion <strong class="text-green-700">Administrateur</strong></h2>

        <?php if (!empty($message)): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded-lg mb-4 text-center">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <strong class="text-red-700">*</strong></label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 border rounded-lg  focus:outline-none" placeholder="Entrez votre E-mail.">
            </div>

            <div>
                <label for="mot_de_passe" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <strong class="text-red-700">*</strong></label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required
                    class="w-full px-4 py-2 border rounded-lg  focus:outline-none" placeholder="Entrez votre mot de passe.">
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-300">
                Se connecter
            </button>
        </form>
    </div>

</body>

</html>