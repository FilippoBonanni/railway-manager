<?php
include '../scripts/db_connection.php';

// Query to get all rolling stock
$sql = "SELECT * FROM MaterialeRotabile";
$result = $conn->query($sql);

// Store results in array
$trains = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $trains[] = $row;
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
    <link rel="stylesheet" href="../styles_css/style.css">
    <link rel="stylesheet" href="../styles_css/trains.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Convogli</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item"></li>
                    <a class="nav-link active" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section class="hero-section text-white text-center p-5">
        <h1>Tutti i nostri convogli</h1>
        <p>Fai un salto nel passato con le nostre locomotive e carrozze storiche </p>
    </section>
    <!-- Main Section -->
    <div class="timeline">
        <?php $displayedIndex = 0; // Alternative counter 
        ?>
        <?php foreach ($trains as $train): ?>
            <?php if ($train['Serie'] != 'B2' && $train['Serie'] != 'B3' && $train['Serie'] != 'C9' && $train['Serie'] != 'CD2'): ?>
                <div class='train d-flex <?php echo $displayedIndex % 2 === 0 ? 'flex-row' : 'flex-row-reverse' ?>'>
                    <div class='description'>
                        <h2><?php echo $train['Tipo'] . ": " . $train['Nome'] ?></h2>
                        <p><?php echo $train['Descrizione'] ?></p>
                    </div>
                    <div class='image'>
                        <img src="<?php echo "../" . $train['Immagine'] ?>" alt="<?php echo $train['Nome'] ?>" style='width:350px'>
                    </div>
                </div>
                <?php $displayedIndex++; // Increment only for displayed trains 
                ?>
            <?php endif ?>
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