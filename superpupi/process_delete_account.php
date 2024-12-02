<?php
include 'db_connection.php'; // Asegúrate de incluir tu archivo de conexión

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario existe
    $query = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Eliminar la cuenta
            $delete = $conn->prepare("DELETE FROM usuarios WHERE email = ?");
            $delete->bind_param('s', $email);
            $delete->execute();

            echo "Tu cuenta ha sido eliminada exitosamente.";
        } else {
            echo "La contraseña es incorrecta.";
        }
    } else {
        echo "No se encontró un usuario con ese correo electrónico.";
    }
}
?>
