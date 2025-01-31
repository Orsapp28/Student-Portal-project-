<?php
    error_reporting(E_ALL ^ E_NOTICE); //specify All errors and warnings are displayed
    session_start();
    extract($_POST);
    $idErr = "";
    $passwordErr = "";
    $logInErr = "";

    //connect to MySQL DB
    include("db.php");
    $pdo = connect();

    if(isset($submit)){
        $idErr = ValidateEmail($id);
        $passwordErr = ValidatePassword($Password);

        if(!$idErr && !$passwordErr){

            //hash password
            $hashedPassword = hash("sha256", $Password);

            
            $sqlLogin = "SELECT StudentId FROM Student WHERE StudentId = :id AND Password = :hashedPassword";
            $preparedStatement = $pdo -> prepare($sqlLogin);
            $preparedStatement -> execute(['id'=>$id, 'hashedPassword'=>$hashedPassword]);
            $row = $preparedStatement -> fetch(PDO::FETCH_ASSOC);
            if(!$row){
                $logInErr = "Incorrect Student ID Address and/or Password!";
            }
            else{
                //store data in session
                $_SESSION["id"] = $id;
                $_SESSION["Password"] = $Password;
                $_SESSION["login"] = "true";
                header("Location: CourseSelection.php");
            }
        }
    }
    else{
        //if the data has been stored in the session, display the data on the page when the user enters this page
        $id = $_SESSION["id"] ?? "";
        $Password = $_SESSION["Password"] ?? "";
    }

    if(isset($clear)) {
        $id = '';
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
                <label for="id" class="col-md-2">Student ID: </label>
                <input type="text" id="id" name="id" class="form-control col-md-3" value="$id">
                <span class="errorMsg">$idErr</span>
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


    function ValidateEmail($id): string
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
