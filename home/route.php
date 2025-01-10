<?php
include '../scripts/db_connection.php';
// Query to get all stations
$sql = "SELECT * FROM Stazione";
$result = $conn->query($sql);

// Store results in array
$stations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stations[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convogli</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/style_route.css">
    <link rel="stylesheet" href="../styles_css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Linea</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item"></li>
                    <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section class="hero-section text-white text-center p-5">
        <h1>La nostra linea lunga 54 Km</h1>
        <p>Scopri tutti i paesi che attraverserete con SFT </p>
    </section>
    <!-- Main Section -->
    <div class="timeline">
        <?php foreach ($stations as $station): ?>
            <div class="station">
                <div class="circle"></div>
                <div class="description">
                    <h2>Stazione <?= htmlspecialchars($station['Nome']) ?></h2>
                    <p>Paese: <?= htmlspecialchars($station['Paese']) ?></p>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <!-- Footer -->
    <footer>
        <p>&copy; Società Ferrovie Turistiche - Tutti i diritti riservati</p>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>