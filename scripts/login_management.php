<?php
include 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    $sql = "SELECT * FROM Utente WHERE Email='" . $username . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['Password'] === $password) {
            // Save session variables
            $_SESSION['email'] = $user['Email'];
            $_SESSION['name'] = $user['Password'];
            $_SESSION['type'] = $user['Tipo'];

            // Page selection based on user type
            switch ($_SESSION['type']) {
                case "Acquirente":
                    header('Location: ../user/registered_user.php');
                    break;
                case "Backoffice Amministrativo":
                    header('Location: ../administrative/administrative_office.php');
                    break;
                case "Backoffice Esercizio":
                    header('Location: ../exercise/exercise_office.php');
                    break;
                default:
                    break;
            }
        } else {
            $_SESSION['errore_msg'] = "Password Errata";
            header('Location: ../home/login.php');
        }
    } else {
        $_SESSION['errore_msg'] = "Utente non trovato";
        header('Location: ../home/login.php');
    }
}
$conn->close();
?>
