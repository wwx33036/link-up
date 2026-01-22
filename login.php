<?php
// 1. START SESJI - TO MUSI BYĆ PIERWSZA LINIA KODU PHP
session_start(); 

// 2. POŁĄCZENIE Z BAZĄ
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "Uzytkownicy"; 

$conn = mysqli_connect($host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

// 3. LOGIKA LOGOWANIA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $haslo = isset($_POST['password']) ? $_POST['password'] : ''; 

    if (!empty($email) && !empty($haslo)) {
        $sql = "SELECT ID, imie, nazwisko FROM uzytkownicy WHERE email = ? AND haslo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $haslo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Zapisujemy ID do sesji - teraz zostanie zapamiętane
            $_SESSION['user_id'] = $user['ID']; 
            $_SESSION['user_full_name'] = $user['imie'] . " " . $user['nazwisko'];
            
            header("Location: main.php");
            exit();
        } else {
            echo "Błędny email lub hasło.";
        }
    } else {
        echo "Proszę wypełnić wszystkie pola.";
    }
}
?>