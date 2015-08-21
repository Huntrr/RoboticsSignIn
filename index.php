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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>B[Ar]low Rob[Au]tics</title>
        
        <link rel="stylesheet" href="style.css" />
	    <script src="jquery.js"></script>
	    <script src="hydrate.js"></script>
	    <script src="signin.js"></script>
    </head>
    <body>
        <div id="outer">
            <div id="inner">
            
                <div class="container">
                    <h1>B[Ar]low Rob[Au]tics Sign-In:</h1>
                    <div id="content">
                        <span style="font-weight: bold;">
                            <div class="name" style="text-align: center;">Name:</div>
                            <div class="in" style="text-align: center;">Time in:</div>
                            <div class="out" style="text-align: center;">Time out:</div>
                            <div class="x" style="text-align: center;">X</div>
                        </span>
                        
                        <div class="name">
                            <input id="input-name" style="width: 90%;" />
                        </div>
                        <div class="in">
                            <input type="button" id='initIn' value="Sign in" />
                        </div>
                        <div class="out">
                            --
                        </div>
                        <div class="x">
                            --
                        </div>
                        
                        <div id="roboList"></div>
                        
                    </div>
                    
                    <input type="button" id='headhome' value="Head Home" />
                    <input type="button" id='logout' value="Log Out" />
                    <input type="button" value="View Hours" onclick="window.location.href='times.php'"/>
                    <input type="button" id="users" value="View User Log" <?php if($_SESSION['admin'] == FALSE) { echo "disabled='true'"; } else { echo "onclick=\"window.location.href='user.php'\""; } ?> />
                </div>
                
            </div>
        </div>
    </body>
</html>