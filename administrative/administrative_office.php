<?php
include '../scripts/db_connection.php';
include '../scripts/pagination.php';
session_start();

// Insert results into an array
$routes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
} else {
    $message = "Nessuna tratta presente.";
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Amministrativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CSS Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- My CSS file Link -->
    <link rel="stylesheet" href="../styles_css/style.css">

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Profilo Backoffice Amministrativo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../administrative/mail_exercise.php">Contatta</a>
                    </li>
                    <li>
                        <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content container-mobile p-4" style="flex: 1">
        <!-- Page Navigation -->
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
        <!-- Grid header -->
        <div class="row grid-header text-center align-items-center">
            <!-- <div class="col-2">Id Tratta</div> -->
            <div class="col-3">Stazione Partenza</div>
            <div class="col-2">Data e Ora Partenza</div>
            <div class="col-2">Stazione Arrivo</div>
            <div class="col-2">Data e Ora Arrivo</div>
            <div class="col-3">Posti Disponibili</div>
        </div>

        <!-- Print route table elements -->
        <?php if (!empty($routes)): ?>
            <?php foreach ($routes as $route): ?>
                <div class="row grid-row text-center align-items-center">

                    <div class="col-3">
                        <p> <?php echo $route['Partenza'] ?> </p>
                    </div>
                    <div class="col-2">
                        <p> <?php echo date("d/m/Y H:i", strtotime($route['DataPartenza'])) ?> </p>
                    </div>
                    <div class="col-2">
                        <p> <?php echo $route['Arrivo'] ?> </p>
                    </div>
                    <div class="col-2">
                        <p> <?php echo date("d/m/Y H:i", strtotime($route['DataArrivo'])) ?> </p>
                    </div>
                    <div class="col-3">
                        <p> <?php echo $route['PostiRimasti']; ?> </p>
                    </div>
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