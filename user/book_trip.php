<?php
session_start();

include '../scripts/db_connection.php';
include '../scripts/book_trip_functions.php';

// Check if POST data exists
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if date and time were entered
    if (!empty($_POST['dataPartenza']) && !empty($_POST['orarioPartenza'])) {
        // Store input values
        $departureStation = $_POST['partenza'];
        $departureDate = $_POST['dataPartenza'];
        $departureTime = $_POST['orarioPartenza'];
        $arrivalStation = $_POST['arrivo'];
        $passengers = $_POST['passeggeri'];

        // Combine date and time from input
        $departureTimestamp = $departureDate . ' ' . $departureTime . ':00';

        // Get departure station id
        $departureId = getDepartureId($conn, $departureStation);
        // Get arrival station id
        $arrivalId = getArrivalId($conn, $arrivalStation);
        // Get route id
        $routeIdsString = getRouteId($conn, $departureId, $arrivalId, $departureTimestamp);
        // Get train ids
        $trainIds = getTrainIds($conn, $departureId, $arrivalId, $departureTimestamp);

        $routes = [];

        // Get requested routes
        $resultRoutes = getRoutes($conn, $departureId, $arrivalId, $departureTimestamp, $departureDate);

        if ($resultRoutes->num_rows > 0) {
            while ($row = $resultRoutes->fetch_assoc()) {
                // Check seat availability for this specific route
                $seatsSql = "SELECT PostiRimasti 
                           FROM Tratta 
                           WHERE Id = {$row['Id_Tratta']}";
                $resultSeats = $conn->query($seatsSql);

                if ($resultSeats->num_rows > 0) {
                    $row_check = $resultSeats->fetch_assoc();
                    $available_seats = $row_check['PostiRimasti'];

                    if ($available_seats >= $passengers) {
                        // Calculate price
                        $priceQuery = "SELECT ABS(((s2.Km - s1.Km) * 0.4)* $passengers) AS Prezzo 
                                     FROM Stazione s1, Stazione s2 
                                     WHERE s1.Nome = '{$row['DepartureName']}' 
                                     AND s2.Nome = '{$row['ArrivalName']}'";
                        $priceResult = $conn->query($priceQuery);
                        $price = 0;

                        if ($priceResult->num_rows > 0) {
                            $priceRow = $priceResult->fetch_assoc();
                            $price = $priceRow['Prezzo'];
                        }

                        // Use unique key to avoid duplicates  
                        $uniqueKey = $row['DataOraPartenza'] . '_' . $row['Id_Convoglio'];

                        $routes[$uniqueKey] = [
                            'id_tratta' => $row['Id_Tratta'],
                            'passeggeri' => $passengers,
                            'id_convoglio' => $row['Id_Convoglio'],
                            'partenza' => $row['DepartureName'],
                            'arrivo' => $row['ArrivalName'],
                            'dataPartenza' => $row['DataOraPartenza'],
                            'dataArrivo' => $row['DataOraArrivo'],
                            'prezzo' => number_format($price, 2),
                        ];
                    }
                }
            }
        }

        // Convert associative array to numeric array
        $routes = array_values($routes);

        // Sort routes by departure date
        usort($routes, function ($a, $b) {
            return strtotime($a['dataPartenza']) - strtotime($b['dataPartenza']);
        });

        $_SESSION['tratte'] = $routes;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prenota Viaggio</title>
    <!-- Font Awesome library link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/style.css">

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Profilo Utente</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="../user/registered_user.html">Home</a>
                    </li>
                    <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content p-4" style="flex: 1">
        <!-- Grid header -->
        <div class="row grid-header text-center py-2">
            <div class="col-2">Partenza</div>
            <div class="col-2">Data e ora</div>
            <div class="col-2">Arrivo</div>
            <div class="col-2">Data e ora</div>
            <div class="col-2">Prezzo</div>
            <div class="col-2">Azione</div>
        </div>
        <!-- Check if routes were found -->
        <?php if (empty($routes)): ?>
            <p>Nessuna tratta trovata.</p>
        <?php else: ?>
            <?php foreach ($routes as $route): ?>
                <div class="row grid-row text-center align-items-center">
                    <div class="col-2">
                        <?php echo $route['partenza'] ?>
                    </div>
                    <div class="col-2">
                        <?php echo date("d/m/Y H:i", strtotime($route['dataPartenza'])) ?>
                    </div>
                    <div class="col-2">
                        <?php echo $route['arrivo'] ?>
                    </div>
                    <div class="col-2">
                        <?php echo date("d/m/Y H:i", strtotime($route['dataArrivo'])) ?>
                    </div>
                    <div class="col-2 price">
                        <?php echo $route['prezzo'] . '€' ?>
                    </div>
                    <div class="col-2">
                        <!-- Pass index -->
                        <a href="../paysteam/login_paysteam.php?indice=<?php echo $index ?>">
                            <button class="btn btn-primary">Prenota</button>
                        </a>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
    <!-- Footer -->
    <footer>
        <p>&copy; Società Ferrovie Turistiche - Tutti i diritti riservati</p>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>