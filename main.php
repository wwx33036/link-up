<?php
// Rozpoczęcie sesji, aby móc korzystać z danych użytkownika
session_start();
// Przykład pobrania danych użytkownika z sesji (musisz mieć to ustawione przy logowaniu)
$uzytkownik_imie = isset($_SESSION['user_imie']) ? $_SESSION['user_imie'] : "Kacper"; 
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkUp</title>
    <link rel="icon" href="Logo.png" type="image/png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #3476e1;
            --primary-hover: #2759ab;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #cce4ff 100%);
            --card-bg: #ffffff;
            --text-main: #2d3436;
            --text-muted: #636e72;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 10px 25px rgba(0,0,0,0.08);
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* KLUCZOWA ZMIANA: Układ Body */
        body {
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh; /* Zajmuje całą wysokość ekranu */
            display: flex;
            flex-direction: column; /* Układa elementy pionowo: Header -> Main -> Footer */
            -webkit-font-smoothing: antialiased;
        }

        /* --- NAGŁÓWEK --- */
        header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 10%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        #G3 a {
            text-decoration: none;
            color: var(--primary-color);
            background: #f0f7ff;
            padding: 8px 20px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        #G3 a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        /* KLUCZOWA ZMIANA: Kontener Main */
        main {
            max-width: 850px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1; /* Sprawia, że Main "wypycha" stopkę do dołu, zajmując całą wolną przestrzeń */
            width: 100%;
        }

        .layout {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .box {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .box h3 {
            margin-bottom: 25px;
            font-size: 1.4rem;
            color: var(--text-main);
            font-weight: 700;
        }

        /* --- FORMULARZ --- */
        form input[type="text"], 
        form textarea {
            width: 100%;
            border-radius: 10px;
            border: 2px solid #edf2f7;
            padding: 14px;
            font-size: 1rem;
            margin-bottom: 20px;
            background: #f8fafc;
        }

        form button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        form button:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        /* --- LISTA POSTÓW --- */
        #m3 {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .post {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 35px;
            box-shadow: var(--shadow-md);
        }

        .post h3 { color: var(--primary-color); margin-bottom: 12px; }
        .post p { line-height: 1.8; color: #4a5568; margin-bottom: 20px; }
        .post small { display: block; color: var(--text-muted); border-top: 1px solid #f1f5f9; padding-top: 15px; }

        /* --- STOPKA: Zawsze na dole --- */
        .site-footer {
            background-color: white;
            padding: 40px 0;
            margin-top: auto; /* Dodatkowe zabezpieczenie dla flexboxa */
            border-top: 1px solid #eee;
            width: 100%;
        }

        .footer-container {
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .footer-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .footer-links a:hover { color: var(--primary-color); }
    </style>
</head>
<body>
    <header>
        <div id="G1">LinkUp</div>
        <div id="G3">
            <a href="Contact.php"><h2>Profil</h2></a>
        </div>
    </header>

    <main>
        <div class="layout">
            <div class="box">
                <h3>Dodaj post</h3>
                <form action="dodaj_post.php" method="POST">
                    <input type="text" name="tytul" placeholder="Tytuł posta" required>
                    <textarea name="tresc" placeholder="Co słychać?" required></textarea>
                    <button type="submit">Opublikuj post</button>
                </form>
            </div>

            <div id="m3">
                <?php
                $conn = mysqli_connect("localhost", "root", "", "uzytkownicy");
                if (!$conn) {
                    echo "Błąd połączenia.";
                } else {
                    $sql = "SELECT posty.tytul, posty.tresc, posty.data_dodania, uzytkownicy.imie, uzytkownicy.nazwisko 
                            FROM posty 
                            JOIN uzytkownicy ON posty.autor_id = uzytkownicy.ID 
                            ORDER BY posty.data_dodania DESC";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='post'>";
                            echo "<h3>" . htmlspecialchars($row['tytul']) . "</h3>";
                            echo "<p>" . nl2br(htmlspecialchars($row['tresc'])) . "</p>";
                            echo "<small>Autor: <b>" . $row['imie'] . " " . $row['nazwisko'] . "</b> | " . $row['data_dodania'] . "</small>";
                            echo "</div>";
                        }
                    } else { echo "<p>Brak postów.</p>"; }
                    mysqli_close($conn);
                }
                ?>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>© 2025 Linkup</p>
            <nav class="footer-links">
                <a href="informacje.html">Informacje</a>
                <a href="pomoc.html">Pomoc</a>
                <a href="regulamin.html">Regulamin</a>
                <a href="o-nas.html">O nas</a>
                <a href="prywatnosc.html">Prywatność</a>
                <a href="bezpieczenstwo.html">Bezpieczeństwo</a>
                <a href="kontakt.html">Kontakt</a>
                <a href="faq.html">FAQ</a>
                <a href="zglos-problem.html">Zgłoś problem</a>
            </nav>
        </div>
    </footer>
</body>
</html>