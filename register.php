<?php
session_start();

// Connect to SQLite database
try {
    $db = new SQLite3('/var/www/html/db/users.db');
} catch (Exception $e) {
    $response = array('success' => false, 'message' => 'Erro: Não foi possível conectar à base de dados.');
    echo json_encode($response);
    exit();
}

// Initialize response
$response = array('success' => false, 'message' => '');

// Process registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form data
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    $mobile = filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = filter_input(INPUT_POST, 'RadioButton', FILTER_SANITIZE_STRING);

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $response['message'] = "As senhas não correspondem.";
        echo json_encode($response);
        exit();
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row['count'] > 0) {
        $response['message'] = "Erro: Email já registrado.";
        echo json_encode($response);
        exit();
    }

    // Insert data into database without hashing the password
    $stmt = $db->prepare("INSERT INTO users (first_name, surname, mobile, email, password, role) VALUES (:first_name, :surname, :mobile, :email, :password, :role)");
    $stmt->bindValue(':first_name', $first_name, SQLITE3_TEXT);
    $stmt->bindValue(':surname', $surname, SQLITE3_TEXT);
    $stmt->bindValue(':mobile', $mobile, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT); // Storing plain text password
    $stmt->bindValue(':role', $role, SQLITE3_TEXT);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Conta criada com sucesso!";
    } else {
        $response['message'] = "Erro: " . htmlspecialchars($db->lastErrorMsg());
    }

    echo json_encode($response);
    exit();
}
?>
