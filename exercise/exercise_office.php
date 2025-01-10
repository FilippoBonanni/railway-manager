<?php
include '../scripts/db_connection.php';
include '../scripts/office_pagination.php';

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['elimina_id'])) {
    // Get the ID to delete
    $delete_id = intval($_POST['elimina_id']);

    // Delete query
    $sql = "DELETE FROM Richiesta WHERE Id = $delete_id";

    // If successful, redirect to the same page
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Insert results into array
$requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
} else {
    $message = "Nessun messaggio ricevuto.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posta in arrivo</title>
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
                        <a class="nav-link active" href="#">Home</a>
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
                        <a class="nav-link" href="../exercise/edit_route_home.php">Modifica Corsa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Pagination handling -->
    <div class="content container-mobile p-4">
        <h1 style="color: #007bff; font-weight: bold;">Posta in arrivo</h1>
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
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $request): ?>
                    <h2><?php echo $request['Mittente']; ?></h2>
                    <!-- Email box -->
                    <div class="email-item d-flex justify-content-between align-items-start border-bottom py-3">
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <h5 class="mb-0 me-2">Data:</h5>
                                <p class="text-muted mb-0">
                                    <?php echo date("d/m/Y H:i", strtotime($request['Data'])) ?>
                                </p>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <h5 class="mb-0 me-2">Operazione:</h5>
                                <p class="text-muted mb-0"><?php echo $request['Operazione']; ?></p>
                            </div>
                            <div class="d-flex align-items-start">
                                <h5 class="mb-0 me-2">Messaggio:</h5>
                                <p class="text-muted mb-0" style="word-wrap: break-word; max-width: 865px;">
                                    <?php echo $request['Messaggio']; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Delete button to remove email -->
                        <div class="text-end mt-4">
                            <form method="POST" action="">
                                <input type="hidden" name="elimina_id" value="<?php echo $request['Id']; ?>">
                                <button type="submit" class="btn btn-primary">Elimina</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
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