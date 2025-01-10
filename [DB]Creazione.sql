-- Active: 1727962011672@@192.168.0.89@3306@trains

-- ############### Tabella Stazione ##############

CREATE TABLE Stazione (
    Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Nome varchar(50) NOT NULL,
    Km FLOAT NOT NULL,
    Paese varchar(60) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci


INSERT INTO Stazione (Nome, Km, Paese) VALUES
('Torre Spaventa',       0,       'Aosta'                      ),
('Prato Terra',          2.700,   'Susa'                       ),
('Rocca Pietrosa',       7.580,   'Lecco'                      ),
('Villa Pietrosa',       12.680,  'Desenzano del Garda'        ),
('Villa Santa Maria',    16.900,  'Mira'                       ),
('Pietra Santa Maria',   23.950,  'Fiesole'                    ),
('Castro Marino',        31.500,  'Orvieto'                    ),
('Porto Spigola',        39.500,  'Frascati'                   ),
('Porto San Felice',     46.000,  'Monte Porzio Catone'        ),
( 'Villa San Felice',    54.680,  'Caserta'                    );

-- ############### Tabella Materiale Rotabile ##############

CREATE TABLE MaterialeRotabile (
    Id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Tipo enum('Locomotiva', 'Automotrice', 'Carrozza'),
    Nome varchar(50) NOT NULL,
    Serie varchar(10) NOT NULL,
    Posti INT,
    Descrizione VARCHAR(1000),
    Immagine VARCHAR(50),
    Id_Convoglio INT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci

INSERT INTO MaterialeRotabile (Tipo, Nome, Serie, Posti, Immagine) VALUES
('Locomotiva', 'Cavour', 'SFT.3', 0, 'images/trains/SFT.3.jpg'),
('Locomotiva', 'Vittorio Emanuele', 'SFT.4', 0, 'images/trains/SFT.4.jpeg'),
('Locomotiva', 'Garibaldi', 'SFT.6', 0, 'images/trains/SFT.6.jpg'),
('Automotrice', 'Nafta Lorenzo il Magnifico', 'AN 56.2', 56, 'images/trains/Automotrice AN56.2.jpg'),
('Automotrice', 'Nafta Cosimo de Medici', 'AN 56.4', 56, 'images/trains/Automotrice AN56.4.jpg'),
('Carrozza', 'Serie 1928', 'B1', 36, 'images/trains/Carrozza 1928.jpg'),
('Carrozza', 'Serie 1928', 'B2', 36),
('Carrozza', 'Serie 1928', 'B3', 36),
('Carrozza', 'Serie 1930', 'C6', 48, 'images/trains/Carrozza 1930.jpg'),
('Carrozza', 'Serie 1930', 'C9', 48),
('Carrozza', 'Serie 1952', 'C12', 52, 'images/trains/Carrozza 1952.jpg'),
('Carrozza', 'Bagagliaio Serie 1910', 'CD1', 12, 'images/trains/Bagagliai.jpg'),
('Carrozza', 'Bagagliaio Serie 1910', 'CD2', 12);

-- ############### Tabella Convoglio ##############

CREATE TABLE Convoglio (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(50) NOT NULL UNIQUE,
    Posti_Disponibili INT NOT NULL,
    Stato ENUM('Utilizzato', 'Mai Utilizzato')
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Tratta ##############

CREATE TABLE Tratta (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Partenza VARCHAR(50) NOT NULL,
    DataPartenza TIMESTAMP NOT NULL,
    Arrivo VARCHAR(50) NOT NULL,
    DataArrivo TIMESTAMP,
    PostiRimasti INT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Sub Tratta ##############

CREATE TABLE SubTratta (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    StazionePartenza INT,
    DataOraPartenza TIMESTAMP,
    StazioneArrivo INT,
    DataOraArrivo TIMESTAMP,
    Id_Tratta INT,
    Id_Convoglio INT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Utenti ##############

CREATE TABLE Utente (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Nome  VARCHAR(50) NOT NULL,
    Cognome VARCHAR(50) NOT NULL,
    Email VARCHAR(60) NOT NULL UNIQUE,
    Password VARCHAR(60) NOT NULL,
    Tipo ENUM('Acquirente', 'Backoffice Amministrativo', 'Backoffice Esercizio')
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Richiesta ##############
CREATE TABLE Richiesta (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Data TIMESTAMP NOT NULL,
    Mittente  VARCHAR(50) NOT NULL,
    Operazione ENUM('Treni Straordinari', 'Cancellazione Tratta', 'Altro'),
    Messaggio VARCHAR (200) NOT NULL,
    Id_Utente_Amministrativo INT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Utenti PaySteam ##############

CREATE TABLE UtentePaySteam (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Nome  VARCHAR(50) NOT NULL,
    Cognome VARCHAR(50) NOT NULL,
    Email VARCHAR(60) NOT NULL UNIQUE,
    Password VARCHAR(60) NOT NULL,
    SaldoConto FLOAT DEFAULT 0,
    Tipo ENUM('Utente Registrato', 'Esercente'),
    Id_Carta INT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Movimento  ##############
CREATE TABLE Movimento (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Tipo ENUM('Effettuato', 'Ricevuto'),
    Importo FLOAT NOT NULL,
    Id_UtentePaysteam INT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Carta di Credito ##############

CREATE TABLE CartaDiCredito (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    NomeCognome VARCHAR(80) NOT NULL,
    Numero VARCHAR(16) NOT NULL,
    DataScadenza CHAR(5) NOT NULL,
    CVV INT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Tabella Biglietto ##############

CREATE TABLE Biglietto (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Prezzo FLOAT,
    Posti INT,
    CodiceBiglietto VARCHAR(10) NOT NULL,
    Id_Utente INT,
    Id_Tratta INT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci 

-- ############### Vincoli ##############

-- MaterialeRotabile <--> Convoglio
ALTER TABLE MaterialeRotabile
    ADD CONSTRAINT fk_convoglio
    FOREIGN KEY (Id_Convoglio) 
    REFERENCES Convoglio(Id)
    ON DELETE SET NULL;

-- SubTratta <--> Stazione partenza
ALTER TABLE SubTratta
    ADD CONSTRAINT fk_stazione_partenza
    FOREIGN KEY (StazionePartenza)
    REFERENCES Stazione(Id);

-- SubTratta <--> Stazione arrivo
ALTER TABLE SubTratta
    ADD CONSTRAINT fk_stazione_arrivo
    FOREIGN KEY (StazioneArrivo)
    REFERENCES Stazione(Id);

-- SubTratta <--> Convoglio
ALTER TABLE SubTratta
    ADD CONSTRAINT fk_convoglio_subTratta
    FOREIGN KEY (Id_Convoglio) 
    REFERENCES Convoglio(Id)
    ON DELETE CASCADE;

-- SubTratta <--> Tratta
ALTER TABLE SubTratta
    ADD CONSTRAINT fk_tratta
    FOREIGN KEY (Id_Tratta) 
    REFERENCES Tratta(Id)
    ON DELETE CASCADE;

-- UtentePaySteam <--> Carta di Credito
ALTER TABLE UtentePaySteam
    ADD CONSTRAINT fk_Carta
    FOREIGN KEY (Id_Carta) 
    REFERENCES CartaDiCredito(Id)
    ON DELETE SET NULL;

-- Biglietto <--> Tratta
ALTER TABLE Biglietto
    ADD CONSTRAINT fk_BigliettoTratta
    FOREIGN KEY (Id_Tratta) 
    REFERENCES Tratta(Id)
    ON DELETE SET NULL;

-- Biglietto <--> Utente
ALTER TABLE Biglietto
    ADD CONSTRAINT fk_BigliettoUtente
    FOREIGN KEY (Id_Utente) 
    REFERENCES Utente(Id);

-- Richiesta <--> Utente
ALTER TABLE Richiesta
    ADD CONSTRAINT fk_Messaggio
    FOREIGN KEY (Id_Utente_Amministrativo)
    REFERENCES Utente(Id);

-- Movimento <--> UtentePaySteam

ALTER TABLE Movimento 
    ADD CONSTRAINT fk_Movimento
    FOREIGN KEY (Id_UtentePaysteam)
    REFERENCES UtentePaySteam(Id);
