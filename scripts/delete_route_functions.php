<?php
//Query to find train
function findTrain($conn, $routeId)
{
    $sql = "SELECT Id_Convoglio FROM SubTratta WHERE Id_Tratta = $routeId LIMIT 1";
    $result = $conn->query($sql);
    $trainId = 0;
    if ($result->num_rows > 0) {
        $rowTrain = $result->fetch_assoc();
        $trainId = $rowTrain['Id_Convoglio'];
    }
    return $trainId;
}

// Query to get departure date of selected route 
function getDepartureDate($conn, $routeId)
{
    $sql = "SELECT DataPartenza FROM Tratta WHERE Id = $routeId";
    $result = $conn->query($sql);
    $routeDeparture = 0;
    if ($result->num_rows > 0) {
        $rowRoute = $result->fetch_assoc();
        $routeDeparture = $rowRoute['DataPartenza'];
    }
    return $routeDeparture;
}

// Query to get arrival date of selected route
function getArrivalDate($conn, $routeId)
{
    $sql = "SELECT DataArrivo FROM Tratta WHERE Id = $routeId";
    $result = $conn->query($sql);
    $routeArrival = 0;
    if ($result->num_rows > 0) {
        $rowRoute = $result->fetch_assoc();
        $routeArrival = $rowRoute['DataArrivo'];
    }
    return $routeArrival;
}

// Query to find future routes of that train
function findRoutes($conn, $trainId, $routeArrival)
{
    $sql = "SELECT Id_Tratta FROM SubTratta 
    WHERE Id_Convoglio = $trainId AND DataOraPartenza > '$routeArrival'";
    $result = $conn->query($sql);
    $routes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $routes[] = $row['Id_Tratta'];
        }
    }
    $futureRoutes = array_unique($routes);
    return implode(',', $futureRoutes);
}

// Query to find past routes of that train
function findPastRoutes($conn, $trainId, $routeDeparture)
{
    $sql = "SELECT Id_Tratta FROM SubTratta
    WHERE Id_Convoglio = $trainId AND DataOraPartenza < '$routeDeparture'";
    $result = $conn->query($sql);
    $routes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $routes[] = $row['Id_Tratta'];
        }
    }
    $pastRoutes = array_unique($routes);
    return implode(',', $pastRoutes);
}

// Query to delete selected route
function deleteRoute($conn, $routeId)
{
    $sql = "DELETE FROM Tratta WHERE Id = $routeId";
    $result = $conn->query($sql);
}

// Query to delete future routes
function deleteFutureRoutes($conn, $futureRoutes)
{
    $sql = "DELETE FROM Tratta WHERE Id in ($futureRoutes)";
    $result = $conn->query($sql);
}

//Query to reset train to never used status
function resetToNeverUsed($conn, $trainId)
{
    $sql = "UPDATE Convoglio SET Stato = 'Mai Utilizzato' WHERE Id = $trainId";
    $result = $conn->query($sql);
}
?>