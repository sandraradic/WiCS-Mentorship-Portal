
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
    <a href="login.php"> LOGIN  </a> 
    <h3>
       Profile Update Page
    </h3>    
    <form method="POST" action="profile-update.php">
    <input type="hidden" id="updateProfileRequest" name="updateProfileRequest">



        First Name: <input name="firstName">  <br /><br />
        Last Name: <input name="lastName">  <br /><br />
        Email: <input name="email">  <br /><br />
        Gender: <input type="radio" id="female" name="gender" value="female"><label for="female">female</label>
                 <input type="radio" id="male" name="gender" value="male"> <label for="male">male</label>
                 <input type="radio" id="Other" name="gender" value="Other"> <label for="Other">Other</label><br /><br />
        Gender Preference: <input type="radio" id="female" name="genderPref" value="female"><label for="female">female</label>
                 <input type="radio" id="male" name="genderPref" value="male"> <label for="male">male</label>
                 <input type="radio" id="Other" name="genderPref" value="Other"> <label for="Other">Other</label><br /><br />

                 <br /><br />
        Year: <input name="year" type="number"> <br /><br />
        Degree: <input name="degree"> <br /><br />
        <!-- Mentor: <input type="radio" id="Yes" name="mentor" value="Yes"><label for="Yes">Yes</label>
                <input type="radio" id="No" name="mentor" value="No"> <label for="No">No</label><br /><br />
        Major: <input name="major">  <br /><br /> -->
        <!-- <p>Work Experience</p>
        Company: <input name="company">  <br /><br />
        Job Title: <input name="job title">  <br /><br />
        Salary: <input name="salary" type="number"><label for="salary"> /year</label><br /><br />
        Duration: <input name="duration" type="number"><label for="duration"> year(s)</label><br /><br />

        <br /><br />

        Mentee: <input type="radio" id="Yes" name="mentee" value="Yes"><label for="Yes">Yes</label>
                <input type="radio" id="No" name="mentee" value="No"> <label for="No">No</label><br /><br />
        
        <p>Interests</p>
        Career: <input name="career">  <br /><br />
        Major: <input name="major">  <br /><br /> -->
            

        <input type="submit" value="SAVE CHANGES" name="updateProfileSubmit">
    </form>

    <form method="POST" action="profile-update.php"> 
        <input type="hidden" id="deleteProfileRequest" name="deleteProfileRequest">
        Delete User Account Profile:
        <input type="submit" value="DELETE PROFILE" name="deleteProfileSubmit">
    </form>

    <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function displayBasicInfo() {
            session_start(); 
            $pid = $_SESSION['pid'];
            // echo "<br> pid " . $pid . "<br>";

            $result = executePlainSQL("SELECT * 
                                        FROM Person 
                                        WHERE pid= '" . $pid ."'");

            if($row = oci_fetch_array($result, OCI_BOTH)) {
                $pid = $row["PID"];
                $firstName = $row["FIRSTNAME"];
                $lastName = $row["LASTNAME"];
                $email = $row["EMAIL"];
                $year = $row["YEAR"];
                $gender = $row["GENDER"];
                $genderPreference = $row["GENDERPREFERENCE"];
                $degree = $row["DEGREE"];

                echo 
                <<<HTML
                     <h3>Profile UserID: ${pid}</h3>
                            <p><u>Basic Account Info:</u></p>
                            <p>First Name: ${firstName}</p>
                            <p>Last Name: ${lastName}</p>
                            <p>Email: ${email}</p>
                            <p>Year: ${year}</p>
                            <p>Gender: ${gender}</p>
                            <p>Gender Preference: ${genderPreference}</p>
                            <p>Degree: ${degree}</p>
                            
                    HTML;
            } else {
                echo "<br><br> PROFILE NOT FOUND <br>";
            }
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

        function handleUpdateRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $gender = $_POST['gender'];
            $genderPref = $_POST['genderPref'];
            $year = $_POST['year'];
            $degree = $_POST['degree'];

            session_start(); 
            $pid = $_SESSION['pid'];

            // Query: UPDATE
            executePlainSQL("UPDATE Person SET firstName='" . $firstName . "',
                 lastName='" . $lastName . "', 
                 email='" . $email . "', 
                 gender='" . $gender . "', 
                 genderPreference='" . $genderPref . "', 
                 year='" . $year . "',
                 degree='" . $degree . "' 
                 WHERE pid= '" . $pid ."'");

            OCICommit($db_conn);
        }

        function handleDeleteRequest() {
            global $db_conn;

            session_start(); 
            $pid = $_SESSION['pid'];

            // Query: DELETE
            executePlainSQL("DELETE FROM Person WHERE pid = '" . $pid . "'");

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
                if (array_key_exists('updateProfileRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('deleteProfileRequest', $_POST)) {
                    handleDeleteRequest();
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

        if (isset($_POST['updateProfileSubmit']) || isset($_POST['deleteProfileSubmit'])) {
            handlePOSTRequest();
            // displayBasicInfo();
        }

        if(connectToDB()) {
            displayBasicInfo();
            // if (isset($_POST['updateProfileSubmit']) || isset($_POST['deleteProfileSubmit'])) {
            //     handlePOSTRequest();
            //     // displayBasicInfo();
            // }
    
            // if (isset($_POST['deleteProfileSubmit'])) {
            //     handlePOSTRequest();
            // }
    
        } else {
            echo "<br><br> Unable to retrieve your details. Please refresh your browser. <br>";
        }
		?>


    
    
</html>