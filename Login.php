<?php
    error_reporting(E_ALL ^ E_NOTICE); //specify All errors and warnings are displayed
    session_start();
    extract($_POST);
    $emailErr = "";
    $passwordErr = "";
    $logInErr = "";

    //connect to MySQL DB
    include("db.php");
    $pdo = connect();

    if(isset($submit)){
        $emailErr = ValidateEmail($email);
        $passwordErr = ValidatePassword($Password);

        if(!$emailErr && !$passwordErr){

            //hash password
            $hashedPassword = hash("sha256", $Password);

            //Method 2: prevent SQL-injection attack - PDO prepared statement
            $sqlLogin = "SELECT email FROM Student WHERE email = :email AND Password = :hashedPassword";
            $preparedStatement = $pdo -> prepare($sqlLogin);
            $preparedStatement -> execute(['email'=>$email, 'hashedPassword'=>$hashedPassword]);
            $row = $preparedStatement -> fetch(PDO::FETCH_ASSOC);
            if(!$row){
                $logInErr = "Incorrect Email Address and/or Password!";
            }
            else{
                //store data in session
                $_SESSION["email"] = $email;
                $_SESSION["Password"] = $Password;
                $_SESSION["login"] = "true";
                header("Location: CourseSelection.php");
            }
        }
    }
    else{
        //if the data has been stored in the session, display the data on the page when the user enters this page
        $email = $_SESSION["email"] ?? "";
        $Password = $_SESSION["Password"] ?? "";
    }

    if(isset($clear)) {
        $email = '';
        $Password = '';
    }

    include("Header.php");
    print <<<HTML
    <div class="container">
        <h1>Log In</h1>
        <p>Not yet a member? <a href="NewUser.php">sign up</a> here</p>
        <form action="Login.php" method="post">
            <span class="errorMsg">$logInErr</span>
            <div class="row form-group form-inline">
                <label for="email" class="col-md-2">Email: </label>
                <input type="text" id="email" name="email" class="form-control col-md-3" value="$email">
                <span class="errorMsg">$emailErr</span>
            </div>
            <div class="row form-group form-inline">
                <label for="Password" class="col-md-2">Password: </label>
                <input type="password" id="Password" name="Password" class="form-control col-md-3" value="$Password">
                <span class="errorMsg">$passwordErr</span>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            <button type="submit" name="clear" class="btn btn-primary">Clear</button>
        </form>
        </div>
    </div>
    HTML;


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

    function ValidatePassword($Password): string
    {
        if(!trim($Password))
        {
            return "Password can not be blank";
        }
        else
        {
            return "";
        }
    }
?>
