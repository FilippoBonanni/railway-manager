<?php
include '../scripts/db_connection.php';
include '../scripts/pagination.php';
session_start();

// Store results in array
$routes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
} else {
    $message = "Nessuna tratta ancora programmata.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Corsa</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                        <a class="nav-link active" href="#">Crea Corsa</a>
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
    <div class="content container-mobile p-4">
        <h1 style="color: #007bff; font-weight: bold">Crea una nuova corsa</h1>
        <!-- Page navigation -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php if ($current_page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=<?php echo $current_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $current_page) echo 'active'; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?pagina=<?php echo $current_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="email-list">
            <!-- Grid header -->
            <div class="row grid-header text-center align-items-center">
                <div class="col-2">Stazione Partenza</div>
                <div class="col-2">Data e Ora Partenza</div>
                <div class="col-2">Stazione Arrivo</div>
                <div class="col-2">Data e Ora Arrivo</div>
            </div>
            <?php if (!empty($routes)): ?>
                <?php foreach ($routes as $route): ?>
                    <div class="row email-item text-center align-items-center border-bottom py-3">
                        <div class="col-2">
                            <p><?php echo $route['Partenza'] ?></p>
                        </div>
                        <div class="col-2">
                            <p><?php echo date("d/m/Y H:i", strtotime($route['DataPartenza'])) ?></p>
                        </div>
                        <div class="col-2">
                            <p><?php echo $route['Arrivo'] ?></p>
                        </div>
                        <div class="col-2">
                            <p><?php echo date("d/m/Y H:i", strtotime($route['DataArrivo'])) ?></p>
                        </div>
                        <div class="col-3">
                            <form method="POST" action="route_details.php">
                                <input type="hidden" name="dettagli_id" value="<?php echo $route['Id'] ?>">
                                <button type="submit" class="btn" style="color: #00695C; font-weight: bold">Dettagli</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach ?>
                <div class="text-end mt-4">
                    <a class="btn btn-primary" href="create_route.php">Aggiungi Corsa</a>
                </div>
            <?php else: ?>
                <p><?php echo $message ?></p>
                <div class="text-end mt-4">
                    <a class="btn btn-primary" href="create_route.php">Aggiungi Corsa</a>
                </div>
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