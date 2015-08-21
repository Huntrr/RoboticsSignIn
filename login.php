<?php
    session_start();
    
    $password = "BASIC PASSWORD";
    $adminpassword = "ADMIN PASSWORD";
    $badlogin = FALSE;
    
    if(isset($_POST['password'])) {
        if(!isset($__SESSION['login'])) {
            $_SESSION['login'] = FALSE;
        }
        
        if($_POST['password'] == $password || $_POST['password'] == $adminpassword) {
            $_SESSION['login'] = TRUE;
            $_SESSION['admin'] = FALSE;
            
            if($_POST['password'] == $adminpassword) {
                $_SESSION['admin'] = TRUE;
            }
            
        } else {
            $badlogin = TRUE;
        }
    }
    
    if(isset($_SESSION['login'])) {
        if($_SESSION['login'] == TRUE) {
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP-SELF']), '/\\');
            $page = 'robits/index.php';
            header("Location: http://$host$uri/$page");
            
            exit;
        }
    } else {
        $_SESSION['login'] = FALSE;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>B[Ar]low Rob[Au]tics</title>
        
        <link rel="stylesheet" href="style.css" />
	    <script src="jquery.js"></script>
	    <script src="script.js"></script>
    </head>
    <body>
        <div id="outer">
            <div id="inner">
            
                <div class="container">
                    <h1>B[Ar]low Rob[Au]tics:</h1>
                    <div id="content" style="text-align: center;">
                        <p><?php
                            if($badlogin == TRUE) {
                                echo "Whoops! The password you entered was incorrect";
                            }
                            else {
                                echo "<span style='color: red;'> Sorry, you're not signed in to log hours</span>";
                            }
                        ?></p>
                        
                        <form action="login.php" method="post">
                            Password: <input name="password" type="password" />
                            <br />
                            <input type="submit" value="Log in" />
                        </form>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </body>
</html>
