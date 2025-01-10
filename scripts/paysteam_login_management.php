<?php
include 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    $sql = "SELECT * FROM UtentePaySteam WHERE Email='" . $username . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['Password'] === $password) {
            // Save session variables
            $_SESSION['email'] = $user['Email'];
            $_SESSION['name'] = $user['Nome'];
            $_SESSION['type'] = $user['Tipo'];
            $_SESSION['user_id'] = $user['Id'];

            // Check if index was passed
            if (isset($_POST['indice'])) {
                $index = (int)$_POST['indice'];
                header("Location: ../paysteam/purchase_confirmation.php?indice=$index");
                exit();
            }
        } else {
            $_SESSION['errore_msg'] = "Password Errata";
            header('Location: ../paysteam/login_paysteam.php');
            exit();
        }
    } else {
        $_SESSION['errore_msg'] = "Utente non trovato";
        header('Location: ../paysteam/login_paysteam.php');
        exit();
    }
}
$conn->close();
?>