<?php
    error_reporting(E_ALL ^ E_NOTICE); //specify All errors and warnings are displayed
    session_start();
    extract($_POST);
    $idErr = "";
    $emailErr = "";
    $firstNameErr = "";
    $lastNameErr = "";
    $passwordErr = "";
    $passwordAgainErr = "";

    //connect to MySQL DB
    include("db.php");
    $pdo = connect();

    if(isset($submit)){  //if the page is requested due to the form submission, NOT the first time request
        //Method 2: prevent SQL-injection attack - PDO prepared statement
        $sqlId = "SELECT StudentId FROM Student WHERE StudentId = :id";
        $preparedStatement1 = $pdo -> prepare($sqlId);
        $preparedStatement1 -> execute(['id'=>$id]);
        $row = $preparedStatement1 -> fetch(PDO::FETCH_ASSOC);
        if($row){
            $idErr = "A student with this ID already exist";
        }
        else{
            $idErr = ValidateId($id);
        }
        $emailErr = ValidateEmail($email);
        $firstNameErr = ValidateFirstName($firstName);
        $lastNameErr = ValidateLastName($lastName);
        $passwordErr = ValidatePassword($Password);
        $passwordAgainErr = ValidatePasswordAgain($passwordAgain, $Password);

        if(!$idErr && !$emailErr && !$firstNameErr && !$lastNameErr && !$passwordErr && !$passwordAgainErr)
        {
            //store data in session
            $_SESSION["id"] = $id;
            $_SESSION["email"] = $email;
            $_SESSION["firstName"] = $firstName;
            $_SESSION["lastName"] = $lastName;
            $_SESSION["Password"] = $Password;
            $_SESSION["passwordAgain"] = $passwordAgain;

           

            //hash password
            $hashedPassword = hash("sha256", $Password);

            //Method 2: prevent SQL-injection attack - PDO prepared statement
            $sqlInsert = "INSERT INTO Student(StudentId,email,firstName,lastName,Password) VALUES(:id,:email,:firstName,:lastName,:hashedPassword)";
            $preparedStatement2 = $pdo -> prepare($sqlInsert);
            $preparedStatement2 -> execute(['id'=>$id, 'email'=>$email, 'firstName'=>$firstName, 'lastName'=>$lastName, 'hashedPassword'=>$hashedPassword]);
            $_SESSION["login"] = "true";
            header("Location: CourseSelection.php");
        }
    }
    else{
        //if the data has been stored in the session, display the data on the page when the user enters this page
        $id = $_SESSION["id"] ?? "";
        $email = $_SESSION["email"] ?? "";
        $firstName = $_SESSION["firstName"] ?? "";
        $lastName = $_SESSION["lastName"] ?? "";
        $Password = $_SESSION["Password"] ?? "";
        $passwordAgain = $_SESSION["passwordAgain"] ?? "";
    }


    if(isset($clear)) {
        $id = '';
        $email = '';
        $firstName = '';
        $lastName = '';
        $Password = '';
        $passwordAgain = '';
    }


    include("Header.php");
    print <<<HTML
        <div class="container">
            <h1>Sign Up</h1>
            <p>All fields are required</p>
            <form action="NewUser.php" method="post">
                <div class="row form-group form-inline">
                    <label for="id" class="col-md-2">Student ID: </label>
                    <input type="text" id="id" name="id" class="form-control col-md-3" value="$id">
                    <span class="errorMsg">$idErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="email" class="col-md-2">Email: </label>
                    <input type="text" id="email" name="email" class="form-control col-md-3" value="$email">
                    <span class="errorMsg">$emailErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="fistName" class="col-md-2">First Name: </label>
                    <input type="text" id="firstName" name="firstName" class="form-control col-md-3" value="$firstName">
                    <span class="errorMsg">$firstNameErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="lastName" class="col-md-2">Last Name: </label>
                    <input type="text" id="lastName" name="lastName" class="form-control col-md-3" value="$lastName">
                    <span class="errorMsg">$lastNameErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="Password" class="col-md-2">Password: </label>
                    <input type="password" id="Password" name="Password" class="form-control col-md-3" value="$Password">
                    <span class="errorMsg">$passwordErr</span>
                </div>
                <div class="row form-group form-inline">
                    <label for="passwordAgain" class="col-md-2">Confirm password: </label>
                    <input type="password" id="passwordAgain" name="passwordAgain" class="form-control col-md-3" value="$passwordAgain">
                    <span class="errorMsg">$passwordAgainErr</span>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                <button type="submit" name="clear" class="btn btn-primary">Clear</button>
            </form>
            <p>already a member? <a href="login.php">login</a> here</p>
        </div>
    HTML;

    function ValidateId($id): string
    {
        if(!trim($id))
        {
            return "Student ID can not be blank";
        }
        else
        {
            return "";
        }
    }

        function ValidateEmail($email): string
    {
        if(!trim($email))
        {
            return "Email can not be blank";
        }
        else
        {
            return "";
        }
    }

    function ValidateFirstName($firstName): string
    {
        if(!trim($firstName))
        {
            return "Name can not be blank";
        }
        else
        {
            return "";
        }
    }

    function ValidateLastName($lastName): string
    {
        if(!trim($lastName))
        {
            return "Last Name can not be blank";
        }
        else
        {
            return "";
        }
    }

    
    function ValidatePassword($Password): string
    {
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/";
        if(!trim($Password))
        {
            return "Password can not be blank";
        }
        elseif(!preg_match($regex, $Password))
        {
            return "Password must be at least 6 characters long, contains at least one upper case, one lowercase and one digit";
        }
        else
        {
            return "";
        }
    }

    function ValidatePasswordAgain($passwordAgain, $Password): string
    {
        if($passwordAgain != $Password)
        {
            return "Password does not match";
        }
        else
        {
            return "";
        }
    }
?>
