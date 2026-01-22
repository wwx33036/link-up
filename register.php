<?php
// Włączamy pełne raportowanie błędów
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. POŁĄCZENIE
$conn = mysqli_connect('localhost', 'root', '', 'uzytkownicy');

if (!$conn) {
    die("<h3>BŁĄD POŁĄCZENIA: " . mysqli_connect_error() . "</h3>");
}

// 2. ODBIERANIE DANYCH
$imie = $_POST['imie'] ?? 'BRAK';
$nazwisko = $_POST['nazwisko'] ?? 'BRAK';
$plec = $_POST['plec'] ?? '';
$data_urodzenia = $_POST['data_urodzenia'] ?? '1900-01-01';
$nr_telefon = $_POST['telefon'] ?? '000';
$email = $_POST['email'] ?? 'brak@maila.pl';
$haslo = $_POST['password'] ?? 'haslo';

// 3. WYKONANIE ZAPYTANIA
// Używamy małych liter dla tabeli 'uzytkownicy', zgodnie z Twoją bazą w phpMyAdmin
$sql = "INSERT INTO uzytkownicy (imie, nazwisko, plec, data_urodzenia, nr_telefon, email, haslo) 
        VALUES ('$imie', '$nazwisko', '$plec', '$data_urodzenia', '$nr_telefon', '$email', '$haslo')";

// Kluczowy krok: mysqli_query wysyła dane do bazy
if (mysqli_query($conn, $sql)) {
    // Jeśli zapis się udał, przekieruj do login.html
    header("Location: login.html");
    exit(); // Zawsze używaj exit() po przekierowaniu header
} else {
    // Jeśli wystąpił błąd, wyświetl go
    echo "Błąd podczas rejestracji: " . mysqli_error($conn);
}

// 4. ZAMKNIĘCIE POŁĄCZENIA
mysqli_close($conn);
?>