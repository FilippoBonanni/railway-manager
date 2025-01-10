<?php
session_start();
include '../scripts/db_connection.php';
include '../scripts/convoy_functions.php';

// Get arrays for each type of rolling stock
$locomotives = getRollingStock($conn, 'Locomotiva');
$railcars = getRollingStock($conn, 'Automotrice');
$carriages = getRollingStock($conn, 'Carrozza');

// Handle convoy creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_convoglio'])) {
    // Convoy name from the input.
    $trainName = trim($_POST['nomeConvoglio']);
    // ID of the selected rolling stock.
    $selectedLocomotives = isset($_POST['locomotive']) ? $_POST['locomotive'] : [];
    $selectedRailcars = isset($_POST['automotrici']) ? $_POST['automotrici'] : [];
    $selectedCarriages = isset($_POST['carrozze']) ? $_POST['carrozze'] : [];
    // Check that at least one locomotive has been selected and no more than one.
    if (count($selectedLocomotives) < 1 && count($selectedRailcars) < 1) {
        $_SESSION['messaggio'] = "Devi selezionare almeno una locomotiva.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (count($selectedLocomotives) > 1) {
        $_SESSION['messaggio'] = "Devi selezionare solo una locomotiva.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (count($selectedLocomotives) == 1 && count($selectedCarriages) == 0 && count($selectedRailcars) == 0) {
        $_SESSION['messaggio'] = "Devi selezionare anche almeno una carrozza o un automotrice.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Convoy insertion.
        insertTrain($conn, $trainName);
        // ID of the convoy just created.
        $lastTrainId = $conn->insert_id;
        $union = [];

        switch (true) {
            case !empty($selectedLocomotives):
                $union = $selectedLocomotives;
                // Merging of the multiple units if selected.
                if (!empty($selectedRailcars)) {
                    $union = array_merge($union, $selectedRailcars);
                }
                // Merging of the carriages if selected.
                if (!empty($selectedCarriages)) {
                    $union = array_merge($union, $selectedCarriages);
                }
                break;

            case !empty($selectedRailcars):
                $union = $selectedRailcars;
                // Merging of the carriages if selected.
                if (!empty($selectedCarriages)) {
                    $union = array_merge($union, $selectedCarriages);
                }
                break;

            case !empty($selectedCarriages):
                $union = $selectedCarriages;
                break;
        }
        // Functions to update the convoy ID and the available seats.
        updateEquipment($conn, $lastTrainId, $union);
        updateSeats($conn, $lastTrainId);
        // Redirect to the page with a success message.
        $_SESSION['messaggioPositivo'] = "Convoglio creato con successo";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Convoglio</title>
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
                        <a class="nav-link active" href="#">Crea Convoglio</a>
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
    <!-- Main content -->
    <div class="content d-flex justify-content-center align-items-center">
        <div class="convoglio-container search-container">
            <h1 style="color: #007bff; font-weight: bold;">Seleziona i materiali rotabili</h1>
            <!-- Check for messages to display -->
            <?php if (isset($_SESSION['messaggio'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['messaggio'];
                    unset($_SESSION['messaggio']);
                    ?>
                </div>
            <?php endif ?>
            <?php if (isset($_SESSION['messaggioPositivo'])): ?>
                <div class="alert alert-info" role="alert">
                    <?php
                    echo $_SESSION['messaggioPositivo'];
                    unset($_SESSION['messaggioPositivo']);
                    ?>
                </div>
            <?php endif ?>
            <form method="post" class="row">
                <!-- Train name -->
                <div class="nome-convoglio-input">
                    <h4>Nome Treno</h4>
                    <input type="text" name="nomeConvoglio" class="form-control" placeholder="Crea Nome" required>
                </div>
                <!-- Locomotives -->
                <div class="rotabile-section">
                    <h2>Locomotive:</h2>
                    <div class="rotabile-grid">
                        <?php foreach ($locomotives as $loc): ?>
                            <div class="rotabile-item">
                                <input type="checkbox" name="locomotive[]" value="<?php echo $loc['Id']; ?>" class="form-check-input">
                                <div class="rotabile-info">
                                    <h3><?php echo $loc['Nome'] . " " . $loc['Serie'] ?></h3>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <!-- Multiple units -->
                <div class="rotabile-section">
                    <h2>Automotrici:</h2>
                    <div class="rotabile-grid">
                        <?php foreach ($railcars as $railcar): ?>
                            <div class="rotabile-item">
                                <input type="checkbox" name="automotrici[]" value="<?php echo $railcar['Id']; ?>" class="form-check-input">
                                <div class="rotabile-info">
                                    <h3><?php echo $railcar['Nome'] . " " . $railcar['Serie'] ?></h3>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <!-- Carriages -->
                <div class="rotabile-section">
                    <h2>Carrozze:</h2>
                    <div class="rotabile-grid">
                        <?php foreach ($carriages as $carriage): ?>
                            <div class="rotabile-item">
                                <input type="checkbox" name="carrozze[]" value="<?php echo $carriage['Id']; ?>" class="form-check-input">
                                <div class="rotabile-info">
                                    <h3><?php echo $carriage['Nome'] . " " . $carriage['Serie'] ?></h3>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <!-- Button -->
                <div class="text-center">
                    <button type="submit" name="create_convoglio" class="btn btn-primary btn-crea-convoglio">
                        Crea Convoglio
                    </button>
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