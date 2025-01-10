<?php
include 'db_connection.php';

// Query to get balance
function getBalance($conn, $user_id)
{
    $sql = "SELECT SaldoConto FROM UtentePaySteam WHERE Id = $user_id ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $rowAccount = $result->fetch_assoc();
        return $rowAccount['SaldoConto'];
    }
    return 0;
}

// Query to check if credit card exists
function checkCard($conn, $user_id)
{
    $card = "SELECT Id_Carta FROM UtentePaySteam WHERE Id = $user_id";
    $resultCard = $conn->query($card);
    if ($resultCard->num_rows > 0) {
        $rowCard = $resultCard->fetch_assoc();
        return $rowCard['Id_Carta'];
    } else {
        return null;
    }
}

// Query to find if card is present
function findCard($conn, $user_id)
{
    $sql = "SELECT Id_Carta FROM UtentePaySteam WHERE Id = $user_id";
    $result = $conn->query($sql);
    $creditCard = null;
    if ($result->num_rows > 0) {
        $rowCard = $result->fetch_assoc();
        $creditCard = $rowCard['Id_Carta'];
    }
    return $creditCard;
}

// Query to update remaining seats after purchase
function subtractSeats($conn, $passengers, $route_id)
{
    $sql = "UPDATE Tratta SET PostiRimasti = PostiRimasti - $passengers WHERE Id = $route_id";
    $result = $conn->query($sql);
}

// Generate 10 random characters string
function generateRandomString()
{
    $length = 10;
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $randomString;
}

// Query to insert data in Ticket table
function insertTicket($conn, $price, $passengers, $random, $user_id, $route_id)
{
    $sql = "INSERT INTO Biglietto (Prezzo, Posti, CodiceBiglietto, Id_Utente, Id_Tratta)
VALUES ($price, $passengers, '$random', $user_id, $route_id)";
    $result = $conn->query($sql);
}

// Query to insert data in Transaction table
function insertTransaction($conn, $price, $user_id)
{
    $sql = "INSERT INTO Movimento (Tipo, Importo, Id_UtentePaysteam) VALUES ('Effettuato', $price, $user_id)";
    $result = $conn->query($sql);
}

// Query to insert data in SFT merchant table
function insertSFTTransaction($conn, $price)
{
    $sql = "INSERT INTO Movimento (Tipo, Importo, Id_UtentePaysteam) VALUES ('Ricevuto', $price, 1)";
    $result = $conn->query($sql);
}

//Query to update SFT Merchant balance
function updateSFTBalance($conn, $price)
{
    $sql = "UPDATE UtentePaySteam SET SaldoConto = SaldoConto + $price 
    WHERE Email = 'filippo.bonanni@sftesercente.it'";
    $result = $conn->query($sql);
}

// Query to update user balance
function updateUserBalance($conn, $price, $user_id)
{
    $sql = "UPDATE UtentePaySteam SET SaldoConto = SaldoConto - $price WHERE Id = $user_id";
    $result = $conn->query($sql);
}

// Query to get final ticket details
function getTicket($conn, $user_id)
{
    $sql = "SELECT b.`Id`, u.Nome, u.Cognome, b.CodiceBiglietto, b.Prezzo, b.Posti
    FROM Biglietto AS b JOIN UtentePaySteam AS u ON b.Id_Utente = u.Id
    WHERE b.Id in (SELECT MAX(Id) FROM Biglietto WHERE Id_Utente = $user_id)";
    $result = $conn->query($sql);
    $tickets = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
    }
    return $tickets;
}
?>