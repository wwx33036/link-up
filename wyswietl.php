<div id="wynik">
<link rel="stylesheet" href="style.css">
<?php
// 1. Połączenie z bazą danych
    $conn = mysqli_connect("localhost", "root", "", "Uzytkownicy");

    if (!$conn) {
        echo "Błąd połączenia z bazą danych.";
    } else {
        // 2. Pobranie postów wraz z danymi autorów
        // Używamy JOIN, aby połączyć ID autora z jego imieniem i nazwiskiem
        $sql = "SELECT 
                    posty.tytul, 
                    posty.tresc, 
                    posty.data_dodania, 
                    uzytkownicy.imie, 
                    uzytkownicy.nazwisko 
                FROM posty 
                JOIN uzytkownicy ON posty.autor_id = uzytkownicy.ID 
                ORDER BY posty.data_dodania DESC";

        $result = mysqli_query($conn, $sql);

        // 3. Wyświetlanie wpisów
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<div class='post' style='border-bottom: 1px solid #ccc; padding: 10px;'>";
                echo "<h3>" . htmlspecialchars($row['tytul']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['tresc'])) . "</p>";
                echo "<small>Autor: <b>" . $row['imie'] . " " . $row['nazwisko'] . "</b> | " . $row['data_dodania'] . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p>Brak postów do wyświetlenia.</p>";
        }
        mysqli_close($conn);
    }
    ?>
    </div>