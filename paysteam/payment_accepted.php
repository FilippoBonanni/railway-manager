<?php
session_start();

include '../scripts/db_connection.php';
include '../scripts/purchase_functions.php';

// Get user type from session
$userType = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'Utente';
$profileText = ($userType == 'Esercente') ? 'Profilo Esercente' : 'Profilo Utente';

// Get routes array from session
$routes = isset($_SESSION['tratte']) ? $_SESSION['tratte'] : [];

// Handle both GET and POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $selectedRouteIndex = isset($_GET['indice']) ? (int)$_GET['indice'] : null;
    if ($selectedRouteIndex !== null && isset($routes[$selectedRouteIndex])) {
        $_SESSION['tratta_selezionata'] = $routes[$selectedRouteIndex];
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedRouteIndex = isset($_POST['indice']) ? (int)$_POST['indice'] : null;
    if ($selectedRouteIndex !== null && isset($routes[$selectedRouteIndex])) {
        $selectedRoute = $routes[$selectedRouteIndex];

        // Verify route selection and user authentication
        if ($selectedRoute !== null && isset($_SESSION['user_id'])) {
            // Get user ID from session
            $userId = $_SESSION['user_id'];
            // Get selected route details
            $routeId = $selectedRoute['id_tratta'];
            $passengers = $selectedRoute['passeggeri'];
            $price = $selectedRoute['prezzo'];
        }
        // Get account balance
        $balance = getBalance($conn, $userId);

        // Check card availability
        $creditCard = findCard($conn, $userId);

        if ($creditCard != null && $balance < $price) {
            // Update available seats
            subtractSeats($conn, $passengers, $routeId);
            // Generate random string for ticket
            $randomString = generateRandomString();
            // Insert ticket data
            insertTicket($conn, $price, $passengers, $randomString, $userId, $routeId);
            // Insert transaction data for user
            insertTransaction($conn, $price, $userId);
            // Insert transaction data for SFT merchant
            insertSFTTransaction($conn, $price);
            // Update SFT merchant balance
            updateSFTBalance($conn, $price);
        } else {
            // Update available seats
            subtractSeats($conn, $passengers, $routeId);
            // Generate random string for ticket
            $randomString = generateRandomString();
            // Insert ticket data 
            insertTicket($conn, $price, $passengers, $randomString, $userId, $routeId);
            // Insert transaction data for user
            insertTransaction($conn, $price, $userId);
            // Update user balance
            updateUserBalance($conn, $price, $userId);
            // Insert transaction data for SFT merchant
            insertSFTTransaction($conn, $price);
            // Update SFT merchant balance
            updateSFTBalance($conn, $price);
        }
        // Get final ticket
        $tickets = getTicket($conn, $userId);

        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acquisto Effettuato</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?php echo $profileText ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Acquisto Effettuato</a>
                    </li>
                    <li class="nav-item"></li>
                    <a class="nav-link" href="profile.php">Profilo</a>
                    </li>
                    <li>
                        <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main content -->
    <div class="content d-flex justify-content-center align-items-center">
        <div class="search-container">
            <form class="row"></form>
            <div class="form-group col-10 offset-1">
                <h1 style="color: #4caf50; font-weight: bold;">Acquisto Confermato!</h1>
                <?php foreach ($tickets as $ticket): ?>
                    <h2><?php echo $ticket['Nome'] ?> <?php echo $ticket['Cognome'] ?></h2>
                    <p>Grazie per aver usufruito dei servizi SFT</p>
                    <p>
                        <span style="font-size: 1.2em; font-weight: bold;">Codice Biglietto: </span>
                        <?php echo $ticket['CodiceBiglietto']; ?>
                    </p>
                    <p>
                        <span style="font-size: 1.2em; font-weight: bold;">Prezzo: </span>
                        <?php echo $ticket['Prezzo'] ?>€
                    </p>
                    <p>
                        <span style="font-size: 1.2em; font-weight: bold;">Posti: </span>
                        <?php echo $ticket['Posti'] ?>
                    </p>
                <?php endforeach ?>
            </div>

            <div class="form-group col-3 offset-7">
                <a href="../scripts/logout.php">
                    <button type="button" class="search-btn">Torna alla Home</button>
                </a>
            </div>

            </form>
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