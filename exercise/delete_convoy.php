<?php
include '../scripts/db_connection.php';

session_start();

$sql = "SELECT * FROM Convoglio";
$result = $conn->query($sql);
// Store results in array
$convoys = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $convoys[] = $row;
    }
} else {
    $message2 = "Nessun Convoglio da eliminare.";
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if (isset($_POST['id']) && is_array($_POST['id'])) {
        // Get selected route IDs
        $convoysToDelete = $_POST['id'];
        // Array for successfully deleted convoy IDs
        $deletedConvoys = [];
        // Array for non-deletable convoy IDs
        $nonDeletableConvoys = [];

        foreach ($convoysToDelete as $selectedId) {
            $selectedId = intval($selectedId);

            // Check if convoy has future routes
            $sql = "SELECT Id FROM SubTratta 
                    WHERE Id_Convoglio = $selectedId AND DataOraArrivo > NOW()";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $nonDeletableConvoys[] = $selectedId;
            } else {
                // Query to identify convoy routes
                $sqlRoutes = "SELECT Id_Tratta FROM SubTratta WHERE Id_Convoglio = $selectedId";
                $resultRoutes = $conn->query($sqlRoutes);

                $routeIds = [];
                if ($resultRoutes->num_rows > 0) {
                    while ($row = $resultRoutes->fetch_assoc()) {
                        $routeIds[] = $row['Id_Tratta'];
                    }
                }

                if (!empty($routeIds)) {
                    $routesToDelete = implode(',', array_map('intval', $routeIds));
                    // Delete routes query
                    $deleteRoute = "DELETE FROM Tratta WHERE Id IN ($routesToDelete)";
                    $conn->query($deleteRoute);
                }

                // Delete convoy query
                $deleteConvoy = "DELETE FROM Convoglio WHERE Id = $selectedId";
                $conn->query($deleteConvoy);

                $deletedConvoys[] = $selectedId;
            }
        }

        // Create messages for deleted convoys
        if (!empty($deletedConvoys)) {
            $_SESSION['messaggioSuccesso'] = "Convoglio con Id: " . implode(",", $deletedConvoys) . " eliminati con successo.";
        }
        // Create messages for non-deleted convoys
        if (!empty($nonDeletableConvoys)) {
            $_SESSION['messaggioErrore'] = "Convoglio con Id: " . implode(",", $nonDeletableConvoys) . " non cancellabili perché già programmati per un viaggio.";
        }
        // Refresh page after operation
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['messaggioErrore'] = "Nessun convoglio selezionato.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Convoglio</title>
    <!-- Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="../styles_css/style.css">
    <!-- Font Awesome library link for icons -->
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
                        <a class="nav-link active" href="#">Elimina Convoglio</a>
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

    <!-- Main content -->
    <div class="content container-mobile p-4">
        <h1 style="color: #dc3545; font-weight: bold;">Seleziona il convoglio da eliminare</h1>
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

        <form method="POST" action="">
            <div class="email-list">
                <!-- Grid header -->
                <div class="row grid-header text-center align-items-center">
                    <div class="col-2">Id</div>
                    <div class="col-2">Nome</div>
                    <div class="col-2">Posti Disponibili</div>
                    <div class="col-2">Stato</div>
                    <div class="col-3">Seleziona</div>
                </div>
                <?php if (!empty($convoys)): ?>
                    <?php foreach ($convoys as $convoy): ?>
                        <div class="row email-item text-center align-items-center border-bottom py-3">
                            <div class="col-2">
                                <p><?php echo $convoy['Id'] ?></p>
                            </div>
                            <div class="col-2">
                                <p><?php echo $convoy['Nome'] ?></p>
                            </div>
                            <div class="col-2">
                                <p><?php echo $convoy['Posti_Disponibili'] ?></p>
                            </div>
                            <div class="col-2">
                                <p><?php echo $convoy['Stato'] ?></p>
                            </div>
                            <div class="col-3">
                                <input type="checkbox" name="id[]" value="<?php echo $convoy['Id'] ?>" class="form-check-input">
                            </div>
                        </div>
                    <?php endforeach ?>
                    <div class="text-end mt-4">
                        <button type="submit" name="delete" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Elimina
                        </button>
                    </div>
                <?php else: ?>
                    <p><?php echo $message2; ?></p>
                <?php endif ?>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; Società Ferrovie Turistiche - Tutti i diritti riservati</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>