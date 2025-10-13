<?php
session_start();
require_once '../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);

    if (!empty($email) && !empty($mot_de_passe)) {
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ? AND role = 'client' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $client = $result->fetch_assoc();
            if (password_verify($mot_de_passe, $client['mot_de_passe'])) {
                $_SESSION['client_id'] = $client['id'];
                $_SESSION['client_nom'] = $client['nom'];
                header("Location: Acceuil.php");
                exit();
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Aucun compte client trouvé avec cet e-mail.";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<?php include 'include/header.php'; ?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">Connexion client</h1>

    <?php if (!empty($message)): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6 space-y-4">
        <div>
            <label class="block mb-1 font-medium">Adresse e-mail <span class="text-red-600">*</span></label>
            <input type="email" name="email" class="w-full border rounded-lg p-2" required>
        </div>

        <div>
            <label class="block mb-1 font-medium">Mot de passe <span class="text-red-600">*</span></label>
            <input type="password" name="mot_de_passe" class="w-full border rounded-lg p-2" required>
        </div>

        <button type="submit"
            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-300">
            <i class="fas fa-sign-in-alt"></i> Se connecter
        </button>
    </form>

    <p class="text-center mt-4">
        Pas encore de compte ?
        <a href="inscription.php" class="text-green-700 font-semibold hover:underline">Créer un compte</a>
    </p>
</div>

</body>
</html>
