<?php
include '../scripts/db_connection.php';
session_start();

// Get error message from session
$errorMessage = isset($_SESSION['messaggioErrore']) ? $_SESSION['messaggioErrore'] : null;

// Reset message after displaying
unset($_SESSION['messaggioErrore']);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Registrazione PaySteam</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Registrazione</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../paysteam/login_paysteam.php">Torna al login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Check if there are messages to display -->
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errorMessage ?>
        </div>
    <?php endif ?>
    <!-- Main content -->
    <div class="content d-flex justify-content-center align-items-center">
        <div class="search-container registration-container">
            <h1>Registrazione PaySteam</h1>
            <form class="row" action="../scripts/register_paysteam.php" method="POST">
                <!-- First name -->
                <div class="form-group col-6">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Nome">
                </div>

                <!-- Last name -->
                <div class="form-group col-6">
                    <label for="cognome">Cognome</label>
                    <input type="text" id="cognome" name="cognome" class="form-control" placeholder="Cognome">
                </div>
                <!-- Username -->
                <div class="form-group col-6">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="Email">
                </div>
                <!-- User type -->
                <div class="form-group col-6">
                    <label for="tipo">Seleziona tipo di utente</label>
                    <!-- Dropdown menu -->
                    <select id="tipo" name="tipo">
                        <option value="" disabled selected>Seleziona tipo</option>
                        <option value="Utente Registrato">Utente Registrato</option>
                        <option value="Esercente">Esercente</option>
                    </select>
                </div>
                <!-- Password -->
                <div class="form-group col-6">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Crea Password">
                </div>
                <!-- Confirm Password -->
                <div class="form-group col-6">
                    <label for="conferma_password">Conferma Password</label>
                    <input type="password" id="conferma_password" name="conferma_password" class="form-control" placeholder="Conferma Password">
                </div>

                <!-- Register button -->
                <div class="form-group col-12">
                    <button type="submit" class="search-btn">Registra</button>
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