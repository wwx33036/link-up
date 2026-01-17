<?php
// 1. POŁĄCZENIE Z BAZĄ
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "Uzytkownicy"; // Upewnij się, że nazwa bazy w XAMPP jest identyczna

$conn = mysqli_connect($host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

// 2. LOGIKA LOGOWANIA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Zmienione na 'password', bo tak wysyła Twój formularz (widoczne na screenie)
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $haslo = isset($_POST['password']) ? $_POST['password'] : ''; 

    if (!empty($email) && !empty($haslo)) {
        // Używamy bazy do sprawdzenia użytkownika
        $sql = "SELECT ID, imie, nazwisko FROM uzytkownicy WHERE email = ? AND haslo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $haslo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // SESJA - To jest klucz do automatycznych podpisów postów
            $_SESSION['user_id'] = $user['ID']; // Zapisujemy ID z Twojej tabeli
            $_SESSION['user_full_name'] = $user['imie'] . " " . $user['nazwisko'];
            
            header("Location: main.html");
            exit();
        } else {
            echo "Błędny email lub hasło.";
        }
    } else {
        echo "Proszę wypełnić wszystkie pola.";
    }
}
?>