<?php
include '../scripts/db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dettagli_id'])) {
    // Get the passed ID
    $edit_id = intval($_POST['dettagli_id']);

    // Print all sub-routes of the selected route
    $subroutes = [];
    $sql = "SELECT s1.Nome AS Partenza, sb.DataOraPartenza, s2.Nome AS Arrivo, 
                sb.DataOraArrivo, c.Nome AS Convoglio
            FROM SubTratta AS sb
            JOIN Convoglio AS c ON sb.Id_Convoglio = c.Id
            JOIN Stazione AS s1 ON sb.StazionePartenza = s1.Id
            JOIN Stazione AS s2 ON sb.StazioneArrivo = s2.Id
            WHERE Id_Tratta = $edit_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subroutes[] = $row;
        }
    } else {
        $message = "Errore nella visualizzazione dei dettagli";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli corsa</title>
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
                        <a class="nav-link active" href="#">Dettagli Corsa</a>
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
    <div class="content container-mobile p-4">
        <div class="email-list">
            <!-- Grid header -->
            <div class="row grid-header text-center align-items-center">
                <h5>Convoglio: <?php echo $subroutes[0]['Convoglio'] ?><br><br></h5>
                <div class="col-3">Stazione Partenza</div>
                <div class="col-3">Data e Ora Partenza</div>
                <div class="col-3">Stazione Arrivo</div>
                <div class="col-3">Data e Ora Arrivo</div>
            </div>
            <!-- Print elements of the sub-route table -->
            <?php if (!empty($subroutes)): ?>
                <?php foreach ($subroutes as $subroute): ?>
                    <div class="row grid-row text-center align-items-center">

                        <div class="col-3">
                            <p> <?php echo $subroute['Partenza'] ?> </p>
                        </div>
                        <div class="col-3">
                            <p> <?php echo date("d/m/Y H:i", strtotime($subroute['DataOraPartenza'])) ?> </p>
                        </div>
                        <div class="col-3">
                            <p> <?php echo $subroute['Arrivo'] ?> </p>
                        </div>
                        <div class="col-3">
                            <p> <?php echo date("d/m/Y H:i", strtotime($subroute['DataOraArrivo'])) ?> </p>
                        </div>
                    </div>
                <?php endforeach ?>
                <div class="text-end mt-4">
                    <a class="btn btn-primary" href="create_route_home.php">Torna a crea corsa</a>
                </div>
            <?php else: ?>
                <p><?php echo $message ?></p>
                <div class="text-end mt-4">
                    <a class="btn btn-primary" href="create_route_home.php">Torna a crea corsa</a>
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