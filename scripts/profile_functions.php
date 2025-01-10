<?php
include 'db_connection.php';

// Query to get all user info
function getUserArray($conn, $user_id)
{
    $sql = "SELECT * FROM UtentePaySteam WHERE Id= $user_id";
    $result = $conn->query($sql);
    $user = [];
    if ($result->num_rows > 0) {
        while ($rowUser = $result->fetch_assoc()) {
            $user[] = $rowUser;
        }
    }
    return $user;
}

// Query to get all user transactions
function getTransactionArray($conn, $user_id)
{
    $sql = "SELECT * FROM Movimento WHERE Id_UtentePaysteam = $user_id ORDER BY Id DESC";
    $result = $conn->query($sql);
    $transactions = [];
    if ($result->num_rows > 0) {
        while ($rowTransactions = $result->fetch_assoc()) {
            $transactions[] = $rowTransactions;
        }
    }
    return $transactions;
}

// Query to check card presence
function isCardPresent($conn, $user_id)
{
    $sql = "SELECT c.Numero FROM UtentePaySteam AS u
JOIN CartaDiCredito AS c ON u.Id_Carta = c.Id WHERE u.Id = $user_id";
    $result = $conn->query($sql);
    $card = 0;
    if ($result->num_rows > 0) {
        $rowCard = $result->fetch_assoc();
        $card = $rowCard['Numero'];
    }
    return $card;
}

// Query to identify Card ID 
function findCard($conn, $user_id)
{
    $sql = "SELECT Id_Carta FROM UtentePaySteam WHERE Id = $user_id";
    $result = $conn->query($sql);
    $cardId = 0;
    if ($result->num_rows > 0) {
        $rowCard = $result->fetch_assoc();
        $cardId = $rowCard['Id_Carta'];
    }
    return $cardId;
}

// Query to delete card
function deleteCard($conn, $cardId)
{
    $sql = "DELETE FROM CartaDiCredito WHERE Id = $cardId";
    $result = $conn->query($sql);
}
?>