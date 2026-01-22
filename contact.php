<?php
// 1. Inicjalizacja sesji i raportowanie błędów
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- SEKCJA WYLOGOWANIA ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 1. Czyszczenie tablicy sesji
    $_SESSION = array();
    
    // 2. Usunięcie ciasteczka sesyjnego z przeglądarki
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // 3. Zniszczenie sesji na serwerze
    session_destroy();
    
    // 4. Resetowanie pliku cookie "ostatnia_wizyta" (ustawienie daty wstecznej)
    setcookie("ostatnia_wizyta", "", time() - 3600, "/");
    
    // 5. Przekierowanie do strony logowania
    header("Location: login.html");
    exit();
}
// --- KONIEC SEKCJI WYLOGOWANIA ---

// 2. Parametry połączenia
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "uzytkownicy"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

// 3. Obsługa pliku COOKIE (informacja o wizycie)
$cookie_name = "ostatnia_wizyta";
$visit_time = date("d-m-Y H:i:s");
$last_visit_info = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : "To Twoja pierwsza wizyta!";
setcookie($cookie_name, $visit_time, time() + (86400 * 1), "/");

// 4. Sprawdzenie sesji użytkownika
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// 5. Pobieranie danych użytkownika
$stmt = $conn->prepare("SELECT imie, nazwisko, plec, data_urodzenia, nr_telefon, email FROM uzytkownicy WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$uzytkownik = $result->fetch_assoc();

if (!$uzytkownik) {
    die("Błąd: Nie znaleziono użytkownika w bazie.");
}

// Obliczanie wieku
$data_ur = new DateTime($uzytkownik['data_urodzenia']);
$teraz = new DateTime();
$wiek = $teraz->diff($data_ur)->y;

// 6. Pobieranie 2 ostatnich postów użytkownika
$sql_posts = "SELECT tytul, tresc, data_dodania FROM posty WHERE autor_id = ? ORDER BY data_dodania DESC LIMIT 2";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $user_id);
$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();

$stmt->close();
$stmt_posts->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Profil użytkownika - <?php echo htmlspecialchars($uzytkownik['imie']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="Logo.png" type="image/png">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: linear-gradient(180deg, #ffffff, #cce4ff); 
            min-height: 100vh; 
            display: flex;
            flex-direction: column;
        }

        .container { 
            max-width: 1000px; 
            width: 100%;
            margin: 0 auto; 
            padding: 40px 20px; 
            position: relative; 
            flex: 1;
        }
        
        /* NAWIGACJA PRZYCISKI */
        .nav-wrapper {
            position: absolute;
            top: 0;
            left: 20px;
            display: flex;
            gap: 15px;
            z-index: 10;
        }

        .nav-btn {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .home-button { background-color: #ffffff; color: #0066cc; }
        .home-button:hover { background-color: #0066cc; color: #ffffff; transform: translateY(-2px); }

        .logout-button { background-color: #ff4d4d; color: white; }
        .logout-button:hover { background-color: #cc0000; transform: translateY(-2px); }

        /* KARTY */
        .profile-card, .box { 
            background: #ffffff; 
            border-radius: 16px; 
            padding: 30px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            margin-bottom: 25px; 
        }

        .profile-card { display: flex; gap: 30px; align-items: center; margin-top: 20px; }
        .profile-photo img { width: 150px; height: 150px; border-radius: 50%; border: 5px solid #0066cc; object-fit: cover; }
        
        .profile-info h2 { margin: 0; color: #0066cc; font-size: 28px; }
        .profile-info p { margin: 8px 0; font-size: 15px; }
        
        .layout { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        
        textarea, input { width: 100%; padding: 14px; border-radius: 10px; border: 1px solid #ddd; margin-top: 12px; }
        
        button { 
            margin-top: 15px; padding: 12px 24px; background: #0066cc; color: white; 
            border: none; border-radius: 10px; cursor: pointer; font-weight: 600; 
        }
        
        .mini-post { border-bottom: 1px solid #f0f0f0; padding: 15px 0; }
        .mini-post h4 { margin: 0; color: #0066cc; font-size: 15px; }
        .mini-post p { margin: 6px 0; font-size: 14px; color: #666; }
        
        .cookie-status { font-size: 12px; color: #888; margin-top: 20px; font-style: italic; border-top: 1px solid #eee; padding-top: 15px; }

        .site-footer { 
            background: #fff; 
            padding: 40px 0; 
            border-top: 1px solid #e0e0e0; 
            text-align: center;
        }
        .footer-links { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        .footer-links a { text-decoration: none; color: #666; font-size: 13px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-wrapper">
        <a href="main.php" class="nav-btn home-button">🏠 Strona Główna</a>
        <a href="?action=logout" class="nav-btn logout-button">🚪 Wyloguj się</a>
    </div>

    <div class="profile-card">
        <div class="profile-photo">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($uzytkownik['imie'].'+'.$uzytkownik['nazwisko']); ?>&background=0066cc&color=fff&size=150" alt="Avatar">
        </div>
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($uzytkownik['imie'] . " " . $uzytkownik['nazwisko']); ?></h2>
            <p><strong>Wiek:</strong> <?php echo $wiek; ?> lat</p>
            <p><strong>Płeć:</strong> <?php echo htmlspecialchars($uzytkownik['plec']); ?></p>
            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($uzytkownik['nr_telefon']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($uzytkownik['email']); ?></p>
            <div class="cookie-status">Ostatnia wizyta: <?php echo htmlspecialchars($last_visit_info); ?></div>
        </div>
    </div>

    <div class="layout">
        <div class="box">
            <h3>Dodaj post</h3>
            <form action="postconcact.php" method="POST">
                <input type="text" name="tytul" placeholder="Tytuł posta" required>
                <textarea name="tresc" placeholder="Co słychać, <?php echo htmlspecialchars($uzytkownik['imie']); ?>?" required></textarea>
                <button type="submit">Opublikuj post</button>
            </form>
        </div>

        <div class="box">
            <h3>Twoje ostatnie wpisy</h3>
            <?php if ($result_posts->num_rows > 0): ?>
                <?php while($post = $result_posts->fetch_assoc()): ?>
                    <div class="mini-post">
                        <h4><?php echo htmlspecialchars($post['tytul']); ?></h4>
                        <p><?php echo mb_strimwidth(htmlspecialchars($post['tresc']), 0, 80, "..."); ?></p>
                        <small><?php echo $post['data_dodania']; ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="font-size: 13px; color: #999;">Brak postów.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

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