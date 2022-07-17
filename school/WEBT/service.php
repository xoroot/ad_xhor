<?php
session_start();
if((int)$_SESSION['punkte'] > 5) {
    $_SESSION['punkte'] = 0;
}

$frage_1 = ['Wieviele Richis braucht es um einen Computer zu reparieren?', '42', '1337', 'es gibt nur einen...'];
$frage_2 = ['Wie reinigt man eine Festplatte am besten?', 'In den Rhein werfen', 'Seife und Schwamm', 'Wischmobb'];
$frage_3 = ['Welches Werkzeug eignet sich am Besten zum Brennen einer CD?', 'Feuerzeug und Aceton', 'Lötkolben', 'Flammenwerfer'];
$frage_4 = ['Wie installiert man Windows?', 'Mit Hilfe des Swisscom-Supports', 'Mit reichlich Holz und Glas', 'Mit Schraubenzieher und Nagel'];
$frage_5 = ['Ich wurde gehackt, was soll ich tun???', 'Swisscom-Support anrufen', 'Computer in den Rhein werfen', 'Nicht gehackt werden'];

if(isset($_POST['startval']) && $_POST['startval'] == 'start') {
    if(isset($_COOKIE['frage_nr']) && $_COOKIE['frage_nr'] > 6) {
        $frage_nr = 7;
    } elseif(!isset($_COOKIE['frage_nr']) || $_COOKIE['frage_nr'] < 1) {
        $_SESSION['punkte'] = 0;
        setcookie('frage_nr', 1, time() + (5 * 60));
        $frage_nr = 1;
    } else {
        $frage_nr = $_COOKIE['frage_nr'];
    }
} else {
    $frage_nr = $_COOKIE['frage_nr'];
}

if(isset($_POST['check_benutzer'])) {
    $conn = mysqli_connect('localhost', 'root', '', 'client_db');
    $stmt = mysqli_prepare($conn, 'SELECT c_req_timestmp, c_name, c_punkte FROM clients WHERE c_zustimmung = 1 ORDER BY c_punkte DESC');

    mysqli_stmt_execute($stmt);
    $daten = mysqli_stmt_get_result($stmt);

    if ($daten) {
        while($row = mysqli_fetch_assoc($daten)) {
            $benutzernamen .= $row['c_name'];
        }
    }
    mysqli_close($conn);

    echo $benutzernamen;

} elseif (isset($frage_nr)) {

    $resp_code = '';

    checkQuestion($frage_nr-1);
    
    switch($frage_nr) {
        case 1:
            setcookie('frage_nr', 2, time() + (5 * 60));
            $resp_code = getRespString($frage_1);
            break;
        case 2:
            $resp_code = getRespString($frage_2);
            setcookie('frage_nr', 3, time() + (5 * 60));
            break;
        case 3:
            $resp_code = getRespString($frage_3);
            setcookie('frage_nr', 4, time() + (5 * 60));
            break;
        case 4:
            $resp_code = getRespString($frage_4);
            setcookie('frage_nr', 5, time() + (5 * 60));
            break;
        case 5:
            $resp_code = getRespString($frage_5);
            setcookie('frage_nr', 6, time() + (5 * 60));
            break;
        case 6:
            if(isset($_POST['benutzername']) && strlen($_POST['benutzername']) > 2) {
                setcookie('frage_nr', 7, time() + (48 * 60 * 60));
                saveBenutzerdaten();
                $resp_code = getBenutzer();
            } else {
                $resp_code =  '<form id="final_form" action="service.php" method="post" onsubmit="return checkBenutzername()">';
                $resp_code .= '<div id="form-container" class="w3-container w3-center w3-cell-row">';

                $resp_code .= '<input id="benutzername" type="text" name="benutzername" placeholder="Nickname" class="input_text quiz-antwort w3-cell w3-mobile" required>';
                $resp_code .= '<input id="email" type="email" name="email" placeholder="E-Mail" class="input_text quiz-antwort w3-cell w3-mobile" required>';

                $resp_code .= '<input id="zustimmung" type="checkbox" name="zustimmung">';
                $resp_code .= '<label id="zusttimmungs-label" for="zustimmung" class="input_text quiz-antwort w3-cell w3-mobile">Resultate veröffentlichen</label>';
                $resp_code .= '</div>';

                $resp_code .= '<button type="submit" id="antwort-button">Absenden</button>';


                $resp_code .=  '</form>';

            }
            break;
        case 7:
            $resp_code = getBenutzer();
            break;
        default:
            echo 'Error';
            break;
    }

    echo $resp_code;
}  else {
    echo 'Error';
}

function checkQuestion($nr) {
    switch($nr) {
        case 1:
            if($_POST['antwort'] == '3') {
                $_SESSION['punkte'] = ((int) $_SESSION['punkte']) + 1;
            }
            break;
        case 2:
            if($_POST['antwort'] == '2') {
                $_SESSION['punkte'] = ((int) $_SESSION['punkte']) + 1;
            }
            break;
        case 3:
            if($_POST['antwort'] == '3') {
                $_SESSION['punkte'] = ((int) $_SESSION['punkte']) + 1;
            }
            break;
        case 4:
            if($_POST['antwort'] == '1') {
                $_SESSION['punkte']++;
            }
            break;
        case 5:
            if($_POST['antwort'] == '2') {
                $_SESSION['punkte']++;
            }
            break;
        default:
            $_SESSION['punkte'] += $_POST['antwort'];
            break;
    }
}

function getRespString($respArray) {
    $respString = '';

    $respString .= '<h1>'.$respArray[0].'</h1>';
    $respString .= '<div id="quiz-container" class="w3-container w3-center w3-cell-row">';

    for($i = 1; $i < 4; $i++) {
        $respString .= '<input id="antwort_'.$i.'" type="radio" value="'.$i.'" name="antwort">';
        $respString .= '<label for="antwort_'.$i.'" class="quiz-antwort w3-cell w3-mobile">'.$respArray[$i].'</label>';
    }

    $respString .= '</div>';

    $respString .= '<button id="antwort-button" type="button" onclick="sendeAntwort()">Antworten</button>';

    if((int)$_SESSION['punkte'] > 5) {
        $_SESSION['punkte'] = 0;
    }

    $respString .= '<p>Anzahl Punkte: <b>'.$_SESSION['punkte'].'</b>';

    return $respString;
}

function saveBenutzerdaten() {
    if(!isset($_POST['benutzername']) || !isset($_POST['email'])) {
        return 'Bitte nochmals versuchen!';     
    }

    $benutzername = $_POST['benutzername'];
    $mail = $_POST['email'];

    if(isset($_POST['zustimmung'])) {
        $zustimmung = 1;
    } else {
        $zustimmung = 0;
    }

    db_insert_benutzer($benutzername, $mail, $zustimmung);
}

function getBenutzer() {
    if(isset($_POST['benutzername'])) {
        $respString = '<!DOCTYPE html>
            <html lang="de">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <title>ääliö Computer-Service</title>
            <link rel="stylesheet" href="css/style.css">
            <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
            </head>';
        $respString .= '<h1 id="php-titel"> Vielen Dank für das Abschliessen des Quizzes! </h1>';
        $respString .= '<p class="w3-center"><a href="index.html#php-form" id="php-link">Zurück zur Hauptseite</a></p>';
        $respString .= '</html>';
    } else {
        $respString = '<h1> Vielen Dank für das Abschliessen des Quizzes! </h1>';
        $respString .= db_get_benutzer();
    }


    return $respString;
}

function db_insert_benutzer($uname, $mail, $zustimmung) {
    $punkte = $_SESSION['punkte'];
    $_SESSION['punkte'] = 0;

    $conn = mysqli_connect('localhost', 'root', '', 'client_db');
    $stmt = mysqli_prepare($conn, "INSERT INTO `clients` (`c_req_id`, `c_name`, `c_mail`, `c_zustimmung`, `c_punkte`, `c_req_timestmp`) VALUES (NULL, '".$uname."', '".$mail."', '".$zustimmung."', '".$punkte."', current_timestamp())");

    mysqli_stmt_execute($stmt);
    
    mysqli_close($conn);
}

function db_get_benutzer() {
    $conn = mysqli_connect('localhost', 'root', '', 'client_db');
    $stmt = mysqli_prepare($conn, 'SELECT c_req_timestmp, c_name, c_punkte FROM clients WHERE c_zustimmung = 1 ORDER BY c_punkte DESC');

    mysqli_stmt_execute($stmt);
    $daten = mysqli_stmt_get_result($stmt);

    $daten_str = '<h2 class="w3-center">Rangliste</h2>';
    $daten_str .= '<table id="resultate" class="w3-table w3-striped">';
    $daten_str .= '<thead><tr><th>Datum</th><th>Nickname</th><th>Punkte</th></tr></thead>';
    $daten_str .= '<tbody>';

    if ($daten) {
        while($row = mysqli_fetch_assoc($daten)) {
            $daten_str .= '<tr><th>'. $row['c_req_timestmp'] .'</th><th>'. $row['c_name']. '</th><th>' . $row['c_punkte'] . '</th></tr>';
        }
    }
    $daten_str .= '</tbody>';
    $daten_str .= '</table>';

    mysqli_close($conn);
    return $daten_str;
}
?>