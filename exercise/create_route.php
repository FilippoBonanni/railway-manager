<?php
include '../scripts/db_connection.php';
include '../scripts/create_route_functions.php';

// Set timezone to Rome (Europe/Rome)
date_default_timezone_set('Europe/Rome');
// Get all stations
$stations = getStations($conn);

$trains = [];
$message = "";
$firstForm = false;
$departureDateTime = '';

// Maintain selected values
$selected_departure = isset($_POST['partenza']) ? $_POST['partenza'] : '';
$selected_date = isset($_POST['dataPartenza']) ? $_POST['dataPartenza'] : '';
$selected_time = isset($_POST['orarioPartenza']) ? $_POST['orarioPartenza'] : '';

// Check if first part of form was submitted
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['continua'])) {
    if (isset($_POST['partenza']) && isset($_POST['dataPartenza']) && isset($_POST['orarioPartenza'])) {
        $departureStation = $_POST['partenza'];
        $date = $_POST['dataPartenza'];
        $time = $_POST['orarioPartenza'];

        $departureDate = date('Y-m-d', strtotime($date));
        $completeDepartureDate = $departureDate . ' ' . $time . ':00';
        $departureTimestamp = strtotime($completeDepartureDate);
        $departureDateTime = date('Y-m-d H:i:s', $departureTimestamp);

        if ($departureStation == 'Torre Spaventa') {
            // Get available trains
            $resultTrains = getAvailableTrains($conn, $departureStation, $departureDateTime);

            // Check if train has never been used
            $resultStatus = getTrainStatus($conn);
            if ($resultTrains->num_rows > 0) {
                while ($row = $resultTrains->fetch_assoc()) {
                    $trains[] = $row;
                }
                $firstForm = true;
            } else if ($resultStatus->num_rows > 0) {
                while ($row = $resultStatus->fetch_assoc()) {
                    $trains[] = $row;
                }
                $firstForm = true;
            } else {
                $message = "Nessun convoglio disponibile per la data e l'orario selezionati.";
            }
        } else {
            // Get available trains
            $resultTrains = getAvailableTrains($conn, $departureStation, $departureDateTime);

            if ($resultTrains->num_rows > 0) {
                while ($row = $resultTrains->fetch_assoc()) {
                    $trains[] = $row;
                }
                $firstForm = true;
            } else {
                $message = "Nessun convoglio disponibile per la data e l'orario selezionati.";
            }
        }
    }
}

// Check if second part of form was submitted
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['crea'])) {
    $departureStation = $_POST['partenza_hidden'];
    $arrivalStation = $_POST['arrivo'];
    $currentDepartureTime = $_POST['dataOraPartenza_hidden'];
    $train = $_POST['convoglio'];

    // Check if input train has never been used
    $trainStatus = neverUsed($conn, $train);
    // Update status if never used
    if ($trainStatus->num_rows > 0) {
        updateStatus($conn, $train);
    }

    // Get train seats
    $seats = getSeats($conn, $train);

    // Create route
    createRoute($conn, $departureStation, $currentDepartureTime, $arrivalStation, $seats);

    // Find departure station Id
    $sDeparture = getDepartureStationId($conn, $departureStation);

    // Find arrival station Id
    $sArrival = getArrivalStationId($conn, $arrivalStation);

    // Find train Id
    $trainId = getTrainId($conn, $train);

    // Query to get route Id
    $routeId = getRouteId($conn);

    // Initialize direction variable
    $direction = ($sDeparture < $sArrival) ? 'ascending' : 'descending';

    // Determine start, end and increment for loop
    $startStation = $sDeparture;
    $endStation = $sArrival;
    $increment = ($direction === 'ascending') ? 1 : -1;

    for (
        $currentStation = $startStation;
        ($direction === 'ascending') ? $currentStation < $endStation : $currentStation > $endStation;
        $currentStation += $increment
    ) {
        $departure = $currentStation;
        $arrival = $currentStation + $increment;

        // Get departure station kilometer
        $kmDeparture = getDepartureKm($conn, $departure);

        // Get arrival station kilometer
        $kmArrival = getArrivalKm($conn, $arrival);

        // Calculate arrival time
        $arrivalTime = abs($kmArrival - $kmDeparture) / 50 * 60;
        $arrivalTimestamp = strtotime($currentDepartureTime) + ($arrivalTime * 60);
        $arrivalDateTime = date('Y-m-d H:i:s', $arrivalTimestamp);

        // Check for connections
        $newDepartureDateTime = checkConnections($conn, $currentDepartureTime, $arrivalDateTime, $departure, $arrival, $trainId);
        if ($newDepartureDateTime != $departureDateTime) {
            // Check if it's the first station of the route
            if ($sDeparture == $departure) {
                // Modify route with new departure time
                updateDepartureTimeWithDelay($conn, $newDepartureDateTime, $routeId);
            }

            // Calculate times with delay
            $arrivalTime = abs($kmArrival - $kmDeparture) / 50 * 60;
            $arrivalTimestamp = strtotime($newDepartureDateTime) + ($arrivalTime * 60);
            $arrivalDateTime = date('Y-m-d H:i:s', $arrivalTimestamp);

            // Insert data with delay
            insertSubrouteWithDelay($conn, $departure, $newDepartureDateTime, $arrival, $arrivalDateTime, $routeId, $trainId);
            $currentDepartureTime = $arrivalDateTime;
            // Prepare next departure
            $currentDepartureTime = $arrivalDateTime;
            $date = new DateTime($arrivalDateTime);
            $date->modify('+2 minutes');
            $currentDepartureTime = $date->format('Y-m-d H:i:s');
        } else {
            // Calculate times without delays
            $arrivalTime = abs($kmArrival - $kmDeparture) / 50 * 60;
            $arrivalTimestamp = strtotime($currentDepartureTime) + ($arrivalTime * 60);
            $finalArrivalDateTime = date('Y-m-d H:i:s', $arrivalTimestamp);

            // Insert data without delay
            insertSubrouteNoDelay($conn, $departure, $currentDepartureTime, $arrival, $arrivalDateTime, $routeId, $trainId);
            // Prepare next departure
            $currentDepartureTime = $arrivalDateTime;
            $date = new DateTime($arrivalDateTime);
            $date->modify('+2 minutes');
            $currentDepartureTime = $date->format('Y-m-d H:i:s');
        }
    }
    // Insert final arrival time in route
    updateRouteFinalArrival($conn, $arrivalDateTime, $routeId);
    header("Location: create_route_home.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Corsa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles_css/style.css">
</head>

<body>
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
                        <a class="nav-link active" href="../exercise/create_route.php">Crea Corsa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../exercise/edit_route_home.php">Modifica Corsa</a>
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
                <h1 style="color: #007bff;">Crea Corsa</h1>
                <!-- Check for messages to display -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-warning" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif ?>
                <!-- First part of the form -->
                <?php if (!$firstForm): ?>
                    <form action="" method="POST">
                        <div class="form-row">
                            <!-- Departure station -->
                            <div class="form-group">
                                <label for="partenza">Stazione di Partenza</label>
                                <select id="partenza" name="partenza" required>
                                    <option value="" disabled selected>Seleziona stazione</option>
                                    <?php foreach ($stations as $station): ?>
                                        <option value="<?php echo $station['Nome'] ?>"
                                            <?php echo ($selected_departure == $station['Nome']) ? 'selected' : ''; ?>>
                                            <?php echo $station['Nome'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <!-- Departure date -->
                            <div class="form-group">
                                <label for="dataPartenza">Data di partenza</label>
                                <input type="date" id="dataPartenza" name="dataPartenza"
                                    value="<?php echo $selected_date ?>"
                                    min="<?php echo date('Y-m-d') ?>" required>
                            </div>
                            <!-- Departure time -->
                            <div class="form-group">
                                <label for="orarioPartenza">Orario di partenza</label>
                                <input type="time" id="orarioPartenza" name="orarioPartenza"
                                    value="<?php echo $selected_time; ?>" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="continua" class="btn btn-primary">Continua</button>
                            </div>
                        </div>
                        <!-- I prevent selecting a time earlier if the date is the current one -->
                        <script>
                            // Function that updates the time limit
                            document.getElementById('dataPartenza').addEventListener('change', function() {
                                var selectedDate = this.value;
                                var today = new Date().toISOString().split('T')[0];
                                var orarioPartenza = document.getElementById('orarioPartenza');
                                // If the selected date is today, set the time limit to the current time
                                if (selectedDate === today) {
                                    var currentTime = new Date().toTimeString().split(' ')[0].slice(0, 5);
                                    orarioPartenza.setAttribute('min', currentTime);
                                } else {
                                    // Otherwise, remove the time limit
                                    orarioPartenza.removeAttribute('min');
                                }
                            });
                        </script>
                    </form>
                <?php endif ?>
                <!-- Second part of the form -->
                <?php if ($firstForm): ?>
                    <form action="" method="POST">
                        <!-- Hidden fields to retain the values of the first part of the form -->
                        <input type="hidden" name="partenza_hidden" value="<?php echo htmlspecialchars($selected_departure); ?>">
                        <input type="hidden" name="data_hidden" value="<?php echo htmlspecialchars($selected_date); ?>">
                        <input type="hidden" name="orario_hidden" value="<?php echo htmlspecialchars($selected_time); ?>">
                        <input type="hidden" name="dataOraPartenza_hidden" value="<?php echo htmlspecialchars($departureDateTime); ?>">
                        <!-- Display the previously selected values -->
                        <div class="selected-values mb-4">
                            <h4>Dettagli selezionati:</h4>
                            <p>Stazione di partenza: <?php echo htmlspecialchars($selected_departure); ?></p>
                            <p>Data: <?php echo date("d/m/Y", strtotime($selected_date)); ?></p>
                            <p>Orario: <?php echo htmlspecialchars($selected_time); ?></p>
                        </div>
                        <!-- Convoy -->
                        <div class="form-group">
                            <label for="convoglio">Convoglio</label>
                            <select id="convoglio" name="convoglio" required>
                                <option value="" disabled selected>Seleziona convoglio</option>
                                <?php foreach ($trains as $train): ?>
                                    <option value="<?php echo $train['Nome']; ?>">
                                        <?php echo htmlspecialchars($train['Nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Arrival station -->
                        <div class="form-group">
                            <label for="arrivo">Stazione di Arrivo</label>
                            <select id="arrivo" name="arrivo" required>
                                <option value="" disabled selected>Seleziona stazione</option>
                                <?php foreach ($stations as $station): ?>
                                    <?php if ($station['Nome'] != $selected_departure): ?>
                                        <option value="<?php echo $station['Nome'] ?>">
                                            <?php echo $station['Nome'] ?>
                                        </option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <!-- Create button -->
                        <div class="form-group">
                            <button type="submit" name="crea" class="btn btn-success">Crea</button>
                        </div>
                    </form>
                <?php endif ?>
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