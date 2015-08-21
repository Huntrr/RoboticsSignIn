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
    
    if(!isset($_SESSION['admin'])) {
        $_SESSION['admin'] = FALSE;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>B[Ar]low Rob[Au]tics</title>
        
        <link rel="stylesheet" href="style.css" />
	    <script src="jquery.js"></script>
	    <script src="hydrate.js"></script>
	    <script src="times.js"></script>
    </head>
    <body>
        <div id="admin" style="display: none;"><?php if($_SESSION['admin'] == TRUE) { echo 'TRUE'; } else { echo 'FALSE'; } ?></div>
        <div id="outer">
            <div id="inner">
            
                <div class="container">
                    <h1>B[Ar]low Rob[Au]tics Hours:</h1>
                    <div id="content">
                        <span style="font-weight: bold;">
                            <div class="name" style="text-align: center;">Name:</div>
                            <div class="totalHours" style="text-align: center;">Hours:</div>
                            <div class="x" style="text-align: center;">X</div>
                        </span>
                        
                        <!--<div class="name">
                            <input id="input-name" style="width: 90%;" />
                        </div>
                        <div class="totalHours">
                            <input id='time' type="text"/>
                            <input type="button" id='add' value="ADD" <?php if($_SESSION['admin'] == FALSE) { echo "disabled='true'"; } ?>/>
                        </div>
                        <div class="x">
                            --
                        </div>-->
                        
                        <div id="roboList"><div class="full">Loading...</div></div>
                        
                    </div>
                    
                    <!--<input type="button" id="update" value="Update" <?php if($_SESSION['admin'] == FALSE) { echo "disabled='true'"; } ?> />-->
                    <input type="button" id='logout' value="Log Out" />
                    <input type="button" value="Back to Sign-In" onclick="window.location.href='index.php'" />
                    <input type="button" id="users" value="View User Log" <?php if($_SESSION['admin'] == FALSE) { echo "disabled='true'"; } else { echo "onclick=\"window.location.href='user.php'\""; } ?> />
                    <?php if($_SESSION['admin'] == FALSE) { echo '<p style=\'font-size: 11px; color: red;\'> Not logged in as administrator; can\'t manually edit values</p>'; } ?>
                </div>
                
            </div>
        </div>
    </body>
</html>