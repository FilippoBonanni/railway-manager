<?php
include '../scripts/db_connection.php';
// Query to get all stations
$sql = "SELECT* FROM Stazione";
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
    <title>Ricerca Biglietto Treno</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Profilo Utente</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main content -->
    <div class="content">
        <div class="container d-flex justify-content-center align-items-center">
            <div class="search-container">
                <h1>Cerca il tuo biglietto</h1>
                <form action="book_trip.php" method="POST">
                    <div class="form-row">
                        <!-- Departure station -->
                        <div class="form-group col-5">
                            <label for="partenza">Stazione di Partenza</label>
                            <!-- Dropdown menu -->
                            <select id="partenza" name="partenza" onchange="updateArrivalOptions()">
                                <option value="" disabled selected>Seleziona stazione</option>
                                <?php foreach ($stations as $station): ?>
                                    <option value="<?php echo $station['Nome'] ?>"><?php echo $station['Nome'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <!-- Arrival station -->
                        <div class="form-group col-5">
                            <label for="arrivo">Stazione di Arrivo</label>
                            <select id="arrivo" name="arrivo">
                                <option value="" disabled selected>Seleziona stazione</option>
                                <?php foreach ($stations as $station): ?>
                                    <option value="<?php echo $station['Nome'] ?>"><?php echo $station['Nome'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <!-- Departure date -->
                        <div class="form-group col-2">
                            <label for="dataPartenza">Data di partenza</label>
                            <input type="date" id="dataPartenza" name="dataPartenza" 
                                min="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <!-- Departure time -->
                        <div class="form-group col-2">
                            <label for="orarioPartenza">Orario di partenza</label>
                            <input type="time" id="orarioPartenza" name="orarioPartenza">
                        </div>
                        <!-- Number of passengers -->
                        <div class="form-group col-3">
                            <label for="passeggeri">Passeggeri</label>
                            <select id="passeggeri" name="passeggeri">
                                <?php for ($i = 1; $i < 10; $i++): ?>
                                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php endfor ?>
                            </select>
                        </div>

                        <!-- Search button -->
                        <div class="form-group col-4 offset-4">
                            <button type="submit" class="btn search-btn">Cerca Biglietti</button>
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
    <!-- Hide selected departure station from arrival options -->
    <script>
        function updateArrivalOptions() {
            const departureSelect = document.getElementById('partenza');
            const arrivalSelect = document.getElementById('arrivo');
            const selectedStation = departureSelect.value;

            // Store current arrival station selection
            const currentArrivalValue = arrivalSelect.value;

            // Remove all options except first (placeholder)
            while (arrivalSelect.options.length > 1) {
                arrivalSelect.remove(1);
            }

            // Get all options from departure station
            Array.from(departureSelect.options).forEach(option => {
                if (option.value && option.value !== selectedStation) {
                    const newOption = new Option(option.text, option.value);
                    arrivalSelect.add(newOption);
                }
            });

            // Restore previously selected arrival station if still valid
            if (currentArrivalValue && currentArrivalValue !== selectedStation) {
                arrivalSelect.value = currentArrivalValue;
            } else {
                arrivalSelect.selectedIndex = 0;
            }
        }

        // Run function on load to handle preselected values
        window.onload = updateArrivalOptions;
    </script>
</body>
</html>