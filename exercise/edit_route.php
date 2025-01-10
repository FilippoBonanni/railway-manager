<?php
include '../scripts/db_connection.php';
include '../scripts/create_route_functions.php';
session_start();


if (isset($_SESSION['modifica_id'])) {
    // Get the route ID passed through session
    $route_id = intval($_SESSION['modifica_id']);
    // Execute query to get the route
    $sql = "SELECT * FROM Tratta WHERE Id = $route_id";
    $result = $conn->query($sql);

    // Check if there's a result
    if ($result->num_rows > 0) {
        // Directly retrieve the first row as associative array
        $route = $result->fetch_assoc();

        // Convert timestamp to date
        if (isset($route['DataPartenza'])) {
            $timestamp = strtotime($route['DataPartenza']);
            $route['DataPartenza'] = date('Y-m-d', $timestamp);
        }
    }

    // Find the train
    $trainName = findTrain($conn, $route_id);
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['modifica'])) {
    if (
        isset($_POST['partenza']) && isset($_POST['dataPartenza']) && isset($_POST['orarioPartenza'])
        && isset($_POST['partenza']) && isset($_POST['arrivo'])
    ) {
        // Get input variables
        $departureStation = $_POST['partenza'];
        $arrivalStation = $_POST['arrivo'];
        $departureDate = $_POST['dataPartenza'];
        $departureTime = $_POST['orarioPartenza'];
        $routeId = $_POST['modifica_id'];

        // Combine date and time into a timestamp
        $date = date('Y-m-d', strtotime($route['DataPartenza']));
        $completeDepartureDate = $date . ' ' . $departureTime . ':00';
        $departureTimestamp = strtotime($completeDepartureDate);
        $departureDateTime = date('Y-m-d H:i:s', $departureTimestamp);

        // Find departure station ID
        $dStation = getDepartureStationId($conn, $departureStation);

        // Find arrival station ID
        $aStation = getArrivalStationId($conn, $arrivalStation);

        // Get train ID
        $trainId = getTrainIdFromRoute($conn, $routeId);

        $direction = ($dStation < $aStation) ? 'ascending' : 'descending';

        // Determine start, end and increment for the for loop
        $startStation = $dStation;
        $endStation = $aStation;
        $increment = ($direction === 'ascending') ? 1 : -1;

        for (
            $currentStation = $startStation;
            ($direction === 'ascending') ? $currentStation < $endStation : $currentStation > $endStation;
            $currentStation += $increment
        ) {

            $departure = $currentStation;
            $arrival = $currentStation + $increment;
            // Get departure station kilometer
            $kmD = getDepartureKm($conn, $departure);

            // Get arrival station kilometer
            $kmA = getArrivalKm($conn, $arrival);

            // Calculate arrival time
            $arrivalTime = abs($kmA - $kmD) / 50 * 60;
            $arrivalTimestamp = strtotime($departureDateTime) + ($arrivalTime * 60);
            $arrivalDateTime = date('Y-m-d H:i:s', $arrivalTimestamp);

            // Check for connections
            $newDepartureDateTime = checkConnections($conn, $departureDateTime, $arrivalDateTime, $departure, $arrival, $trainId);

            if ($newDepartureDateTime != $departureDateTime) {
                // Check if it's the first station of the route
                if ($dStation == $departure) {
                    // Modify route with new departure time
                    updateDepartureTimeWithDelay($conn, $newDepartureDateTime, $routeId);
                }
                // Calculate times with delay
                $arrivalTime = abs($kmA - $kmD) / 50 * 60;
                $arrivalTimestamp = strtotime($newDepartureDateTime) + ($arrivalTime * 60);
                $arrivalDateTime = date('Y-m-d H:i:s', $arrivalTimestamp);

                // Insert data with delay
                updateSubrouteWithDelay($conn, $departure, $newDepartureDateTime, $arrival, $arrivalDateTime, $routeId);
                // Prepare next departure
                $departureDateTime = $arrivalDateTime;
                $date = new DateTime($arrivalDateTime);
                $date->modify('+2 minutes');
                $departureDateTime = $date->format('Y-m-d H:i:s');
            } else {
                // Check if it's the first station of the route
                if ($dStation == $departure) {
                    // Modify route with new departure time
                    updateDepartureTimeNoDelay($conn, $departureDateTime, $routeId);
                }
                // Insert data without delay
                updateSubrouteNoDelay($conn, $departure, $departureDateTime, $arrival, $arrivalDateTime, $routeId);

                // Prepare next departure
                $departureDateTime = $arrivalDateTime;
                $date = new DateTime($arrivalDateTime);
                $date->modify('+2 minutes');
                $departureDateTime = $date->format('Y-m-d H:i:s');
            }
        }

        // Insert final arrival time in the route
        updateRouteFinalArrival($conn, $arrivalDateTime, $routeId);
    }
    $_SESSION["messaggioSuccesso"] = "Tratta Aggiornata con successo.";
    header("Location: edit_route_home.php?messaggio=" . urlencode($messaggio));
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica corsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles_css/style.css">
</head>

<body>
    <?php echo $htmlErrors; ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Profilo Backoffice di Esercizio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../exercise/exercise_office.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../exercise/create_convoy.php">Crea Convoglio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../exercise/delete_convoy.php">Elimina Convoglio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../exercise/create_route_home.php">Crea Corsa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Modifica Corsa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main content -->
    <div class="content p-4">
        <div class="container d-flex justify-content-center align-items-center">
            <div class="search-container">
                <h1 style="color: #007bff;">Modifica Corsa</h1>
                <?php if (isset($messaggio)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $messaggio; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="form-row">
                        <!-- Hidden field for route ID -->
                        <input type="hidden" name="modifica_id" value="<?php echo $route_id ?>">

                        <!-- Departure time -->
                        <div class="form-group">
                            <label for="orarioPartenza">Orario di partenza</label>
                            <input type="time" id="orarioPartenza" name="orarioPartenza" required>
                        </div>

                        </script>
                        <!-- Departure date -->
                        <div class="form-group">
                            <label for="dataPartenza">Data di Partenza</label>
                            <p id="dataPartenza"><?php echo $route['DataPartenza'] ?></p>
                            <input type="hidden" name="dataPartenza" value="<?php echo $route['DataPartenza'] ?>">
                        </div>

                        <!-- Departure station -->
                        <div class="form-group">
                            <label for="partenza">Stazione di Partenza</label>
                            <p id="partenza"><?php echo $route['Partenza']; ?></p>
                            <input type="hidden" name="partenza" value="<?php echo $route['Partenza'] ?>">
                        </div>

                        <!-- Arrival station -->
                        <div class="form-group">
                            <label for="arrivo">Stazione di Arrivo</label>
                            <p id="arrivo"><?php echo $route['Arrivo']; ?></p>
                            <input type="hidden" name="arrivo" value="<?php echo $route['Arrivo'] ?>">
                        </div>

                        <!-- Choose available train -->
                        <div class="form-group">
                            <label for="partenza">Convoglio</label>
                            <p id="convoglio"><?php echo $trainName; ?></p>
                        </div>

                        <!-- Modify button -->
                        <div class="form-group">
                            <button type="submit" name="modifica" class="search-btn">Modifica</button>
                        </div>
                    </div>
                    <script>
                        // Function to update time limit
                        document.addEventListener('DOMContentLoaded', function() {
                            var departureDateElement = document.getElementById('dataPartenza');
                            var departureTimeElement = document.getElementById('orarioPartenza');

                            // Check if departure date is today
                            function isToday(dateString) {
                                var today = new Date().toISOString().split('T')[0];
                                return dateString === today;
                            }

                            // Set time limit if date is today
                            if (isToday(departureDateElement.textContent.trim())) {
                                var currentTime = new Date().toTimeString().split(' ')[0].slice(0, 5);
                                departureTimeElement.setAttribute('min', currentTime);
                            }
                        });
                    </script>
                </form>

            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer>
        <p>&copy; Società Ferrovie Turistiche - Tutti i diritti riservati</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>