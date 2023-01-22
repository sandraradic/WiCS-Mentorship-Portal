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
    <a href="login.php"> LOGIN  </a> 
    <h3>
        Profile Signup Page
    </h3> 
    
    <form method="POST" action="profile-signup.php">
        <input type="hidden" id="createProfileRequest" name="createProfileRequest">

        First Name: <input name="firstName">  <br /><br />
        Last Name: <input name="lastName">  <br /><br />
        ID: <input name="pid" type="number">  <br />
        <p> If this returns an error, please enter a larger number. </p>
        Email: <input name="email">  <br /><br />
        Gender: <input type="radio" id="female" name="gender" value="female"><label for="female">female</label>
                 <input type="radio" id="male" name="gender" value="male"> <label for="male">male</label>
                 <input type="radio" id="Other" name="gender" value="Other"> <label for="Other">Other</label><br /><br />
        Gender Preference: <input type="radio" id="female" name="genderPref" value="female"><label for="female">female</label>
                 <input type="radio" id="male" name="genderPref" value="male"> <label for="male">male</label>
                 <input type="radio" id="Other" name="genderPref" value="Other"> <label for="Other">Other</label><br /><br />
        Year: <input name="year" type="number"> <br /><br />
        Degree: <input name="degree"> <br /><br />

        <br /><br />


        <h4>Are you a mentor or mentee?</h4>
        <div>
            <div>
            <input type="radio" name="personType" id="mentee" value="mentee" required>
            <label for="mentee">Mentee</label>
            
            <div class="reveal-if-active">
                <!-- <p>Interests</p>
                    Career: <input name="career">  <br /><br />
                    Major: <input name="major">  <br /><br /> -->
            </div>
            </div>
            
            <div>
            <input type="radio" name="personType" id="mentor" value="mentor">
            <label for="mentor">Mentor</label>
            
            <div class="reveal-if-active">
                Major: <input name="major">  <br /><br />
                <!-- Work Experience: 
                Company: <input name="company">, 
                Job Title: <input name="job title">, 
                Salary: <input name="salary"><label for="salary"> /year</label>, 
                Duration: <input name="duration"><label for="duration"> year(s)</label><br /><br /> -->
            </div>
            </div>
        </div>

        </div>
            <style>
            .reveal-if-active {
                opacity: 0;
                max-height: 0;
                overflow: hidden;
                }

            input[type="radio"]:checked ~ .reveal-if-active,
                input[type="checkbox"]:checked ~ .reveal-if-active {
                opacity: 1;
                max-height: 100px; 
                overflow: visible;
                }
            </style>
            
        <br /><br />
        <input type="submit" value="SAVE PROFILE" name="createProfileSubmit">
    </form>

    <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function displayProfileInfo() {
            $pid = $_POST['pid'];

            session_start(); 
            $_SESSION['pid'] = $pid; 

            $personType = $_POST['personType'];
            // session_start(); 
            // $pid = $_SESSION['pid'];
            // echo "<br> pid " . $pid . "<br>";

            $resultPerson = executePlainSQL("SELECT * 
                                        FROM Person 
                                        WHERE pid= '" . $pid ."'");

            if($row = oci_fetch_array($resultPerson, OCI_BOTH)) {
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
                            <p><u>Account Info:</u></p>
                            <p>First Name: ${firstName}</p>
                            <p>Last Name: ${lastName}</p>
                            <p>Email: ${email}</p>
                            <p>Year: ${year}</p>
                            <p>Gender: ${gender}</p>
                            <p>Gender Preference: ${genderPreference}</p>
                            <p>Degree: ${degree}</p>
                            <p>Mentor or Mentee: ${personType}</p>
                            
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
            // echo $cmdstr;

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    // echo $val;
                    // echo "<br>".$bind."<br>";
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

            $old_name = $_POST['oldName'];
            $new_name = $_POST['newName'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
            OCICommit($db_conn);
        }

        function personTypeRequest() {
            global $db_conn;

            $personType = $_POST['personType'];

            if ($personType == "mentee") {
                global $db_conn;

                //Getting the values from user and insert data into the table
                $pid = $_POST['pid'];

                $tuple = array (
                    ":bind1" => $pid
                );

                $alltuples = array (
                    $tuple
                );

                executeBoundSQL("insert into Mentee values (:bind1)", $alltuples);
                OCICommit($db_conn);
            } else if ($personType == "mentor") {
                global $db_conn;

                //Getting the values from user and insert data into the table
                $pid = $_POST['pid'];
                $major = $_POST['major'];

                // echo "test";
                // INSERT INTO Person VALUES (2, 'carlysmith@student.com','carly', 'smith', 2, 'female', 'male', 'BA');
                $tuple = array (
                    ":bind1" => $pid,
                    ":bind2" => $major
                );

                $alltuples = array (
                    $tuple
                );

                executeBoundSQL("insert into Mentor values (:bind1, :bind2)", $alltuples);
                OCICommit($db_conn);
            }

            OCICommit($db_conn);
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

            //Getting the values from user and insert data into the table
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $id = $_POST['pid'];
            $email = $_POST['email'];
            $gender = $_POST['gender'];
            $genderPref = $_POST['genderPref'];
            $year = $_POST['year'];
            $degree = $_POST['degree'];
            // echo "test";
            // INSERT INTO Person VALUES (2, 'carlysmith@student.com','carly', 'smith', 2, 'female', 'male', 'BA');
            $tuple = array (
                ":bind1" => $id,
                ":bind2" => $email,
                ":bind3" => $firstName,
                ":bind4" => $lastName,
                ":bind5" => $year,
                ":bind6" => $gender,
                ":bind7" => $genderPref,
                ":bind8" => $degree
            );

            $alltuples = array (
                $tuple
            );

            // Query: INSERT
            executeBoundSQL("insert into Person values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8)", $alltuples);
            OCICommit($db_conn);

            personTypeRequest();
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
                if (array_key_exists('createProfileRequest', $_POST)) {
                    handleInsertRequest();
                    displayProfileInfo();
                } 
                // else if (array_key_exists('createPersonTypeRequest', $_POST)) {
                //     personTypeRequest();
                // }

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

		if (isset($_POST['createProfileSubmit']) || isset($_POST['personTypeSubmit'])) {
            handlePOSTRequest();
        }
		?>


    
    
</html>