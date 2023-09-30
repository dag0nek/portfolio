<!DOCTYPE html>
    <html lang = "pl-PL">
        <head>
            <title>Panel Administracyjny</title>
            <meta charset = "UTF-8" />
            <style>
                span#loge{
                    color: red;
                }
            </style>
        </head>
        <body>
            <div id = "content">
                <aside>
                    <form method = "POST" action = "./login.php">
                        <input type = "text" name = "login" placeholder = "Login" required /><br />
                        <input type = "password" name = "pass" placeholder = "Hasło" required /><br />
                        <input type = "submit" value = "Zaloguj" />
                    </form>
                    <?php
                        session_start();

                        if(isset($_SESSION['loge']))
                        {
                            echo "<span id = 'loge'>Błędny login lub hasło</span>";
                        }

                        unset($_SESSION['loge']);

                    ?>
                </aside>
            </div>
        </body>
    </html>