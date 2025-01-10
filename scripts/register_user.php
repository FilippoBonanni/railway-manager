<?php
include '../scripts/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $firstName = $_POST['nome'];
    $lastName = $_POST['cognome'];
    $email = $_POST['email'];
    $userType = $_POST['tipoUtente'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['conferma_password'];

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['messaggioErrore'] = "L'indirizzo email inserito non è valido.";
        header("Location: ../home/registration.php");
        exit();
    } else if ($password != $confirmPassword) {
        $_SESSION['messaggioErrore'] = "Le password non corrispondono.";
        header("Location: ../home/registration.php");
        exit();
    } else {
        //Insert data query
        $sql = "INSERT INTO Utente (Nome, Cognome, Email, Password, Tipo)
       VALUES ('$firstName', '$lastName', '$email', '$password', '$userType')";
        if ($conn->query($sql) === TRUE) {
            // Redirect to login page
            header("Location: ../home/login.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
