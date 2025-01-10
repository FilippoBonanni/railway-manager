<?php session_start(); ?>
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
            <a class="navbar-brand" href="#">Login PaySteam</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Login Paysteam</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main content -->
    <div class="content d-flex justify-content-center align-items-center">
        <div class="search-container login-container">
            <h1>Login PaySteam</h1>
            <!-- Check if there are messages to display -->
            <?php if (isset($_SESSION['errore_msg'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['errore_msg']; ?>
                </div>
                <?php unset($_SESSION['errore_msg']); ?>
            <?php endif ?>
            <form class="row" action="../scripts/paysteam_login_management.php" method="POST">
                <!-- Username -->
                <div class="form-group col-12">
                    <label for="Username">Username</label>
                    <input type="text" id="Username" name="Username" class="form-control" placeholder="Email">
                </div>
                <!-- Password -->
                <div class="form-group col-12">
                    <label for="Password">Password</label>
                    <input type="password" id="Password" name="Password" class="form-control" placeholder="Password">
                </div>
                <!-- Credentials -->
                <p style="font-size: 0.8em;">
                    Email: <span style="color: #007bff">utente@paysteam.it</span> Pw: <span style="color: #007bff">utente2024</span>
                </p>
                <p style="font-size: 0.8em;">
                    Email: <span style="color: #007bff">esercente@paysteam.it</span> Pw: <span style="color: #007bff">esercente2024</span>
                </p>
                <!-- Registration -->
                <p>Non hai un account? <a href="../paysteam/paysteam_registration.php">Registrati</a></p><br><br>
                <input type="hidden" name="indice" value="<?php echo isset($_GET['indice']) ? $_GET['indice'] : ''; ?>">
                <!-- Submit button -->
                <div class="form-group col-12 d-flex justify-content-center">
                    <button type="submit" class="search-btn">Accedi</button>
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