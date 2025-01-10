<?php session_start() ?>
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
            <a class="navbar-brand" href="#">Login</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Login</a>
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
            <h1>Login</h1>
            <?php
            if (isset($_SESSION['errore_msg'])) {
                echo "<p style='color:red'>" . $_SESSION['errore_msg'] . "</p>";
                unset($_SESSION['errore_msg']);
            }
            ?>
            <form class="row" action="../scripts/login_management.php" method="POST">
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
                    Email: <span style="color: #007bff">acquirente@sft.it</span> Pw: <span style="color: #007bff">ac2024</span>
                </p>
                <p style="font-size: 0.8em;">
                    Email: <span style="color: #007bff">amministrativo@sft.it</span> Pw: <span style="color: #007bff">am2024</span>
                </p>
                <p style="font-size: 0.8em;">
                    Email: <span style="color: #007bff">esercizio@sft.it</span> Pw: <span style="color: #007bff">es2024</span>
                </p>
                <p>Non hai un account? <a href="../home/registration.php">Registrati</a></p><br><br>
                <!-- Submit button -->
                <div class="form-group col-12">
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