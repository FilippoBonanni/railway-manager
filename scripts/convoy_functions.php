<?php
include 'db_connection.php';

//Get rolling stock data
function getRollingStock($conn, $name)
{
    $sql = "SELECT Nome, Serie, Id FROM MaterialeRotabile 
WHERE Tipo = '$name' AND Id_Convoglio IS NULL;";
    $result = $conn->query($sql);
    $equipment = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $equipment[] = $row;
        }
    }
    return $equipment;
}

//Query to insert into Convoglio table 
function insertTrain($conn, $trainName)
{
    $sql = "INSERT INTO Convoglio (Nome, Posti_Disponibili, Stato) VALUES ('$trainName', 0, 'Mai Utilizzato')";
    $result = $conn->query($sql);
}

// Query to update MaterialeRotabile table with train ID
function updateEquipment($conn, $trainId, $union)
{
    $idString = implode(',', $union);
    $sql = "UPDATE MaterialeRotabile SET Id_Convoglio = $trainId WHERE Id IN ($idString)";
    $result = $conn->query($sql);
}

// Query to update available seats in train
function updateSeats($conn, $trainId)
{
    $sql = "UPDATE Convoglio SET Posti_Disponibili =
(SELECT SUM(Posti) FROM MaterialeRotabile WHERE MaterialeRotabile.Id_Convoglio = Convoglio.Id)
WHERE Id = $trainId";
    $result = $conn->query($sql);
}
?>