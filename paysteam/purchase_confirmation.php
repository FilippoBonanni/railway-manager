<?php
session_start();
include '../scripts/db_connection.php';
include '../scripts/purchase_functions.php';

// Get user type from session
$userType = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'Utente';
$profileText = ($userType == 'Esercente') ? 'Profilo Esercente' : 'Profilo Utente';

// Get routes array from session
$routes = isset($_SESSION['tratte']) ? $_SESSION['tratte'] : [];

// Get selected route index
$selectedRouteIndex = isset($_GET['indice']) ? (int)$_GET['indice'] : null;
$selectedRoute = null;
$message = null;
$sufficientBalance = false;

if ($selectedRouteIndex !== null && isset($routes[$selectedRouteIndex])) {
    $selectedRoute = $routes[$selectedRouteIndex];
}

if ($selectedRoute && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $price = $selectedRoute['prezzo'];

    // Get account balance
    $balance = getBalance($conn, $userId);

    if ($balance >= $price) {
        $sufficientBalance = true;
    } else {
        // Check if credit card exists
        $creditCard = checkCard($conn, $userId);
        if ($creditCard == null) {
            $message = "Saldo insufficiente e nessuna carta di credito disponibile.";
        } else {
            $sufficientBalance = true;
        }
    }
}

// Redirect if balance is sufficient and user confirmed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $sufficientBalance) {
    header("Location: payment_accepted.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Acquisto</title>
    <!-- Font Awesome -->
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
            <a class="navbar-brand" href="#"><?php echo $profileText; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Acquista</a>
                    <li>
                        <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Check for messages -->
    <div class="content p-4">
        <?php if ($message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $message ?>
            </div>
        <?php endif ?>
        <!-- Grid header -->
        <div class="row grid-header text-center py-2">
            <div class="col-2">Partenza</div>
            <div class="col-2">Data e ora</div>
            <div class="col-2">Arrivo</div>
            <div class="col-2">Data e ora</div>
            <div class="col-2">Prezzo</div>
            <div class="col-2">Azione</div>
        </div>
        <!-- Show selected route -->
        <?php if ($selectedRoute): ?>
            <div class="row grid-row text-center align-items-center">
                <div class="col-2">
                    <?php echo $selectedRoute['partenza'] ?>
                </div>
                <div class="col-2">
                    <?php echo date("d/m/Y H:i", strtotime($selectedRoute['dataPartenza'])) ?>
                </div>
                <div class="col-2">
                    <?php echo $selectedRoute['arrivo'] ?>
                </div>
                <div class="col-2">
                    <?php echo date("d/m/Y H:i", strtotime($selectedRoute['dataArrivo'])) ?>
                </div>
                <div class="col-2 price">
                    <?php echo $selectedRoute['prezzo'] . '€' ?>
                </div>
                <div class="col-2 d-flex">
                    <form method="POST" action="payment_accepted.php">
                        <input type="hidden" name="indice" value="<?php echo $selectedRouteIndex; ?>">
                        <button type="submit" class="btn btn-primary me-1" <?php if (!$sufficientBalance) echo 'disabled' ?>>Conferma</button>
                    </form>
                    <a class="nav-link" href="../user/registered_user.php">
                        <button class="btn btn-danger">Annulla</button>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row grid-row text-center align-items-center">
                <div class="col-12">
                    <p>Non ci sono tratte selezionate.</p>
                </div>
            </div>
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