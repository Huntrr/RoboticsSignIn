<?php
    session_start();
    
    if(!isset($_SESSION['login'])) {
        $_SESSION['login'] = FALSE;
    }
    
    if($_SESSION['login'] == FALSE) {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP-SELF']), '/\\');
        $page = 'robits/login.php';
        header("Location: http://$host$uri/$page");
        
        exit;
    }
    
    if($_SESSION['admin'] == FALSE) {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP-SELF']), '/\\');
        $page = 'robits/index.php';
        header("Location: http://$host$uri/$page");
        
        exit;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>B[Ar]low Rob[Au]tics</title>
        
        <link rel="stylesheet" href="style.css" />
	    <script src="jquery.js"></script>
	    <script src="hydrate.js"></script>
	    <script src="user.js"></script>
    </head>
    <body>
        <div id="outer">
            <div id="inner">
                <div class="container">
                    <h1>B[Ar]low Rob[Au]tics User Log:</h1>
                    <div id="content">
                        <span style="font-weight: bold;">
                            <div class="name" style="text-align: center;">Name:</div>
                            <div class="totalHours" style="text-align: center;">Hours:</div>
                            <div class="x" style="text-align: center;"> </div>
                        </span>
                        
                        
                        
                        <div id="roboList"><div class="full">Loading...</div>

                        
                        </div>
                        
                    </div>
                    
                    <input type="button" id='logout' value="Log Out" />
                    <input type="button" value="Back to Sign-In" onclick="window.location.href='index.php'" />
                    <input type="button" value="Back to View Hours" onclick="window.location.href='times.php'"/>
                    
                </div>
                
            </div>
        </div>
    </body>
</html>