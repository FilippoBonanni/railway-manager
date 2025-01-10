<?php
include '../scripts/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = $_POST['utente'];
    $operation = $_POST['operazione'];
    $message_content = $_POST['messaggio'];

    // Query to insert the message into the request table
    $sql = "INSERT INTO Richiesta (Mittente, Operazione, Messaggio) VALUES
                ('$user', '$operation',' $message_content')";
    $result = $conn->query($sql);

    // Redirect to correct page if successful
    if ($result === TRUE) {
        header('Location: ../administrative/mail_sent.html');
    } else {
        $message = "Non è stato possibile inviarla";
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
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
                        <a class="nav-link" href="../administrative/administrative_office.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Contatta</a>
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
        <div class="container d-flex justify-content-center align-items-center">

            <div class="search-container login-container">

                <h1>Contatta Office Esercizio</h1>
                <?php if (isset($message)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif ?>
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-row">
                        <!-- User -->
                        <div class="form-group col-12">
                            <label for="utente">Utente</label>
                            <input type="text" id="utente" name="utente">
                        </div>
                        <!-- Message type -->
                        <div class="form-group col-12">
                            <label for="operazione">Seleziona Operazione</label>
                            <!-- Dropdown Menù -->
                            <select id="operazione" name="operazione">
                                <option value="" disabled selected>Seleziona Tipo</option>
                                <option value="Treni Straordinari">Treni Straordinari</option>
                                <option value="Cancellazione Tratta">Cancellazione Tratta</option>
                                <option value="Altro">Altro</option>
                            </select>
                        </div>
                        <!-- Message -->
                        <div class="form-group col-12">
                            <label for="messaggio">Messaggio</label>
                            <textarea rows=4 type="text" id="messaggio" name="messaggio"></textarea>
                        </div>

                        <!-- Send button -->
                        <div class="form-group col-12">
                            <button type="submit" class="search-btn">Invia</button>
                        </div>
                    </div>
                </form>

            </div>
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