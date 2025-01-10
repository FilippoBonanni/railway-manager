<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linea SFT</title>
    <!-- Bootstrap CSS Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- My CSS file Link -->
    <link rel="stylesheet" href="styles_css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SFT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="home/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section class="hero-section text-white text-center p-5">
        <h1>Benvenuto sulla Linea SFT</h1>
        <p>Scopri i treni storici della nostra linea </p>
    </section>
    <!-- Sections -->
    <div class="content">
        <div class="row">
            <!-- Railway Line -->
            <div class="col-md-4">
                <div class="card">
                    <img src="images/track.png" class="card-img-top" alt="High-speed train">
                    <div class="card-body">
                        <h5 class="card-title">Esplora la nostra Linea</h5>
                        <p class="card-text">Una linea lunga 54 Km tra paesi e stazioni di altri tempi.</p>
                        <a href="home/route.php" class="btn btn-primary">Scopri di più</a>
                    </div>
                </div>
            </div>
            <!-- Trains -->
            <div class="col-md-4">
                <div class="card">
                    <img src="images/train.png" class="card-img-top" alt="Regional train">
                    <div class="card-body">
                        <h5 class="card-title">Scopri la storia dei nostri convogli</h5>
                        <p class="card-text">Comodo e conveniente per viaggi brevi tra le città italiane.</p>
                        <a href="home/convoy_history.php" class="btn btn-primary">Scopri di più</a>
                    </div>
                </div>
            </div>
            <!-- Routes -->
            <div class="col-md-4">
                <div class="card">
                    <img src="images/landscapes.png" class="card-img-top" alt="Scenic train">
                    <div class="card-body">
                        <h5 class="card-title">Visualizza i nostri viaggi</h5>
                        <p class="card-text">Goditi paesaggi mozzafiato lungo itinerari panoramici.</p>
                        <a href="home/itineraries.php" class="btn btn-primary">Scopri di più</a>
                    </div>
                </div>
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