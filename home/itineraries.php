<?php
include '../scripts/db_connection.php';
include '../scripts/pagination.php';

// Store results in array
$routes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
} else {
    $message = "Nessuna tratta ancora caricata.";
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tratte</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Le nostre Tratte</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../home/login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section class="hero-section text-white text-center p-5">
        <h1>I nostri viaggi</h1>
        <p>Scopri le tratte che ricopre la nostra linea</p>
    </section>

    <div class="container container-mobile p-4">
        <!-- Page Navigation -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php if ($current_page <= 1) echo 'disabled' ?>">
                    <a class="page-link" href="?pagina=<?php echo $current_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $current_page) echo 'active' ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled' ?>">
                    <a class="page-link" href="?pagina=<?php echo $current_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Grid header -->
        <div class="row grid-header text-center align-items-center">
            <div class="col-3">Stazione Partenza</div>
            <div class="col-3">Data e Ora Partenza</div>
            <div class="col-3">Stazione Arrivo</div>
            <div class="col-3">Data e Ora Arrivo</div>
        </div>

        <!-- Show routes -->
        <?php if (!empty($routes)): ?>
            <?php foreach ($routes as $route): ?>
                <div class="row grid-row text-center align-items-center">
                    <div class="col-3"><?php echo $route['Partenza'] ?></div>
                    <div class="col-3"><?php echo date("d/m/Y H:i", strtotime($route['DataPartenza'])) ?></div>
                    <div class="col-3"><?php echo $route['Arrivo'] ?></div>
                    <div class="col-3"><?php echo date("d/m/Y H:i", strtotime($route['DataArrivo'])) ?></div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <p><?php echo $message ?></p>
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