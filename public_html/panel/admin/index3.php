<!DOCTYPE html>
    <html lang = 'pl-PL'>
        <head>
            <title>Panel administracyjny</title>
            <meta charset = "UTF-8" />
            <script>
                function fd() // form 
                {
                    let v2 = document.getElementById("2t").value;
                    let v5 = document.getElementById("5t").value;

                    if(v2 !== "None")
                    {
                        document.getElementById("5t").disabled = true;
                    }else if(v5 !== "None")
                    {
                        document.getElementById("2t").disabled = true;
                    }
                    
                    if(v2 == "None")
                    {
                        document.getElementById("5t").disabled = false;
                    }
                    
                    if(v5 == "None")
                    {
                        document.getElementById("2t").disabled = false;
                    }
                }
            </script>
            <style>
                form(
                    float: left;
                )
            </style>
        </head>
        <body>
            <?php
                session_start();

                function main($dbr, $dbw)
                {
                    if(!$_SESSION['login'] == 1)
                    {
                        header("Location: ./../");
                    }

                    $ret = "<nav>
                    <form method = 'GET'>
                        <input type = 'hidden' name = 'type' value = '2v2' />
                        <input type = 'submit' value = '2 na 2' />
                    </form>
                    
                    <form method = 'GET'>
                        <input type = 'hidden' name = 'type' value = '5v5' />
                        <input type = 'submit' value = '5 na 5' />
                    </form>

                    <form method = 'GET'>
                        <input type = 'hidden' name = 'type' value = 'add' />
                        <input type = 'submit' value = 'Dodaj drużynę' />
                    </form>

                    <form method = 'GET' action = './'>
                        <input type = 'hidden' name = 'type' value = 'players' />
                        <input type = 'submit' value = 'Dodaj gracza' />
                    </form>

                    <form method = 'GET'>
                        <input type = 'hidden' name = 'type' value = 'ep' />
                        <input type = 'submit' value = 'Zarządzanie graczami' />
                    </form>

                    <form method = 'GET'>
                        <input type = 'hidden' name = 'type' value = 'm' />
                        <input type = 'submit' value = 'Zarządzanie meczami' />
                    </form>
                    
                    <form method = 'POST' action = './../logout.php'>
                        <input type = 'submit' value = 'Wyloguj' />
                    </form>
                    </nav>
                    <hr />";

                    if(isset($_GET['type']))
                    {

                        if($_GET['type'] == "2v2")
                        {
                            $ret = $ret . teams(2, $dbr);
                        }else if($_GET['type'] == "5v5")
                        {
                            $ret = $ret . teams(5, $dbr);
                        }else if($_GET['type'] == "add")
                        {
                            $ret = $ret . add();
                        }else if($_GET['type'] == "players")
                        {
                            $ret = $ret . players($dbw);
                        }else if($_GET['type'] == 'pa')
                        {
                            $ret = $ret . pa($dbw);
                        }else if($_GET['type'] == "ep")
                        {
                            $ret = $ret . ep($dbr);
                        }else if($_GET['type'] == "epe")
                        {
                            $ret = $ret . epe($dbw) . "
                            <script>
                                window.onload = function() {
                                    fd();
                                }
                            </script>";
                        }else if($_GET['type'] == 'epq')
                        {
                            $ret = $ret . epq($dbw);
                        }else if($_GET['type'] == 'epd')
                        {
                            $ret = $ret . epd($dbw);
                        }else if($_GET['type'] == 'm')
                        {
                            $ret = $ret . m($dbr);
                        }else if($_GET['type'] == 'me')
                        {
                            $ret = $ret . me($dbr);
                        }else if($_GET['type'] == 'ma')
                        {
                            $ret = $ret . ma($dbr, $_POST['type']);
                        }
                    }

                    if(isset($_POST['add']))
                    {
                        if($_POST['add'] == "0")
                        {
                            $ret = $ret . a($dbw);
                        }
                    }

                    if(isset($_GET['edit']))
                    {
                        $ret = $ret . edit($_GET['group'], $_GET['edit'], $dbw);
                    }else if(isset($_GET['del']))
                    {
                        $ret = $ret . del($_GET['group'], $_GET['del'], $dbw);
                    }

                    if(isset($_POST['e']))
                    {
                        $ret = $ret . e($dbw);
                    }

                    return $ret;
                }

                    function teams($type, $db) // showing teams
                    {

                        if($type == 2)
                        {
                            $table = '2v2teamy';
                            $group = "2";
                        }else if($type == 5)
                        {
                            $table = '5v5teamy';
                            $group = "5";
                        }

                            $res = mysqli_query($db, "SELECT id, nazwa FROM $table");

                            $ret = "<table>
                            <tr>
                                <th>Nazwa</th>
                                <th>Edycja</th>
                                <th>Usuwanie</th>
                            </tr>";

                            while($tab = mysqli_fetch_row($res))
                            {
                                $ret = "$ret <tr>
                                    <td>$tab[1]</td>
                                    <td>
                                        <form method = 'GET'>
                                            <input type = 'hidden' name = 'edit' value = '$tab[0]' />
                                            <input type = 'hidden' name = 'group' value = '$group' />
                                            <input type = 'submit' value = 'Edytuj' />
                                        </form>
                                    </td>
                                    <td>
                                        <form method = 'GET' onSubmit = 'return confirm(" . '"Czy na pewno chcesz usunąć tę drużynę? Tej operacji nie da się cofnąć"' . ");'>
                                            <input type = 'hidden' name = 'del' value = '$tab[0]' />
                                            <input type = 'hidden' name = 'group' value = '$group' />
                                            <input type = 'submit' value = 'Usuń' />
                                        </form>
                                    </td>
                                </tr>";
                            }

                            return $ret;

                    }

                function del($group, $id, $db) // deleting teams
                {

                    if($group == "2")
                    {
                        $group = "2v2teamy";
                    }else if($group == '5')
                    {
                        $group = "5v5teamy";
                    }

                    $res = mysqli_query($db, "SELECT nazwa FROM $group WHERE id = $id");
                    while($tab = mysqli_fetch_row($res))
                    {
                        $ret = $tab[0];
                    }

                    if(!mysqli_query($db, "DELETE FROM $group WHERE id = $id"))
                    {
                        return "Nie udało się usunąć drużyny $ret";
                    }

                    return "Pomyślnie usunięto $ret";
                }

                function edit($group, $id, $db) // editing teams
                {

                    if($group == "2")
                    {
                        $group = "2v2teamy";
                    }else if($group == '5')
                    {
                        $group = "5v5teamy";
                    }

                    $res = mysqli_query($db, "SELECT id, nazwa, wygrane, przegrane FROM $group WHERE id = $id");

                    $ret = "<table>
                    <tr>
                        <th>Nazwa</th>
                        <th>Wygrane</th>
                        <th>Przegrane</th>
                    </tr>
                    <form method = 'POST' action = './'>";

                    while($tab = mysqli_fetch_row($res))
                    {
                        $ret = "$ret
                        <tr>
                            <td>
                                <input type = 'hidden' name = 'e' value = '$group' />
                                <input type = 'hidden' name = 'id' value = '$tab[0]' />
                                <input type = 'text' name = 'name' value = '$tab[1]' />
                            </td>
                            <td>
                                <input type = 'number' name = 'win' min = '0' value = '$tab[2]' />
                            </td>
                            <td>
                                <input type = 'number' name = 'loss' min = '0' value = '$tab[3]' />
                            </td>
                        </tr>
                        <tr>
                            <td colspan = '3'>
                                <input type = 'submit' value = 'Edytuj' />
                            </td>
                        </tr>";
                    }

                    $ret = "$ret </form>
                    </table>";

                    return $ret;

                }

                function e($db) // editing teams - query
                {
                    $tab = $_POST['e'];
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $win = $_POST['win'];
                    $loss = $_POST['loss'];

                    $name = htmlentities($name, ENT_QUOTES, "UTF-8");

                    if(!mysqli_query($db, "UPDATE $tab SET nazwa = '$name', wygrane = $win, przegrane = $loss WHERE id = $id"))
                    {
                        return "Nie udało się dokonać zmian";
                    }

                    return "Pomyślnie dokonano zniam";


                }

                function add() // adding teams
                {
                    return "<form method = 'POST' action = './'>
                        <input type = 'hidden' name = 'add' value = '0' />
                        <input type = 'text' name = 'name' placeholder = 'Nazwa drużyny' />
                        <select name = 'type'>
                            <option>2v2</option>
                            <option>5v5</option>
                        </select>
                        <input type = 'submit' value = 'Dodaj' />
                    </form>";
                }

                function a($db) //adding teams - query
                {

                    $name = $_POST['name'];
                    $type = $_POST['type'] . "teamy";

                    $name = htmlentities($name, ENT_QUOTES, "UTF-8");
                    $type = htmlentities($type, ENT_QUOTES, "UTF-8");

                    $q1 = mysqli_query($db, "SELECT id FROM $type WHERE nazwa = '$name'");

                    if(mysqli_num_rows($q1))
                    {
                        return "Nie udało się dodać drużyny $name. Drużyna istnieje";
                        
                    }

                    else if(!mysqli_query($db, "INSERT INTO $type SET nazwa = '$name'"))
                    {
                        return "Nie udało się dodać drużyny $name";
                    }

                    return "Dodano $name"; 

                }

                function players($db) // adding players
                {

                    $res2 = mysqli_query($db, "SELECT id, nazwa FROM 2v2teamy");

                    $res5 = mysqli_query($db, "SELECT id, nazwa FROM 5v5teamy");

                    $t2 = "2v2<select name = 't2'>
                    <option value = '0'>none</option>";
                    $t5 = "5v5<select name = 't5'>
                    <option value = '0'>none</option>";

                    while($tab = mysqli_fetch_row($res2))
                    {
                        $t2 = "$t2 <option value = '$tab[0]'>$tab[1]</optin>";
                    }

                    $t2 = "$t2 </select>";


                    while($tab = mysqli_fetch_row($res5))
                    {
                        $t5 = "$t5 <option value = '$tab[0]'>$tab[1]</optin>";
                    }

                    $t5 = "$t5 </select>";

                    return "<form id = 'p' method = 'POST' action = './?type=pa'>
                        <input type = 'text' name = 'nick' placeholder = 'nick' required />
                        <input type = 'text' name = 'nick_f' placeholder = 'nick faceit' required />
                        <input type = 'number' name = 'kill' min = '0' placeholder = 'Kill' />
                        <input type = 'number' name = 'death' min = '0' placeholder = 'Death' />
                        <input type = 'number' name = 'mvp' min = '0' placeholder = 'MVP' />
                        $t2
                        $t5
                        <input type = 'submit' value = 'Dodaj' onClick = 'sub()' />
                    </form>";
                }

                function pa($db) //adding players - query
                {

                    if(empty($_POST['nick']))
                    {
                        return "Nie podano poprawnych danych";
                    }

                    $nick = htmlentities($_POST['nick'], ENT_QUOTES, "UTF-8");
                    $nick_f = htmlentities($_POST['nick_f'], ENT_QUOTES, "UTF-8");
                    $kill = htmlentities($_POST['kill'], ENT_QUOTES, "UTF-8");
                    $death = htmlentities($_POST['death'], ENT_QUOTES, "UTF-8");
                    $mvp = htmlentities($_POST['mvp'], ENT_QUOTES, "UTF-8");
                    $t2 = htmlentities($_POST['t2'], ENT_QUOTES, "UTF-8");
                    $t5 = htmlentities($_POST['t5'], ENT_QUOTES, "UTF-8");

                    $nick = htmlentities($nick, ENT_QUOTES, "UTF-8");

                    if(!$kill > -1 OR empty($kill))
                    {
                        $kill = 0;
                    }
                    
                    if(!$death > -1 OR empty($dead))
                    {
                        $death = 0;
                    }
                    
                    if(!$mvp > -1 OR empty($mvp))
                    {
                        $mvp = 0;
                    }

                    if($t2 == 0 AND $t5 !== 0)
                    {
                        mysqli_query($db, "INSERT INTO `5v5zawodnicy` (`nick`, `nick_f`, `kill`, `death`, `MVP`, `id_team`) VALUES ('$nick', '$nick_f', '$kill', '$death', '$mvp', '$t5')");

                        return "Pomyślnie dodano zawodnika $nick do drużyny $t5";
                    }else if($t5 == 0 AND $t2 !== 0)
                    {
                        mysqli_query($db, "INSERT INTO `2v2zawodnicy` (`nick`, `nick_f`, `kill`, `death`, `MVP`, `id_team`) VALUES ('$nick', '$nick_f', '$kill', '$death', '$mvp', '$t2')");

                        return "Pomyślnie dodano zawodnika $nick do drużyny $t2";
                    }else if($t2 > 0 AND $t5 > 0)
                    {
                        mysqli_query($db, "INSERT INTO `5v5zawodnicy` (`nick`, `nick_f`, `kill`, `death`, `MVP`, `id_team`) VALUES ('$nick', '$nick_f', '$kill', '$death', '$mvp', '$t2')");
                        mysqli_query($db, "INSERT INTO `2v2zawodnicy` (`nick`, `nick_f`, `kill`, `death`, `MVP`, `id_team`) VALUES ('$nick', '$nick_f', '$kill', '$death', '$mvp', '$t5')");

                        return "Pomyślnie dodano zawodnika $nick do drużyn $t2 i $t5";
                    }

                    return "Nie udało się dodac zawodnika, musisz wybrać drużynę";


                }

                function ep($db) // editing players
                {

                    $res = mysqli_query($db, "SELECT 2v2zawodnicy.id, nick, nazwa FROM 2v2zawodnicy, 2v2teamy WHERE 2v2zawodnicy.id_team = 2v2teamy.id ORDER BY id_team");

                    $ret = "2v2
                    <table>
                        <tr>
                            <th>Nick</th>
                            <th>Team</th>
                            <th>Edycja</th>
                        </tr>";

                    while($tab = mysqli_fetch_row($res))
                    {
                        $ret = "$ret <tr>
                            <td>$tab[1]</td>
                            <td>$tab[2]</td>
                            <td>
                                <form method = 'POST' action = './?type=epe'>
                                    <input type = 'hidden' name = 'et' value = '2v2' />
                                   <input type = 'hidden' name = 'id' value = '$tab[0]' />
                                    <input type = 'submit' value = 'Edytuj' />
                                </form>

                                <form method = 'POST' action = './?type=epd' onSubmit = 'return confirm(" . '"Czy na pewno chcesz usunąć tego grasza? Tej operacji nie da się cofnąć"' . ");'>
                                    <input type = 'hidden' name = 'dt' value = '2v2' />
                                    <input type = 'hidden' name = 'id' value = '$tab[0]' />
                                    <input type = 'submit' value = 'Usuń' />
                                </form>
                            </td>
                        </tr>";
                    }

                    $ret = "$ret </table>";

                    $res = mysqli_query($db, "SELECT 5v5zawodnicy.id, nick, nazwa FROM 5v5zawodnicy, 5v5teamy WHERE 5v5zawodnicy.id_team = 5v5teamy.id ORDER BY id_team");

                    $ret = "$ret 5v5
                    <table>
                        <tr>
                            <th>Nick</th>
                            <th>Team</th>
                            <th>Edycja</th>
                        </tr>";

                    while($tab = mysqli_fetch_row($res))
                    {
                        $n = htmlentities($tab[1], ENT_QUOTES, "UTF-8");
                        $ret = "$ret <tr>
                            <td>$n</td>
                            <td>$tab[2]</td>
                            <td>
                                <form method = 'POST' action = './?type=epe'>
                                    <input type = 'hidden' name = 'et' value = '5v5' />
                                    <input type = 'hidden' name = 'id' value = '$tab[0]' />
                                    <input type = 'submit' value = 'Edytuj' />
                                </form>

                                <form method = 'POST' action = './?type=epd' onSubmit = 'return confirm(" . '"Czy na pewno chcesz usunąć tego grasza? Tej operacji nie da się cofnąć"' . ");'>
                                    <input type = 'hidden' name = 'dt' value = '5v5' />
                                    <input type = 'hidden' name = 'id' value = '$tab[0]' />
                                    <input type = 'submit' value = 'Usuń' />
                                </form>
                            </td>
                        </tr>";
                    }

                    $ret = "$ret </table>";

                    return $ret;

                }

                function epe($db) // edytnig player
                {
                    $et = htmlentities($_POST['et'], ENT_QUOTES, "UTF-8");
                    $id = htmlentities($_POST['id'], ENT_QUOTES, "UTF-8");

                    if(!is_numeric($id))
                    {
                        return "Niepoprawna wartość id";
                    }

                    $etz = $et . "zawodnicy";

                    $res = mysqli_query($db, "SELECT id, imie, nazwisko, nick, nick_f, `kill`, death, MVP, id_team FROM $etz WHERE id = $id");

                    $ret = "<form method = 'POST' action = './?type=epq' >";

                    while($tab = mysqli_fetch_row($res))
                    {
                        $ret = $ret . "<input type = 'hidden' name = 'id' value = '$tab[0]' />
                        Imię (puste)<input type = 'text' name = 'name' value = '$tab[1]' /><br />
                        Nazwisko (puste)<input type = 'text' name = 'lastname' value = '$tab[2]' /><br />
                        Nick<input type = 'text' name = 'nick' value = '$tab[3]' /><br />
                        Nick faceit<input type = 'text' name = 'nick_f' value = '$tab[4]' /><br />
                        Kill<input type = 'number' name = 'kill' value = '$tab[5]' /><br />
                        Death<input type = 'number' name = 'death' value = '$tab[6]' /><br />
                        MVP<input type = 'number' name = 'mvp' value = '$tab[7]' /><br />
                        2v2
                        <select id = '2t' name = '2t' onChange = 'fd()'>
                            <option>None</option>";

                        $res1 = mysqli_query($db, "SELECT id, nazwa FROM 2v2teamy");
                        while($tab1 = mysqli_fetch_row($res1))
                        {
                            if($tab1[0] == $tab[8] AND $et == '2v2')
                            {
                                $ret = "$ret <option selected value = '$tab1[0]'>$tab1[1]</option>";
                            }else{
                                $ret = "$ret <option value = '$tab1[0]'>$tab1[1]</option>";
                            }
                        }

                        $ret = "$ret </select>
                        5v5
                        <select id = '5t' name = '5t' onChange = 'fd()' onLoad = 'fd()'>
                            <option>None</option>";

                        $res2 = mysqli_query($db, "SELECT id, nazwa FROM 5v5teamy");
                        while($tab2 = mysqli_fetch_row($res2))
                        {
                            if($tab2[0] == $tab[8] AND $et == '5v5')
                            {
                                $ret = "$ret <option selected value = '$tab2[0]'>$tab2[1]</option>";
                            }else{
                                $ret = "$ret <option value = '$tab2[0]'>$tab2[1]</option>";
                            }
                        }

                        $ret = "$ret </select><br />";
                    }

                    return "$ret 
                        <input type = 'submit' value = 'Edytuj' />
                    </form>";

                }

                function epq($db) // editing players - query
                {
                    if(isset($_POST['id']))
                    {
                        if(empty($_POST['5t']) AND empty($_POST['2t']))
                        {
                            return "Błąd danych";
                        }

                        $id = htmlentities($_POST['id'], ENT_QUOTES, "UTF-8");
                        $name = htmlentities($_POST['name'], ENT_QUOTES, "UTF-8");
                        $lastname = htmlentities($_POST['lastname'], ENT_QUOTES, "UTF-8");
                        $nick = htmlentities($_POST['nick'], ENT_QUOTES, "UTF-8");
                        $nick_f = htmlentities($_POST['nick_f'], ENT_QUOTES, "UTF-8");
                        $kill = htmlentities($_POST['kill'], ENT_QUOTES, "UTF-8");
                        $death = htmlentities($_POST['death'], ENT_QUOTES, "UTF-8");
                        $mvp = htmlentities($_POST['mvp'], ENT_QUOTES, "UTF-8");

                        if(isset($_POST['2t']))
                        {
                            $t2 = htmlentities($_POST['2t'], ENT_QUOTES, "UTF-8");

                            if(!is_numeric($t2))
                            {
                                return "1. Błąd danych";
                            }

                            $id_t = $t2;

                            $tab = '2v2zawodnicy';

                        }else if(isset($_POST['5t']))
                        {
                            $t5 = htmlentities($_POST['5t'], ENT_QUOTES, "UTF-8");

                            if(!is_numeric($t5))
                            {
                                return "2 .Błąd danych";
                            }

                            $id_t = $t5;

                            $tab = '5v5zawodnicy';
                        }

                        if(!is_numeric($kill))
                        {
                            return "3. Błąd danych";
                        }

                        if(!is_numeric($death))
                        {
                            return "4 .Błąd danych";
                        }

                        if(!is_numeric($mvp))
                        {
                            return "5 .Błąd danych";
                        }

                        if(mysqli_query($db, "UPDATE $tab SET imie = '$name', nazwisko = '$lastname', nick = '$nick', nick_f = '$nick_f', `kill` = $kill, death = $death, MVP = $mvp, id_team = $id_t WHERE id = $id"))
                        {
                            return "Zapisano zmiany";
                        }
                        
                        return "Błąd, nie udało się zapisać zmian";
                    }

                    return "6. Błąd danych";
                }

                function epd($db) // deleting players
                {
                    $dt = htmlentities($_POST['dt'], ENT_QUOTES, "UTF-8");
                    $id = htmlentities($_POST['id'], ENT_QUOTES, "UTF-8");

                    $dtz = $dt . "zawodnicy";
                    $dtt = $dt . "teamy";

                    $res = mysqli_query($db, "SELECT nick, nazwa FROM $dtz, $dtt WHERE $dtz.id_team = $dtt.id AND $dtz.id = $id");
                    
                    while($tab = mysqli_fetch_row($res))
                    {
                        $nick = $tab[0];
                        $team = $tab[1];
                    }

                    if(mysqli_query($db, "DELETE FROM $dtz WHERE id = $id"))
                    {
                        return "Pomyślnie usunięto zawodnika $nick z $team w kategorii $dt";
                    }

                    return "Nie udało się usunąć zawodnika";
                }

                function m($db) // matches
                {
                    $ret = "<form method = 'POST' action = '?type=ma'>
                        <select name = 'type'>
                            <option>2v2</option>
                            <option>5v5</option>
                        </select>
                        <input type = 'submit' value = 'Dodaj mecz' />
                    </form>";

                    $res1 = mysqli_query($db, "SELECT grupy.nazwa, mecze2v2.id, 2v2teamy.nazwa, (SELECT 2v2teamy.nazwa FROM 2v2teamy, mecze2v2 WHERE mecze2v2.id_team2 = 2v2teamy.id), data, wynik FROM 2v2teamy, mecze2v2, grupy WHERE mecze2v2.id_team1 = 2v2teamy.id AND mecze2v2.gr = grupy.id");
                    $res2 = mysqli_query($db, "SELECT grupy.nazwa, mecze5v5.id, 5v5teamy.nazwa, (SELECT 5v5teamy.nazwa FROM 5v5teamy, mecze5v5 WHERE mecze5v5.id_team2 = 5v5teamy.id), data, wynik FROM 5v5teamy, mecze5v5, grupy WHERE mecze5v5.id_team1 = 5v5teamy.id AND mecze5v5.gr = grupy.id");

                    if(mysqli_num_rows($res1) == 0 AND mysqli_num_rows($res2) == 0)
                    {
                        $ret = "$ret Brak meczy w kategoriach 2v2 i 5v5";
                    }else if(mysqli_num_rows($res1) == 0 AND mysqli_num_rows($res2) > 0)
                    {
                        $ret = "$ret Brak meczy w kategorii 2v2 <br />
                        5v5 <table>
                            <tr>
                                <th>Data</th>
                                <th>Typ</th>
                                <th>Drużyna 1</th>
                                <th>Wynik</th>
                                <th>Drużyna 2</th>
                                <th>Edycja</th>
                            </tr>";

                            while($tab = mysqli_fetch_row($res2))
                            {
                                $ret = "<form method = 'POST' action = '?type=me'>
                                <tr>
                                    <td>$tab[4]</td>
                                    <td>$tab[0]</td>
                                    <td>$tab[3]</td>
                                    <td>$tab[5]</td>
                                    <td>$tab[2]</td>
                                    <td>
                                        <input type = 'hidden' name = 'id' value = '$tab[1]' />
                                        <input type = 'submit' value = 'Edytuj' />
                                    </td>
                                </tr>
                                </form>";
                            }
                            
                        $ret = "$ret </table>";
                            
                        $ret = "$ret </table>";
                    }else if(mysqli_num_rows($res1) > 0 AND mysqli_num_rows($res2) == 0)
                    {
                        $ret = "$ret Brak meczy w kategorii 5v5 <br />
                        2v2 <table>
                            <tr>
                                <th>Data</th>
                                <th>Typ</th>
                                <th>Drużyna 1</th>
                                <th>Wynik</th>
                                <th>Drużyna 2</th>
                                <th>Edycja</th>
                            </tr>";

                            while($tab = mysqli_fetch_row($res1))
                            {
                                $ret = "$ret <form method = 'POST' action = '?type=me'>
                                <tr>
                                    <td>$tab[4]</td>
                                    <td>$tab[0]</td>
                                    <td>$tab[3]</td>
                                    <td>$tab[5]</td>
                                    <td>$tab[2]</td>
                                    <td>
                                        <input type = 'hidden' name = 'id' value = '$tab[1]' />
                                        <input type = 'submit' value = 'Edytuj' />
                                    </td>
                                </tr>
                                </form>";
                            }
                            
                        $ret = "$ret </table>";
                    }else{
                        $ret = "$ret
                        2v2 <table>
                            <tr>
                                <th>Data</th>
                                <th>Typ</th>
                                <th>Drużyna 1</th>
                                <th>Wynik</th>
                                <th>Drużyna 2</th>
                                <th>Edycja</th>
                            </tr>";

                            while($tab = mysqli_fetch_row($res1))
                            {
                                $ret = "$ret <form method = 'POST' action = '?type=me'>
                                <tr>
                                    <td>$tab[4]</td>
                                    <td>$tab[0]</td>
                                    <td>$tab[3]</td>
                                    <td>$tab[5]</td>
                                    <td>$tab[2]</td>
                                    <td>
                                        <input type = 'hidden' name = 'id' value = '$tab[1]' />
                                        <input type = 'submit' value = 'Edytuj' />
                                    </td>
                                </tr>
                                </form>";
                            }
                            
                        $ret = "$ret </table>";

                        $ret = "$ret 5v5 <table>
                                <tr>
                                    <th>Data</th>
                                    <th>Typ</th>
                                    <th>Drużyna 1</th>
                                    <th>Wynik</th>
                                    <th>Drużyna 2</th>
                                    <th>Edycja</th>
                                </tr>";

                                while($tab = mysqli_fetch_row($res2))
                                {
                                    $ret = "$ret <form method = 'POST' action = '?type=me'>
                                    <tr>
                                    <td>$tab[4]</td>
                                    <td>$tab[0]</td>
                                    <td>$tab[3]</td>
                                    <td>$tab[5]</td>
                                    <td>$tab[2]</td>
                                    <td>
                                        <input type = 'hidden' name = 'id' value = '$tab[1]' />
                                        <input type = 'submit' value = 'Edytuj' />
                                    </td>
                                </tr>
                                    </form>";
                                }
                                
                            $ret = "$ret </table>";
                    }
                        return $ret;
                }

                function me($db) // metch edit
                {
                    
                }

                function ma($db, $t) // metch adding
                {
                    $ret = "<form method = 'POST' action = './?type=maq'>
                    Drużyna 1
                    <select name = 'team1'>";

                    $t1 = $t . "teamy";

                    $res1 = mysqli_query($db, "SELECT id, nazwa FROM $t1");

                    while($tab = mysqli_fetch_row($res1))
                    {
                        $ret = "$ret <option value = '$tab[0]'>$tab[1]</option>";
                    }

                    $ret = "$ret </select>
                    Wynik
                    <input type = 'number' name = 'r1' />
                    -
                    <input type = 'number' name = 'r2' />
                    Drużyna 2
                    <select name = 'team2'>";

                    $res2 = mysqli_query($db, "SELECT id, nazwa FROM $t1");

                    while($tab = mysqli_fetch_row($res2))
                    {
                        $ret = "$ret <option value = '$tab[0]'>$tab[1]</option>";
                    }

                    $ret = "$ret </select>
                    Data<input type = 'date' name = 'date' />";

                    $res3 = mysqli_query($db, "SELECT id, nazwa FROM grupy WHERE typ = '$t'");

                    $ret = "$ret <select name = 'type'>";

                    while($tab = mysqli_fetch_row($res3))
                    {
                        $ret = "$ret <option id = '$tab[0]'>";
                    }

                    return "</form> $ret";

                    //return "t: $t";
                }

                if(!include("./../db.php"))
                    {
                        return "Błąd łączenia z bazą danych";
                    }

                    $dbr = mysqli_connect($db_server, $db_user_r, $db_pass_r, $db_name);
                    $dbw = mysqli_connect($db_server, $db_user_w, $db_pass_w, $db_name);


                echo main($dbr, $dbw);

                mysqli_close($dbr);
                mysqli_close($dbw);
            ?>
        </body>
    </html>