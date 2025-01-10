<?php
include '../scripts/db_connection.php';
include '../scripts/pagination.php';
include '../scripts/delete_route_functions.php';
session_start();

// Store results in an array
$routes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
} else {
    $message = "Nessuna tratta da modificare.";
}
// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['elimina_id'])) {
    // Get passed ID
    $routeId = intval($_POST['elimina_id']);
    // Find train ID
    $trainId = findTrain($conn, $routeId);
    // Find route departure date
    $routeDeparture = getDepartureDate($conn, $routeId);
    // Convert departure date to DateTime object
    $departureDateObj = new DateTime($routeDeparture);
    if ($departureDateObj > new DateTime()) {
        // Find route arrival date
        $routeArrival = getArrivalDate($conn, $routeId);
        $futureRoutes = findRoutes($conn, $trainId, $routeArrival);
        if (!empty($futureRoutes)) {
            deleteRoute($conn, $routeId);
            deleteFutureRoutes($conn, $futureRoutes);
            $_SESSION['messaggioSuccesso'] = "Tratta eliminata con successo e quelle successive legata ad essa.";
        } else {
            $pastRoutes = findPastRoutes($conn, $trainId, $routeDeparture);
            echo $pastRoutes;
            if (!empty($pastRoutes)) {
                deleteRoute($conn, $routeId);
                $_SESSION['messaggioSuccesso'] = "Tratta eliminata con successo.";
            } else {
                deleteRoute($conn, $routeId);
                resetToNeverUsed($conn, $trainId);
                $_SESSION['messaggioSuccesso'] = "Tratta eliminata con successo.";
            }
        }
    } else {
        $_SESSION['messaggioErrore'] = "La tratta non può essere eliminata perchè già conclusa";
    }

    // Refresh page after operation
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifica_id'])) {
    // Get passed ID
    $routeId = intval($_POST['modifica_id']);
    // Find train ID
    $trainId = findTrain($conn, $routeId);
    // Find route departure date
    $routeDeparture = getDepartureDate($conn, $routeId);
    // Convert departure date to DateTime object
    $departureDateObj = new DateTime($routeDeparture);
    if ($departureDateObj > new DateTime()) {
        // Find route arrival date
        $routeArrival = getArrivalDate($conn, $routeId);
        // Check if train has other routes after this one
        $futureRoutes = findRoutes($conn, $trainId, $routeArrival);

        if (!empty($futureRoutes)) {
            $_SESSION['messaggioErrore'] = "Tratta non modificabile perchè presenti altre dopo.";
        } else {
            $_SESSION['modifica_id'] = $routeId;
            header("Location: edit_route.php");
            exit();
        }
    } else {
        $_SESSION['messaggioErrore'] = "Tratta non modificabile perchè già avvenuta.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica corsa</title>
    <!-- Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS link -->
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
    <div class="content container-mobile p-4" style="flex: 1">
        <h1 style="color: #007bff; font-weight: bold;">Seleziona la tratta da modificare</h1>
        <!-- Page navigation -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php if ($pagina_corrente <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_corrente - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $totale_pagine; $i++): ?>
                    <li class="page-item <?php if ($i == $pagina_corrente) echo 'active'; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($pagina_corrente >= $totale_pagine) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_corrente + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Check for messages to display -->
        <?php if (isset($_SESSION['messaggioSuccesso'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['messaggioSuccesso'] ?>
            </div>
            <?php unset($_SESSION['messaggioSuccesso']) ?>
        <?php endif ?>

        <?php if (isset($_SESSION['messaggioErrore'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['messaggioErrore'] ?>
            </div>
            <?php unset($_SESSION['messaggioErrore']) ?>
        <?php endif ?>

        <!-- Email style -->
        <div class="email-list">
            <!-- Grid header -->
            <div class="row grid-header text-center align-items-center">
                <div class="col-2">Stazione Partenza</div>
                <div class="col-2">Data e Ora Partenza</div>
                <div class="col-2">Stazione Arrivo</div>
                <div class="col-2">Data e Ora Arrivo</div>
            </div>
            <!-- Email item with checkbox -->
            <?php if (!empty($routes)): ?>
                <?php foreach ($routes as $route): ?>
                    <div class="row email-item text-center align-items-center border-bottom py-3">
                        <div class="col-2">
                            <p> <?php echo $route['Partenza']; ?> </p>
                        </div>
                        <div class="col-2">
                            <p> <?php echo date("d/m/Y H:i", strtotime($route['DataPartenza'])) ?> </p>
                        </div>
                        <div class="col-2">
                            <p> <?php echo $route['Arrivo']; ?> </p>
                        </div>
                        <div class="col-2">
                            <p> <?php echo date("d/m/Y H:i", strtotime($route['DataArrivo'])) ?> </p>
                        </div>
                        <div class="col-3 d-flex justify-content-end gap-2">
                            <form method="POST" action="">
                                <input type="hidden" name="modifica_id" value="<?php echo $route['Id']; ?>">
                                <button type="submit" class="btn btn-primary">Modifica</button>
                            </form>
                            <form method="POST" action="">
                                <input type="hidden" name="elimina_id" value="<?php echo $route['Id'] ?>">
                                <button type="submit" name="delete" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <p><?php echo $message ?></p>
            <?php endif ?>
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