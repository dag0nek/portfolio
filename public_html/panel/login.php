<?php
    session_start();
    
    function login()
    {
        if(!include_once("./db.php"))
        {
            return "Przepraszamy akutalnie logowanie nie jest możliwe";
        }

        if(!isset($_POST['login']) AND !isset($_POST['pass']))
        {
            return 0; // przenoszenie do logowania
        }

        $login = $_POST['login'];

        $login = htmlentities($login, ENT_QUOTES, "UTF-8");

        $pass = $_POST['pass'];

        //$pass = password_hash($pass, PASSWORD_DEFAULT);

        $db = mysqli_connect($db_server, $db_user_r, $db_pass_r, $db_name);

       //mysqli_query($db, "INSERT INTO login SET nick = '$login', password = '$pass'");

        $res = mysqli_query($db, "SELECT password FROM login WHERE nick = '$login' LiMIT 1");

        while($tab = mysqli_fetch_row($res))
        {
            $hash = $tab[0]; // zaq1@WSX
        }

        if(!password_verify($_POST['pass'], $hash))
        {
            $_SESSION['loge'] = 1;
            header("Location: ./");
            return 3; // login error
        }

        mysqli_close($db);

        $_SESSION['login'] = 1;

        header("Location: ./admin/");

        return 3; // login

    }

    echo login();
?>