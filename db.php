<?php

/*
SELECT    name,
          SUM(time) AS total,
          SUM(CASE WHEN YEAR(date)=YEAR(CURDATE()) THEN time ELSE 0 END) as season
FROM      robonauts_day
GROUP     BY name
ORDER BY  total DESC
*/

    session_start();
    if(!isset($_SESSION['login'])) {
        $_SESSION['login'] = FALSE;
    }
    
    if($_SESSION['login'] == FALSE) {
        echo "FAIL";
        exit;
    }
    
    if(!isset($_SESSION['admin'])) {
        $_SESSION['admin'] = FALSE;
    }
    
    //MAKE SURE TO CONFIGURE THIS USENAME AND PASSWORD BIT
    $USERNAME = "MYSQL USERNAME";
    $PASSWORD = "MYSQL PASSWORD";
    $HOST = "MYSQL HOST (URL)";

    $MAIN_TABLE = "robonauts_all";
    $DAY_TABLE = "robonauts_day";
    
    $db = new mysqli($HOST, $USERNAME, $PASSWORD, $USERNAME);

    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    
    //Returns a full list of users, sorted from max-time to min-time
    function getAll() {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        $sql = "SELECT name FROM $MAIN_TABLE ORDER BY time DESC;";
        $result = $db->query($sql);
        
        $data = array();
        while($row = $result->fetch_array()) {
            $data[] = $row;
        }
        $result->free();
        
        echo json_encode($data);
    }
    
    //Submits the day's work to the database (CLEAN FOR BOTH TABLES)
    function submitDay($array) {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        
        $fixedName = '';
        $fixedTime = '';
        
        $stmt2 = $db->prepare("INSERT INTO $MAIN_TABLE (name, time) VALUES (?, ?) ON DUPLICATE KEY UPDATE time = time + VALUES(time);");
        $stmt2->bind_param('si', $fixedName, $fixedTime);
        
        $stmt1 = $db->prepare("INSERT INTO $DAY_TABLE (date, name, time) VALUES (CURDATE(), ?, ?);");
        $stmt1->bind_param('si', $fixedName, $fixedTime);
        
        foreach ($array as $value) {
            $fixedName = $value[0];
            $fixedTime = $value[1];
            
            $stmt1->execute();
            $stmt2->execute();
        }
        
        echo json_encode("DONE");
    }
    
    //Submits a new user to add
    function add($array) {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        
        
        $stmt = $db->prepare("REPLACE INTO $MAIN_TABLE (name, time) VALUES(?, ?);");
        
        $fixedName = $array[0];
        $fixedTime = $array[1];
        $stmt->bind_param('si', $fixedName, $fixedTime);
        
        $stmt->execute();
        
        echo json_encode("DONE");
    }
    
    //Drops a user
    function removeUser($name) {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        
        $stmt = $db->prepare("DELETE FROM $MAIN_TABLE WHERE name = ?;");
        $stmt2 = $db->prepare("DELETE FROM $DAY_TABLE WHERE name = ?;");
        
        $fixedName = $name;
        $stmt->bind_param('s', $fixedName);
        $stmt2->bind_param('s', $fixedName);
        
        $stmt->execute();
        $stmt2->execute();
        
        echo json_encode("DONE");
    }
    
    //Submits a set of updates
    function update($array) {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        
        $stmt = $db->prepare("REPLACE INTO $MAIN_TABLE (name, time) VALUES (?, ?);");
        
        $fixedName = '';
        $fixedTime = '';
        $stmt->bind_param('si', $fixedName, $fixedTime);
        
        foreach ($array as $value) {
            $fixedName = $value[0];
            $fixedTime = $value[1];
            
            $stmt->execute();
        }
        
        echo json_encode("DONE");
    }
    
    
    //Returns array of usernames and total times
    function loadList() {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        $sql = "SELECT name, SUM(time) AS total, SUM(CASE WHEN YEAR(date)=YEAR(CURDATE()) THEN time ELSE 0 END) as season FROM robonauts_day GROUP BY name ORDER BY season DESC;";
        $result = $db->query($sql);
        
        $data = array();
        while($row = $result->fetch_array()) {
            $data[] = $row;
        }
        $result->free();
        
        echo json_encode($data);
    }
    
/*
SELECT    name,
          SUM(time) AS total,
          SUM(CASE WHEN YEAR(date)=YEAR(CURDATE()) THEN time ELSE 0 END) as season
FROM      robonauts_day
GROUP     BY name
ORDER BY  total DESC
*/

    //Returns a more detailed array of usernames and total times
    function loadUserList() {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        $sql = "SELECT name, SUM(time) AS total, SUM(CASE WHEN YEAR(date)=YEAR(CURDATE()) THEN time ELSE 0 END) as season FROM robonauts_day GROUP BY name ORDER BY season DESC;";
        $result = $db->query($sql);
        
        $data = array();
        while($row = $result->fetch_array()) {
            $row[] = loadUser($row[0]);
            $data[] = $row;
        }
        $result->free();
        
        echo json_encode($data);
    }
    
    
    function loadUser($name) {
        global $MAIN_TABLE, $DAY_TABLE, $db;
        
        $stmt = $db->prepare("SELECT date, time FROM $DAY_TABLE WHERE name = ? ORDER BY date DESC;");
        $fixedName = $name;
        $stmt->bind_param('s', $fixedName);
        
        $stmt->execute();
        $stmt->bind_result($date, $time);
        
        $data = array();
        while($stmt->fetch()) {
            $row = array();
            $row[] = $date;
            $row[] = $time;
            $data[] = $row;
        }
     
        // Close statement object
        $stmt->close();
        
        return $data;
    }
    
    if(isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];
        switch($action) {
            case 'getAll': 
                getAll();
            break;
            
            case 'loadList':
                loadList();
            break;
            
            case 'loadUserList':
                loadUserList();
            break;
            
            case 'submitDay':
                submitDay($_POST['arr']);
            break;
            
            case 'add':
                if($_SESSION['admin'] == TRUE) {
                    add($_POST['arr']);
                } else {
                    echo "FAIL";
                }
            break;
            
            case 'remove':
                if($_SESSION['admin'] == TRUE) {
                    removeUser($_POST['val']);
                } else {
                    echo "FAIL";
                }
            break;
            
            case 'update':
                if($_SESSION['admin'] == TRUE) {
                    update($_POST['arr']);
                } else {
                    echo "FAIL";
                }
            break;
            
            case 'logout':
                $_SESSION = array();
                session_destroy();
                echo json_encode("DONE");
            break;
            
            default:
                echo "FAIL";
            break;
        }
    }
    
    $db->close();
?>
