<?php
include 'db_connection.php'; // Asegúrate de incluir tu archivo de conexión

if (isset($_POST['token']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que las contraseñas coincidan
    if ($password === $confirm_password) {
        // Verificar si el token es válido y no ha expirado
        $query = $conn->prepare("SELECT * FROM usuarios WHERE reset_token = ? AND token_expiration > NOW()");
        $query->bind_param('s', $token);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            // Token válido, actualizar la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Asegúrate de usar hash
            $update = $conn->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, token_expiration = NULL WHERE reset_token = ?");
            $update->bind_param('ss', $hashed_password, $token);
            $update->execute();

            echo "Tu contraseña ha sido actualizada exitosamente.";
        } else {
            echo "El enlace de recuperación no es válido o ha expirado.";
        }
    } else {
        echo "Las contraseñas no coinciden.";
    }
} else {
    echo "Faltan campos en el formulario.";
}
?>
