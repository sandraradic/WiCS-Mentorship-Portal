<html>
    <body style = "font-family: 'helvetica', monospace">
    <style>
            body {
            background-color: #fcd2ea;
            }
    </style>
    <head>
        <title>MENTORSHIP DATABASE</title>
    </head> 
    <img src="wics-logo.jpg" alt="wics logo" width="50" height="50"/>
    <h2>MENTORSHIP DATABASE </h2>
    <h3>
        LOGIN
    </h3> 
    <form method="POST" action="login.php">
        Enter ID: <input type="number" input name="pid">  <br /><br />
        <input type="submit" value="login" name="onClickSubmit">
    </form>

    <body>
        <a href="profile-signup.php">
            Create New Profile 
        </a> 
    </body>
    <?php
        // require __DIR__ . 'GUI/db-util.php';
        include './db-util.php';

        $success = True;
        $db_conn = NULL;
        $show_debug_alert_messages = False;

        
        
        function handleLoginRequest() {
            global $db_conn;
            $pid = $_POST['pid'];
            //$result = executePlainSQL('SELECT * FROM Person WHERE pid = ' . 1);
            $result = executePlainSQL("select * from Person where pid = " . $pid);
            // $row = oci_fetch_array($result, OCI_BOTH);
            // foreach($row as $key => $value){
            //     echo $value;
            // }   
            if ($row = oci_fetch_array($result, OCI_BOTH)) {
            //    header("Location: ./matches.php?pid=" . $pid);
                session_start(); 
                $_SESSION['pid'] = $pid;   
               header("Location: ./profile-update.php?pid=" . $pid);
               exit;
                echo 'success';
            } else {
                echo "<br><br> User ID not found. Please try a different ID, or create a new profile. <br>";
            }
        }

        // function connectToDB() {
        //     global $db_conn;

        //     // Your username is ora_(CWL_ID) and the password is a(student number). For example,
		// 	// ora_platypus is the username and a12345678 is the password.
        //     $db_conn = OCILogon("ora_sradic", "a23018088", "dbhost.students.cs.ubc.ca:1522/stu");

        //     if ($db_conn) {
        //         debugAlertMessage("Database is Connected");
        //         return true;
        //     } else {
        //         debugAlertMessage("Cannot connect to Database");
        //         $e = OCI_Error(); // For OCILogon errors pass no handle
        //         echo htmlentities($e['message']);
        //         return false;
        //     }
        // }

        // function disconnectFromDB() {
        //     global $db_conn;

        //     debugAlertMessage("Disconnect from Database");
        //     OCILogoff($db_conn);
        // }
        
        if (connectToDB()) {
            if (isset($_POST["onClickSubmit"])) {
                handleLoginRequest();
            }
            disconnectFromDB();
        }
    
        ?>
</html>