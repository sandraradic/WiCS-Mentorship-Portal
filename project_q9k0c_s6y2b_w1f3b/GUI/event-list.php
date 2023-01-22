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
    <h2>MENTORSHIP DATABASE </h2>
    <img src="wics-logo.jpg" alt="wics logo" width="50" height="50"/>
    <a href="profile-update.php"> PROFILE  </a> &nbsp; 
    <a href="event-list.php"> EVENTS  </a> &nbsp;
    <a href="matches.php"> PEOPLE  </a> &nbsp;
    <a href="login.php"> LOGIN </a> 
    <h3> Upcoming Events </h3>

    <a href="event-create.php"> CREATE EVENT</a> &nbsp;
    <br>
    <br>
    <form method="GET" action="matches.php">
        <input type="button" value="Low Attendance" onclick = showAttendance(true)>
        <input type="button" value="Highest Attendance" onclick = showAttendance(false)>
    </form>

<script>
 function showAttendance(show) {
    if (show) {
        lowAttendance.style.display = "block";
        highAttendance.style.display = "none";
    } else {
        lowAttendance.style.display = "none";
        highAttendance.style.display = "block";
    }
}   
</script>

<?php

		//this tells the system that it's no longer just parsing html; it's now parsing PHP
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())


        function displayOptions() {
            // Query: PROJECTION
            //EVENT NAME
            $result = executePlainSQL("SELECT eventName, eventDate, sponsorName FROM SponsoredEvent");
            echo "<form method='GET' action='event-list.php'";
            echo "<label for='eventType'>event type </label>";
            echo "<select name='eventType' id='eventType'>";
            while ($row = oci_fetch_array($result, OCI_BOTH)) {
                $eventName = $row["EVENTNAME"];
                echo $eventName;
                echo "<option value='". $eventName . "'>" . $eventName . "</option>";

            }
            echo "</select>";

             // Query: PROJECTION
            //SPONSOR
            $result = executePlainSQL("SELECT eventName, eventDate, sponsorName FROM SponsoredEvent");
            //echo "<form method='GET' action='event-list.php'";
            echo "<label for='sponsorName'> Sponsor  </label>";
            echo "<select name='sponsorName' id='sponsorName'>";
            while ($row = oci_fetch_array($result, OCI_BOTH)) {
                $sponsorName = $row["SPONSORNAME"];
                echo $sponsorName;
                echo "<option value='". $sponsorName . "'>" . $sponsorName . "</option>";
            }
            echo "</select>";

             // Query: PROJECTION
            //EVENT DATE
            $result = executePlainSQL("SELECT eventName, eventDate, sponsorName FROM SponsoredEvent");
            //echo "<form method='GET' action='event-list.php'";
            echo "<label for='eventDate'> Date  </label>";
            echo "<select name='eventDate' id='eventDate'>";
            while ($row = oci_fetch_array($result, OCI_BOTH)) {
                $eventDate = $row["EVENTDATE"];
                echo $eventDate;
                echo "<option value='". $eventDate . "'>" . $eventDate . "</option>";
            }
            echo "</select>";

            echo 
            <<<HTML
            <input type="submit" value="FILTER" name="filterResults">

            HTML;
            echo "</form>";



        }

        function displayEventsList($eventType, $eventDate, $sponsorName) {
            // Query: SELECTION
            $result = executePlainSQL("SELECT * FROM SponsoredEvent 
                                        WHERE eventName = '" . $eventType . "' AND 
                                            eventDate='" . $eventDate . "'
                                            AND sponsorName='" . $sponsorName . "'");

            // Query: JOIN
            $numResult = executePlainSQL("SELECT Person.firstName, Person.lastName
                                        FROM Attends, Person
                                        WHERE Attends.pid = Person.pid
                                        AND Attends.eventName = '" . $eventType . "'
                                        AND Attends.eventDate = '" . $eventDate . "'");

            if($row = oci_fetch_array($result, OCI_BOTH)) {
                $addressMain = $row["ADDRESSMAIN"];
                $roomNumber = $row["ROOMNUMBER"];

                echo "<table id= eventDetail>";
                echo "<tr><th>Event Details</th></tr>";
                echo "<tr><td> Date: " . $eventDate . "</td></tr>";
                echo "<tr><td> Address: " . $addressMain . "</td></tr>";
                echo "<tr><td> Sponsor: " . $sponsorName . "</td></tr>";
        
            } else {
                echo "<br> No event exists with selected filters. <br><br>";
            }
            echo "<tr><td><br></td></tr>";
            echo "<tr><th>Attendee(s) Name</th></tr>";
            while($row = oci_fetch_array($numResult, OCI_BOTH)) {
                $firstName = $row["FIRSTNAME"];
                $lastName = $row["LASTNAME"];
                echo "<tr><td> " . $firstName . $lastName . "</td></tr>";
            }
            
            echo "</table>";
            echo "</br>";
        }

        function displayLowAttendance() {
            // Query: AGGREGATION WITH HAVING
            $result = executePlainSQL("SELECT eventName, COUNT(eventName) 
                                        FROM Attends
                                        GROUP BY eventName
                                        HAVING COUNT(*) < 2");
            echo "<table id=lowAttendance style='display: none'>";
            echo "<tr><th>Event(s) with Lowest Attendance</th></tr>";
            while($row = oci_fetch_array($result, OCI_BOTH)) {
                $eventName = $row["EVENTNAME"];
                echo "<tr><td>" . $eventName . "</td></tr>";
            }
            echo "</table>";
        }

        function displayGreatestAttendance() {
             // Query: NESTED AGGREGATION WITH GROUP BY
            $result = executePlainSQL("SELECT eventName 
                                        FROM attends 
                                        GROUP BY eventName 
                                        HAVING count(pid) >= 
                                            all(SELECT count(pid) 
                                                FROM attends 
                                                GROUP BY eventName)");
            
            echo "<table id=highAttendance style='display: none'>";
            echo "<tr><th>Event(s) with Highest Attendance</th></tr>";
            while($row = oci_fetch_array($result, OCI_BOTH)) {
                $eventName = $row["EVENTNAME"];
                echo "<tr><td>" . $eventName . "</td></tr>";
            }
            echo "</table>";
        }

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            // echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }
			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);
            echo "hi there";
            console.log($statement);
            echo $cmdstr;

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    echo $val;
                    echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table demoTable:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_sradic", "a23018088", "dbhost.students.cs.ubc.ca:1522/stu");
            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE demoTable");

            // Create new table
            echo "<br> creating new table <br>";
            executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;
            session_start(); 
            echo $pid;
            echo $eventType;
            echo $eventDate;
            //Getting the values from event and insert data into the table
            $pid = $_SESSION['pid'];
            $eventType = $_POST['eventType'];
            $eventDate = $_POST['eventDate'];
            // INSERT INTO Attends VALUES(1, 'Banquet2', 'May 5')
            $tuple = array (
                ":bind1" => $pid,
                ":bind2" => $eventType,
                ":bind3" => $eventDate,
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into Attends values (:bind1, :bind2, :bind3)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM demoTable");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleFilterRequest() {
            if (connectToDB()) {
                $eventType = $_GET['eventType'];
                $sponsorName = $_GET['sponsorName'];
                $eventDate = $_GET['eventDate'];
                $show = true;
                echo 
                <<<HTML
                     <p><u><b>${eventType} hosted by ${sponsorName} on ${eventDate}</b></u></p>
                    HTML;

                    displayEventsList($eventType, $eventDate, $sponsorName);
                
                disconnectFromDB();
            }
        }

        if (isset($_GET['filterResults'])) {
            handleFilterRequest();
        }

        if(connectToDB()) {
            displayOptions();
            displayGreatestAttendance();
            displayLowAttendance();
            displayEventsList();
        } else {
            echo "<br><br> Unable to retrieve your details. Please refresh your browser. <br>";
        }
		?>

</html>