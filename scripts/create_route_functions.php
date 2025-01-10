<?php
include 'db_connection.php';

// Query to get all stations
function getStations($conn)
{
    $sql = "SELECT * FROM Stazione";
    $result = $conn->query($sql);
    $stations = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stations[] = $row;
        }
    }
    return $stations;
}

// Query to get available trains 
function getAvailableTrains($conn, $departureStation, $departureDateTime)
{
    $sql = "SELECT Nome FROM Convoglio WHERE Id IN (SELECT st.Id_Convoglio
    FROM SubTratta AS st
    JOIN Stazione AS s ON s.Id = st.StazioneArrivo
    WHERE s.Nome = '$departureStation' AND st.DataOraArrivo < '$departureDateTime'
    AND st.DataOraArrivo = (SELECT MAX(DataOraArrivo) FROM SubTratta WHERE Id_Convoglio = st.Id_Convoglio))";
    return $result = $conn->query($sql);
}

// Query to get train status
function getTrainStatus($conn)
{
    $sql = "SELECT Nome FROM Convoglio WHERE Stato = 'Mai Utilizzato'";
    return $result = $conn->query($sql);
}

// Query to check if a train has never been used
function neverUsed($conn, $train)
{
    $sql = "SELECT Id FROM Convoglio WHERE Nome = '$train' AND Stato = 'Mai Utilizzato'";
    return $result = $conn->query($sql);
}

// Query to update train status
function updateStatus($conn, $train)
{
    $sql = "UPDATE Convoglio SET Stato = 'Utilizzato' WHERE Nome = '$train'";
    $result = $conn->query($sql);
}

// Query to get available seats on a train
function getSeats($conn, $train)
{
    $sql = "SELECT Posti_Disponibili FROM Convoglio WHERE Nome = '$train'";
    $result = $conn->query($sql);
    $seats = 0;
    if ($result->num_rows > 0) {
        $rowSeats = $result->fetch_assoc();
        $seats = $rowSeats['Posti_Disponibili'];
    }
    return $seats;
}

// Query to create route
function createRoute($conn, $departureStation, $currentDepartureTime, $arrivalStation, $seats)
{
    $sql = "INSERT INTO Tratta (Partenza, DataPartenza, Arrivo, PostiRimasti)
    VALUES ('$departureStation', '$currentDepartureTime', '$arrivalStation', $seats)";
    $result = $conn->query($sql);
}

// Query to get departure station Id
function getDepartureStationId($conn, $departureStation)
{
    $sql = "SELECT Id FROM Stazione WHERE Nome = '$departureStation'";
    $result = $conn->query($sql);
    $departure = 0;
    if ($result->num_rows > 0) {
        $IdD = $result->fetch_assoc();
        $departure = $IdD['Id'];
    }
    return $departure;
}

// Query to get arrival station Id
function getArrivalStationId($conn, $arrivalStation)
{
    $sql = "SELECT Id FROM Stazione WHERE Nome = '$arrivalStation'";
    $result = $conn->query($sql);
    $arrival = 0;
    if ($result->num_rows > 0) {
        $IdA = $result->fetch_assoc();
        $arrival = $IdA['Id'];
    }
    return $arrival;
}

// Query to get train Id from train name
function getTrainId($conn, $train)
{
    $sql = "SELECT Id FROM Convoglio WHERE Nome = '$train'";
    $result = $conn->query($sql);
    $trainId = 0;
    if ($result->num_rows > 0) {
        $rowTrainId = $result->fetch_assoc();
        $trainId = $rowTrainId['Id'];
    }
    return $trainId;
}

// Query to get train Id from route Id
function getTrainIdFromRoute($conn, $routeId)
{
    $sql = "SELECT DISTINCT Id_Convoglio FROM SubTratta WHERE Id_Tratta = $routeId";
    $result = $conn->query($sql);
    $trainId = 0;
    if ($result->num_rows > 0) {
        $rowTrainId = $result->fetch_assoc();
        $trainId = $rowTrainId['Id_Convoglio'];
    }
    return $trainId;
}

// Query to get route Id
function getRouteId($conn)
{
    $sql = "SELECT Id FROM Tratta ORDER BY Id DESC LIMIT 1";
    $result = $conn->query($sql);
    $routeId = 0;
    if ($result->num_rows > 0) {
        $rowRouteId = $result->fetch_assoc();
        $routeId = $rowRouteId['Id'];
    }
    return $routeId;
}

// Query to get departure station kilometer
function getDepartureKm($conn, $departure)
{
    $sql = "SELECT Km FROM Stazione WHERE Id = $departure";
    $result = $conn->query($sql);
    $kmD = 0;
    if ($result->num_rows > 0) {
        $rowKmD = $result->fetch_assoc();
        $kmD = $rowKmD['Km'];
    }
    return $kmD;
}

// Query to get arrival station kilometer  
function getArrivalKm($conn, $arrival)
{
    $sql = "SELECT Km FROM Stazione WHERE Id = $arrival";
    $result = $conn->query($sql);
    $kmA = 0;
    if ($result->num_rows > 0) {
        $rowKmA = $result->fetch_assoc();
        $kmA = $rowKmA['Km'];
    }
    return $kmA;
}

// Query to check connections
function checkConnections($conn, $departureDateTime, $arrivalDateTime, $departure, $arrival, $trainId, $i = 0)
{
    $sql = "SELECT MAX(DataOraArrivo) AS MaxArrivalDateTime FROM SubTratta
    WHERE (('$departureDateTime' BETWEEN DataOraPartenza AND DataOraArrivo
    OR '$arrivalDateTime' BETWEEN DataOraPartenza AND DataOraArrivo
    OR DataOraPartenza = '$departureDateTime'
    OR DataOraPartenza BETWEEN '$departureDateTime' AND '$arrivalDateTime'
    OR DataOraArrivo BETWEEN '$departureDateTime' AND '$arrivalDateTime')
    AND ((StazionePartenza = $departure AND StazioneArrivo = $arrival)
    OR (StazionePartenza = $arrival AND StazioneArrivo = $departure))) AND Id_Convoglio != $trainId";
    $result = $conn->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['MaxArrivalDateTime'] != $departureDateTime) {
            if (!empty($row['MaxArrivalDateTime'])) {
                $newDepartureDateTime = $row['MaxArrivalDateTime'];
                $kmD = getDepartureKm($conn, $departure);
                $kmA = getArrivalKm($conn, $arrival);
                $arrivalTime = abs($kmA - $kmD) / 50 * 60;
                $arrivalTimestamp = strtotime($newDepartureDateTime) + ($arrivalTime * 60);
                $newArrivalDateTime = date('Y-m-d H:i:s', $arrivalTimestamp);
                $i++;
                if ($i > 50) {
                    return $newDepartureDateTime;
                }

                return checkConnections($conn, $newDepartureDateTime, $newArrivalDateTime, $departure, $arrival, $trainId, $i);
            }
        }
    }
    return $departureDateTime;
}

// Query to update route with new departure time including delay
function updateDepartureTimeWithDelay($conn, $newDepartureDateTime, $routeId)
{
    $sql = "UPDATE Tratta SET DataPartenza = '$newDepartureDateTime' WHERE Id = $routeId";
    $result = $conn->query($sql);
}

// Query to update route with new departure time without delay
function updateDepartureTimeNoDelay($conn, $departureDateTime, $routeId)
{
    $sql = "UPDATE Tratta SET DataPartenza = '$departureDateTime' WHERE Id = $routeId";
    $result = $conn->query($sql);
}

// Query to insert subroute with delay
function insertSubrouteWithDelay($conn, $departure, $newDepartureDateTime, $arrival, $arrivalDateTime, $routeId, $trainId)
{
    $sql = "INSERT INTO SubTratta (StazionePartenza, DataOraPartenza, StazioneArrivo,
   DataOraArrivo, Id_Tratta, Id_Convoglio) 
   VALUES ($departure, '$newDepartureDateTime', $arrival, '$arrivalDateTime', $routeId, $trainId)";
    $result = $conn->query($sql);
}

// Query to update subroute with delay
function updateSubrouteWithDelay($conn, $departure, $newDepartureDateTime, $arrival, $arrivalDateTime, $routeId)
{
    $sql = "UPDATE SubTratta SET StazionePartenza = $departure, DataOraPartenza = '$newDepartureDateTime', 
   StazioneArrivo = $arrival, DataOraArrivo = '$arrivalDateTime' 
   WHERE Id_Tratta = $routeId AND StazionePartenza = $departure AND StazioneArrivo = $arrival";
    $result = $conn->query($sql);
}

// Query to insert subroute without delay
function insertSubrouteNoDelay($conn, $departure, $currentDepartureTime, $arrival, $arrivalDateTime, $routeId, $trainId)
{
    $sql = "INSERT INTO SubTratta (StazionePartenza, DataOraPartenza, StazioneArrivo,
   DataOraArrivo, Id_Tratta, Id_Convoglio) 
   VALUES ($departure, '$currentDepartureTime', $arrival, '$arrivalDateTime', $routeId, $trainId)";
    $result = $conn->query($sql);
}

// Query to update subroute without delay
function updateSubrouteNoDelay($conn, $departure, $currentDepartureTime, $arrival, $arrivalDateTime, $routeId)
{
    $sql = "UPDATE SubTratta SET StazionePartenza = $departure, DataOraPartenza = '$currentDepartureTime', 
   StazioneArrivo = $arrival, DataOraArrivo = '$arrivalDateTime' 
   WHERE Id_Tratta = $routeId AND StazionePartenza = $departure AND StazioneArrivo = $arrival";
    $result = $conn->query($sql);
}

// Query to update final arrival time of route
function updateRouteFinalArrival($conn, $finalArrivalDateTime, $routeId)
{
    $sql = "UPDATE Tratta SET DataArrivo = '$finalArrivalDateTime' WHERE Id = $routeId";
    $result = $conn->query($sql);
}

// Query to find train
function findTrain($conn, $modifyId)
{
    $sql = "SELECT DISTINCT c.Nome
   FROM SubTratta AS st
   JOIN Convoglio AS c ON st.Id_Convoglio = c.Id
   WHERE st.Id_Tratta = $modifyId";
    $result = $conn->query($sql);
    $trainName = '';
    if ($result->num_rows > 0) {
        $rowTrain = $result->fetch_assoc();
        $trainName = $rowTrain['Nome'];
    }
    return $trainName;
}
?>