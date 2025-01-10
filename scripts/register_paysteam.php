<?php
include '../scripts/db_connection.php';
session_start();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $firstName = $_POST['nome'];
    $lastName = $_POST['cognome'];
    $email = $_POST['email'];
    $type = $_POST['tipo'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['conferma_password'];

    // Validate email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['messaggioErrore'] = "L'indirizzo email inserito non è valido.";
        header("Location: ../paysteam/paysteam_registration.php");
        exit();
    } else if ($password != $confirmPassword) {
        $_SESSION['messaggioErrore'] = "Le password non corrispondono.";
        header("Location: ../paysteam/paysteam_registration.php");
        exit();
    } else {
        //Query to insert data
        $sql = "INSERT INTO UtentePaySteam (Nome, Cognome, Email, Password, SaldoConto, Tipo)
       VALUES ('$firstName', '$lastName', '$email', '$password', 10, '$type')";
        if ($conn->query($sql) === TRUE) {
            // Redirect to login page
            header("Location: ../paysteam/login_paysteam.php");
            exit();
        }
    }
}
?>
