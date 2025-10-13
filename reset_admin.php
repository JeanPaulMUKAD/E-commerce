<?php
require_once "config/database.php"; // adapte le chemin si besoin

$newPassword = 'Admin@1234'; // mot de passe que tu veux définir
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE role='admin' LIMIT 1");
$stmt->bind_param('s', $hash);

if ($stmt->execute()) {
    echo "Mot de passe admin réinitialisé avec succès ! Nouveau mot de passe : $newPassword";
} else {
    echo "Erreur : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
