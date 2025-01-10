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
           <a class="navbar-brand" href="#">Registra Carta</a>
           <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
           aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
           </button>
           <div class="collapse navbar-collapse" id="navbarNav">
               <ul class="navbar-nav ms-auto">
                   <li class="nav-item">
                       <a class="nav-link active" href="../paysteam/profile.php">Home</a>
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
           <div class="search-container login-container">
               <h1>Registra Carta</h1>
               <form class="row" action="../scripts/register_card.php" method="POST">
                       <!-- Full Name -->
                       <div class="form-group col-4 offset-2">
                           <label for="Nome e Cognome">Nome e Cognome</label>
                           <input type="text" id="Nome e Cognome" name="nome_cognome" class="form-control" placeholder="Es. Marco Rossi">
                       </div>
                       
                       <!-- Card Number -->
                       <div class="form-group col-4 offset-2">
                           <label for="Numero Carta">Numero Carta</label>
                           <input type="text" id="Numero Carta" name="numero" class="form-control" placeholder="Es. 2345992345110991"
                           maxlength="16" pattern="\d{16}" inputmode="numeric" required>
                       </div>    
                       <!-- Expiration Date -->
                       <div class="form-group col-8 offset-2">
                           <label for="scadenza_carta">Scadenza carta (MM/YY):</label>
                               <div style="display: flex; align-items: center;">
                                   <input type="text" id="mese_scadenza" name="mese_scadenza" maxlength="2" size="2" placeholder="MM" required pattern="\d{2}">
                                   <span>/</span>
                                   <input type="text" id="anno_scadenza" name="anno_scadenza" maxlength="2" size="2" placeholder="YY" required pattern="\d{2}">
                               </div>
                       </div>   
                       <!-- CVV -->
                       <div class="form-group col-4 offset-2">
                           <label for="cvv">CVV</label>
                           <input type="text" id="cvv" name="cvv" maxlength="3" size="3" placeholder="Es. 123">
                       </div>
                       <!-- Submit button -->
                       <div class="form-group col-12">
                           <button type="submit" class="search-btn">Aggiungi</button>
                       </div>
               </form>
           </div>
   </div>

   <!-- Footer -->
   <footer >
       <p>&copy; Società Ferrovie Turistiche - Tutti i diritti riservati</p>
   </footer>

   <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>