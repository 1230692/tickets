<?php
// Defina o e-mail e a senha do administrador
$admin_email = 'admin@example.com';
$admin_password_hash = password_hash('adminpassword', PASSWORD_DEFAULT);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verifique se o e-mail e a senha correspondem ao administrador
    if ($email === $admin_email && password_verify($password, $admin_password_hash)) {
        // Redirect to main page
        header("Location: paginaprin.html");
        exit();
    } else {
        echo "Invalid email or password.";
    }
}
?>
