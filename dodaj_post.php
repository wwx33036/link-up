<?php
// 1. Inicjalizacja sesji i błędów
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. Połączenie z bazą
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "Uzytkownicy";

$conn = mysqli_connect($host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

// 3. Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    die("Błąd: Twoja sesja wygasła. Zaloguj się ponownie.");
}

// 4. Obsługa wysyłania formularza
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Odbieramy dane (używamy nazw z formularza HTML)
    $tytul = isset($_POST['tytul']) ? trim($_POST['tytul']) : 'Post bez tytułu';
    $tresc = isset($_POST['tresc']) ? trim($_POST['tresc']) : '';
    $autor_id = $_SESSION['user_id']; // ID pobrane z Twojej tabeli uzytkownicy podczas logowania

    if (!empty($tresc)) {
        // 5. Zapytanie SQL - dodajemy 3 wartości: tytul, tresc, autor_id
        $sql = "INSERT INTO posty (tytul, tresc, autor_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // "ssi" oznacza: string (tytul), string (tresc), integer (autor_id)
        $stmt->bind_param("ssi", $tytul, $tresc, $autor_id);

        if ($stmt->execute()) {
            echo "<h2>Sukces!</h2>";
            echo "Post został dodany przez: " . $_SESSION['user_full_name'];
            header("Refresh: 2; URL=main.html");
        } else {
            echo "Błąd bazy danych: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Treść posta nie może być pusta!";
    }
}
$conn->close();
?>