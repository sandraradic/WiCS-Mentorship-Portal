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
    <h3>
        Matches List Page
    </h3> 
    <form method="GET" action="matches.php">
        <input type="button" value="Matches" onclick = showMatches(true)>
        <input type="button" value="No Matches" onclick = showMatches(false)>
        <input type="button" value="Average Year" onclick = showAverageAge(true)>
    </form>
</table>

<script>
 function showMatches(show) {
    if (show) {
        matchList.style.display = "block";
        noMatchList.style.display = "none";
        averageAge.style.display = "none";
    } else {
        matchList.style.display = "none";
        noMatchList.style.display = "block";
        averageAge.style.display = "none";
    }
}   

function showAverageAge(show) {
    if (show) {
        matchList.style.display = "none";
        noMatchList.style.display = "none";
        averageAge.style.display = "block";
    } else {
        matchList.style.display = "none";
        noMatchList.style.display = "none";
        averageAge.style.display = "none";
    }
}   
</script>



<?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function showAverageAge() {
            // Query: AGGREGATION WITH GROUP BY
            $mentorAge = executePlainSQL("SELECT gender, AVG(year) 
                        FROM person, mentor 
                        WHERE person.pid = mentor.pid 
                        GROUP BY gender");
            $menteeAge = executePlainSQL("SELECT gender, AVG(year) 
                        FROM person, mentee 
                        WHERE person.pid = mentee.pid 
                        GROUP BY gender");

            echo "<table id= averageAge style='display: none'>";
            echo "<caption> Average Year </caption>";
            echo "<tr><th>Average Mentor Year</th></tr>";
            while($row = oci_fetch_array($mentorAge, OCI_BOTH)) {
                $gender = $row["GENDER"];
                $year = $row["AVG(YEAR)"];
                echo "<tr><td>" . $gender . "</td><td>" . $year . "</td></tr>";
            }
            echo "<tr><td><br></td></tr>";

            echo "<tr><th>Average Mentee Year</th></tr>";
            while($row = oci_fetch_array($menteeAge, OCI_BOTH)) {
                $gender = $row["GENDER"];
                $year = $row["AVG(YEAR)"];
                echo "<tr><td>" . $gender . "</td><td>" . $year . "</td></tr>";
            }
            echo "<tr><td><br></td></tr>";
            echo "</table>";
        }
        
        function displayMatchList() {
            $result = executePlainSQL("SELECT * FROM Match");
            $matchCount = executePlainSQL("SELECT COUNT(*) FROM Match");
            
            echo "<table id= matchList style='display: none'>";
            echo "<caption> Matches List </caption>";
            echo "<tr><td><br></td></tr>";
            echo "<tr><th>Mentor ID</th><th>Mentee ID</th></tr>";
            while($row = oci_fetch_array($result, OCI_BOTH)) {
                $mentorId = $row["MENTORID"];
                $menteeId = $row["MENTEEID"];
                echo "<tr><td>" . $mentorId . "</td><td>" . $menteeId . "</td></tr>";
            }

            echo "<tr><td><br></td></tr>";
            if($row = oci_fetch_array($matchCount, OCI_BOTH)) {
                $count = $row["COUNT(*)"];
                echo "<tr><td> Total Matches: " . $count . "</td></tr>";
            }
            echo "</table>";
        }

    function displayNoMatchList() {
        // Query: DIVISION
        $resultMentor = executePlainSQL("SELECT pid 
                                    FROM Mentor m
                                    WHERE NOT EXISTS
                                        (SELECT mentorID
                                        FROM Match ma
                                        WHERE ma.mentorID = m.pid)");
        $resultMentee = executePlainSQL("SELECT pid 
                                    FROM Mentee m
                                     WHERE NOT EXISTS
                                         (SELECT menteeID
                                         FROM Match ma
                                         WHERE ma.menteeID = m.pid)");
        echo "<table id= noMatchList style='display: none'>";
        echo "<caption> No Matches List </caption>";
        echo "<tr><td><br></td></tr>";
        echo "<tr><th>Mentor ID</th></tr>";
        // if (($row = oci_fetch_row($resultMentor)) == false) {
        //     echo "<tr><td> all mentors are matched </td></tr>";
        // }
        while($row = oci_fetch_array($resultMentor, OCI_BOTH)) {
            $mentorId = $row["PID"];
            echo "<tr><td>" . $mentorId . "</td></tr>";
        }
        echo "<tr><td><br></td></tr>";

        echo "<tr><th>Mentee ID</th></tr>";
        // if (($row = oci_fetch_row($resultMentor)) == false) {
        //     echo "<tr><td> all mentees are matched </td></tr>";
        // }
        while($row = oci_fetch_array($resultMentee, OCI_BOTH)) {
            $menteeId = $row["PID"];
            echo "<tr><td>" . $menteeId . "</td></tr>";
        }
        echo "<tr><td><br></td></tr>";
        echo "</table>";
    }

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
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

            //Getting the values from event and insert data into the table
            $eventName = $_POST['eventName'];
            $eventDate = $_POST['eventDate'];
            $sponsorName = $_POST['sponsorName'];
            $addressMain = $_POST['addressMain'];
            $roomNumber = $_POST['roomNumber'];
            // INSERT INTO SponsoredEvent VALUES (Banquet, 'May 5','Apple', '75 Agronomy Road', 144);
            $tuple = array (
                ":bind1" => $eventName,
                ":bind2" => $eventDate,
                ":bind3" => $sponsorName,
                ":bind4" => $addressMain,
                ":bind5" => $roomNumber
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into SponsoredEvent values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM demoTable");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            }
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('createEventRequest', $_POST)) {
                    handleInsertRequest();
                }
                console.log ("here");

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                }

                disconnectFromDB();
            }
        }

        if(connectToDB()) {
            displayMatchList();
            displayNoMatchList();
            showAverageAge();
      } else {
          echo "<br><br> Unable to retrieve your details. Please refresh your browser. <br>";
      }

		if (isset($_POST['createEventSubmit'])) {
            handlePOSTRequest();
        }
		?>
</html>