<?php
session_start();

include '../scripts/db_connection.php';
include '../scripts/profile_functions.php';

// Get user type from session
$userType = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'Utente';
$profileText = ($userType == 'Esercente') ? 'Profilo Esercente' : 'Profilo Utente';
$userId = $_SESSION['user_id'];

// Get user data
$user = getUserArray($conn, $userId);
// Get all user transactions
$transactions = getTransactionArray($conn, $userId);
// Check card presence
$card = isCardPresent($conn, $userId);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $cardId = findCard($conn, $userId);
    echo $cardId;
    if ($cardId) {
        deleteCard($conn, $cardId);
        $_SESSION['messaggio'] = "Carta Eliminata";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get and remove message from session
$message = isset($_SESSION['messaggio']) ? $_SESSION['messaggio'] : '';
unset($_SESSION['messaggio']);
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PaySteam</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../styles_css/profile.css">
    <link rel="stylesheet" href="../styles_css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?php echo $profileText ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Profilo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../scripts/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main content -->
    <div class="container p-4" style="flex: 1">
        <!-- Check for messages -->
        <?php if ($message): ?>
            <div class="alert alert-success mt-3" role="alert">
                <?php echo $message ?>
            </div>
        <?php endif ?>
        <?php foreach ($user as $data): ?>
            <!-- Name box -->
            <div class="box">
                <h2>Benvenuto, <?php echo $data['Nome'] ?> <?php echo $data['Cognome'] ?></h2>
            </div>

            <!-- Balance box -->
            <div class="box">
                <div class="balance">
                    Saldo attuale: € <?php echo $data['SaldoConto'] ?>
                </div>
            </div>
        <?php endforeach ?>

        <!-- Transactions box -->
        <div class="box transactions">
            <h3>Movimenti recenti</h3>
            <ul>
                <?php foreach ($transactions as $transaction): ?>
                    <li>
                        <span>Pagamento <?php echo $transaction['Tipo'] ?></span>
                        <?php if ($transaction['Tipo'] == 'Effettuato'): ?>
                            <span>€ -<?php echo $transaction['Importo'] ?></span>
                        <?php else: ?>
                            <span>€ +<?php echo $transaction['Importo'] ?></span>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
        </div>

        <!-- Add card box -->
        <?php if ($userType != 'Esercente'): ?>
            <div class="box add-card">
                <?php if ($card == 0): ?>
                    <a href="../paysteam/card_registration.php" class="btn btn-primary">Aggiungi una carta di credito</a>
                    <div id="creditCards" class="credit-cards">
                        <div class="credit-card">
                            <span>Nessuna carta presente</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div id="creditCards" class="credit-cards">
                        <div class="credit-card">
                            <span>****<?php echo substr($card, -4) ?></span>
                        </div>
                        <!-- Delete card button -->
                        <form action="" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $userId ?>">
                            <button type="submit" class="btn btn-danger">Elimina carta</button>
                        </form>
                    </div>
                <?php endif ?>
            </div>
    </div>
    <div id="errorMessage" class="error" style="display: none;">Hai già aggiunto una carta.</div>
    </div>
<?php endif ?>
</div>

<!-- Footer -->
<footer>
    <p>&copy; Società Ferrovie Turistiche - Tutti i diritti riservati</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>