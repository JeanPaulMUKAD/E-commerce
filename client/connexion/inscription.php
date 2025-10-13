<?php
session_start();
require_once '../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $email = trim($_POST["email"]);
    $telephone = trim($_POST["telephone"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);
    $confirmer = trim($_POST["confirmer"]);

    if (empty($nom) || empty($email) || empty($mot_de_passe) || empty($confirmer)) {
        $message = "⚠️ Veuillez remplir tous les champs.";
    } elseif ($mot_de_passe !== $confirmer) {
        $message = "⚠️ Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "⚠️ Cet e-mail est déjà utilisé.";
        } else {
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $role = 'client';

            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, telephone, mot_de_passe, role, date_creation) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $nom, $email, $telephone, $mot_de_passe_hache, $role);

            if ($stmt->execute()) {
                $message = "✅ Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $message = "❌ Erreur lors de la création du compte.";
            }
        }
    }
}
?>

<?php include 'include/header.php'; ?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">Créer un compte client</h1>

    <?php if (!empty($message)): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6 space-y-4">
        <div>
            <label class="block mb-1 font-medium">Nom complet <span class="text-red-600">*</span></label>
            <input type="text" name="nom" class="w-full border rounded-lg p-2" required>
        </div>

        <div>
            <label class="block mb-1 font-medium">Email <span class="text-red-600">*</span></label>
            <input type="email" name="email" class="w-full border rounded-lg p-2" required>
        </div>

        <div>
            <label class="block mb-1 font-medium">Téléphone</label>
            <input type="text" name="telephone" class="w-full border rounded-lg p-2">
        </div>

        <div>
            <label class="block mb-1 font-medium">Mot de passe <span class="text-red-600">*</span></label>
            <input type="password" name="mot_de_passe" class="w-full border rounded-lg p-2" required>
        </div>

        <div>
            <label class="block mb-1 font-medium">Confirmer le mot de passe <span class="text-red-600">*</span></label>
            <input type="password" name="confirmer" class="w-full border rounded-lg p-2" required>
        </div>

        <button type="submit"
            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition duration-300">
            <i class="fas fa-user-plus"></i> Créer mon compte
        </button>
    </form>

    <p class="text-center mt-4">
        Vous avez déjà un compte ?
        <a href="connexion.php" class="text-green-700 font-semibold hover:underline">Se connecter</a>
    </p>
</div>

</body>
</html>
