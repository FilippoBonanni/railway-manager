<?php
include 'db_connection.php';
// Query to get the Departure Station Id
function getDepartureId($conn, $DepartureStation)
{
    $sql = "SELECT Id FROM Stazione WHERE Nome = '$DepartureStation'";
    $result = $conn->query($sql);
    $DepartureId = 0;
    if ($result->num_rows > 0) {
        $rowDeparture = $result->fetch_assoc();
        $DepartureId = $rowDeparture['Id'];
    }
    return $DepartureId;
}

function getArrivalId($conn, $ArrivalStation)
{
    $sql = "SELECT Id FROM Stazione WHERE Nome = '$ArrivalStation'";
    $result = $conn->query($sql);
    $ArrivalId = 0;
    if ($result->num_rows > 0) {
        $rowArrival = $result->fetch_assoc();
        $ArrivalId = $rowArrival['Id'];
    }
    return $ArrivalId;
}

// Query to find the route id
function getRouteId($conn, $DepartureId, $ArrivalId, $DepartureTimestamp)
{
    $sql = "SELECT st2.Id_Tratta
FROM SubTratta st1
JOIN SubTratta st2 ON st1.Id_Tratta = st2.Id_Tratta
JOIN Stazione s1 ON s1.Id = st1.StazionePartenza
JOIN Stazione s2 ON s2.Id = st2.StazioneArrivo
WHERE st1.StazionePartenza = $DepartureId
AND st2.StazioneArrivo = $ArrivalId
AND st1.DataOraPartenza > '$DepartureTimestamp'";
    $result = $conn->query($sql);
    $routeIds = [];
    if ($result->num_rows > 0) {
        while ($rowRoute = $result->fetch_assoc()) {
            $routeIds[] = $rowRoute['Id_Tratta'];
        }
    }
    return $routeIdsString = implode(',', $routeIds);
}

// Query to find the train ids
function getTrainIds($conn, $DepartureId, $ArrivalId, $DepartureTimestamp)
{
    $sql = "SELECT st1.Id_Convoglio
FROM SubTratta st1
JOIN SubTratta st2 ON st1.Id_Tratta = st2.Id_Tratta
JOIN Stazione s1 ON s1.Id = st1.StazionePartenza
JOIN Stazione s2 ON s2.Id = st2.StazioneArrivo
WHERE st1.StazionePartenza = $DepartureId
AND st2.StazioneArrivo = $ArrivalId AND st1.DataOraPartenza > '$DepartureTimestamp'";
    $result = $conn->query($sql);
    $TrainIds = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $TrainIds[] = $row['Id_Convoglio'];
        }
    }
    return $TrainIds;
}

// Query to get the routes requested by the user
function getRoutes($conn, $DepartureId, $ArrivalId, $DepartureTimestamp, $DepartureDate)
{
    $sql = "SELECT DISTINCT s1.Nome AS DepartureName, st1.DataOraPartenza, s2.Nome AS ArrivalName,
 st2.DataOraArrivo, st1.Id_Convoglio, st1.Id_Tratta
FROM SubTratta st1
JOIN SubTratta st2 ON st1.Id_Tratta = st2.Id_Tratta
JOIN Stazione s1 ON s1.Id = st1.StazionePartenza
JOIN Stazione s2 ON s2.Id = st2.StazioneArrivo
WHERE st1.StazionePartenza = $DepartureId
AND st2.StazioneArrivo = $ArrivalId
AND st1.DataOraPartenza > '$DepartureTimestamp'
AND DATE(st1.DataOraPartenza) = '$DepartureDate'
AND st1.DataOraPartenza < st2.DataOraArrivo";
    return $result = $conn->query($sql);
}
