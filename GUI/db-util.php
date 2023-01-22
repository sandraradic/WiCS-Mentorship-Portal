<html>
<body style = "font-family: 'Monaco', monospace">
<style>
        body {
        background-color: pink;
        }
</style>
</body>
</html>
<?php
    function debugAlertMessage($message) {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }

    function executePlainSQL($cmdstr) {
        // echo $cmdstr;
        global $db_conn, $success;

        $statement = OCIParse($db_conn, $cmdstr); 

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement);
            echo htmlentities($e['message']);
            $success = False;
        }

        return $statement;
    }

    function executeBoundSQL($cmdstr, $list) {

       
    }

    function connectToDB() {
        global $db_conn;

        $db_conn = OCILogon("ora_sradic", "a23018088", "dbhost.students.cs.ubc.ca:1522/stu");

        if ($db_conn) {
            debugAlertMessage("Connected succesfully");
            return true;
        } else {
            debugAlertMessage("Connection failed");
            $e = OCI_Error();
            echo htmlentities($e['message']);
            return false;
        }
    }

    function disconnectFromDB() {
        global $db_conn;

        debugAlertMessage("Disconnected successfully");
        OCILogoff($db_conn);
    }  

    function debugResult($result) {
        while ($row = oci_fetch_array($result, OCI_NUM)) {
            echo "<br />";
            foreach($row as $val) {
                echo $val;
                echo "&nbsp;";
            }
            echo "<br />";
        }
    }
    
?>