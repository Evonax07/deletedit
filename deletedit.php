<?php
class Database {
    private $host = "localhost:3307";
    private $gebruikersnaam = "root";
    private $wachtwoord = "";
    private $database = "deletedit";

    private $verbinding;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database}";
            $this->verbinding = new PDO($dsn, $this->gebruikersnaam, $this->wachtwoord);
            $this->verbinding->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Verbindingsfout: " . $e->getMessage());
        }
    }

    public function updateData($id, $nieuweData) {
        try {
            $query = "UPDATE jouw_tabel SET kolom1 = :kolom1, kolom2 = :kolom2 WHERE id = :id";
            $stmt = $this->verbinding->prepare($query);
            $stmt->bindParam(':kolom1', $nieuweData['kolom1']);
            $stmt->bindParam(':kolom2', $nieuweData['kolom2']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "<script>openPopup('Succesvol bijgewerkt!');</script>";
            } else {
                echo "<script>openPopup('Update mislukt. Probeer opnieuw.');</script>";
            }
        } catch (PDOException $e) {
            die("Update-fout: " . $e->getMessage());
        }
    }

    public function deleteData($id) {
        try {
            $query = "DELETE FROM jouw_tabel WHERE id = :id";
            $stmt = $this->verbinding->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "<script>openPopup('Succesvol verwijderd!');</script>";
            } else {
                echo "<script>openPopup('Verwijderen mislukt. Probeer opnieuw.');</script>";
            }
        } catch (PDOException $e) {
            die("Verwijder-fout: " . $e->getMessage());
        }
    }

    public function sluitVerbinding() {
        $this->verbinding = null;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $database = new Database();

    $id = $_POST['id'];
    $nieuweData = [
        'kolom1' => $_POST['kolom1'],
        'kolom2' => $_POST['kolom2']
    ];

    $database->updateData($id, $nieuweData);

    $database->sluitVerbinding();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $database = new Database();

    $id = $_POST['id'];

    $database->deleteData($id);

    $database->sluitVerbinding();
}
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update en Delete</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            max-width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); 
            justify-content: center;
            align-items: center;
            z-index: 1000; 
        }

        .popup {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .close-btn {
            cursor: pointer;
        }
        body.overlay-active {
            overflow: hidden; 
        }
    </style>
</head>
<body>

    <div class="overlay" id="popup-overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup()">X</span>
            <p id="popup-message"></p>
        </div>
    </div>

    <form method="post" action="">
        <label for="id">ID:</label>
        <input type="text" name="id" required>
        <label for="kolom1">Kolom 1:</label>
        <input type="text" name="kolom1" required>
        <label for="kolom2">Kolom 2:</label>
        <input type="text" name="kolom2" required>
        <button type="submit" name="update">Update</button>
    </form>

    <form method="post" action="">
        <label for="id_delete">ID om te verwijderen:</label>
        <input type="text" name="id" required>
        <button type="submit" name="delete">Delete</button>
    </form>

    <script>
        function openPopup(message) {
            document.getElementById('popup-message').innerHTML = message;
            document.getElementById('popup-overlay').style.display = 'flex';
            document.body.classList.add('overlay-active');
        }

        function closePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
            document.body.classList.remove('overlay-active');
        }
    </script>
</body>
</html>