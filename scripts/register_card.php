<?php
include '../scripts/db_connection.php';
session_start();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get user id
    $user_id = $_SESSION['user_id'];
    $fullName = $_POST['nome_cognome'];
    $number = $_POST['numero'];
    $month = $_POST['mese_scadenza'];
    $year = $_POST['anno_scadenza'];
    $cvv = $_POST['cvv'];

    // Get current year
    $currentYear = date("y"); // Last two digits of current year

    // Validate month and year
    if ($month > 12) {
        $_SESSION['messaggioErrore'] = "Il mese deve essere compreso tra 01 e 12.";
        header("Location: ../paysteam/card_registration.php");
        exit();
    } elseif ($year < $currentYear) {
        $_SESSION['messaggioErrore'] = "L'anno deve essere maggiore o uguale a " . $currentYear . ".";
        header("Location: ../paysteam/card_registration.php");
        exit();
    } else {
        $expirationDate = $month . '/' . $year;

        // Query to insert card data
        $sqlCard = "INSERT INTO CartaDiCredito (NomeCognome, Numero, DataScadenza, CVV)
       VALUES ('$fullName', $number, '$expirationDate', $cvv)";
        $resultCard = $conn->query($sqlCard);

        // Get generated autoincrement ID
        if ($resultCard) {
            $cardId = $conn->insert_id;

            // Query to insert card id for user
            $sqlUser = "UPDATE UtentePaySteam SET Id_Carta = $cardId WHERE Id = $user_id";
            if ($conn->query($sqlUser) === TRUE) {
                // Redirect to profile page
                header("Location: ../paysteam/profile.php");
                exit();
            } else {
                $_SESSION['messaggioErrore'] = "Qualcosa è andato storto, carta non registrata.";
                header("Location: ../paysteam/card_registration.php");
                exit();
            }
        } else {
            $_SESSION['messaggioErrore'] = "Errore durante la registrazione della carta: " . $conn->error;
            header("Location: ../paysteam/card_registration.php");
            exit();
        }
    }
}
?>