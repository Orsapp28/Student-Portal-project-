<?php
    error_reporting(E_ALL ^ E_NOTICE); //specify All errors and warnings are displayed
    session_start();
    extract($_POST);
    if(!isset($_SESSION["login"])){
        header("Location: Login.php");
        exit();
    }

    //connect to MySQL DB
    include("db.php");
    $pdo = connect();

    //Before you use any session, you always have to check if that session has value or not!
    $firstName = $_SESSION["firstName"] ?? "";
    $id = $_SESSION["id"] ?? "";

    //get student Name from DB
    $sqlName = "SELECT firstName FROM Student WHERE firstName = '$firstName'";
    $nameSet = $pdo -> query($sqlName);
    $row = $nameSet -> fetch(PDO::FETCH_ASSOC);
    if($row){
        $firstName = $row['Name'];
    }


    if(isset($confirmDelete)){
        $sqlDelete = "DELETE FROM Registration WHERE StudentId = :StudentId AND SemesterCode = :SemesterCode AND CourseCode = :CourseCode;";
        $deleteStmt = $pdo -> prepare($sqlDelete);

        foreach ($checkbox as $name => $value) {
            $deleteStmt->execute(['StudentId' => $id, 'SemesterCode' => $value, 'CourseCode' => $name]);
        }

        header("Location: CurrentRegistration.php");
    }

    if(isset($clear)){
        $checkbox = "";
    }

    include("Header.php");
    print <<<HTML
        <div class="container">
            <h1>Enrolled Courses</h1>
            <p>Hello <span style='font-weight: bold;'>$firstName</span> (not you? change user <a href="Login.php">here</a>), These are your current Enrolled Courses</p>
            <form action="CurrentRegistration.php" method="post">
                <table class="table">
                    <tr>
                        <th>Year</th>
                        <th>Term</th>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th></th>
                        <th>Hours</th>
                        <th>Select</th>
                    </tr>
    HTML;

    $semesterArr = [];
    $sqlSemesterCode = "SELECT r.SemesterCode
                        FROM registration r
                        WHERE r.StudentId = '$id'
                        GROUP BY r.SemesterCode;";
    $semesterCodeSet = $pdo -> query($sqlSemesterCode);
    foreach($semesterCodeSet as $row){
        $semesterArr[] = $row['SemesterCode'];
    }

    foreach($semesterArr as $s){
        $totalHours = 0;
        $sqlRegistrations = "SELECT s.Year, s.Term, r.CourseCode, c.Title, c.WeeklyHours, r.SemesterCode
                             FROM registration r INNER JOIN Course c ON r.CourseCode = c.CourseCode
                                                 INNER JOIN Semester s ON r.SemesterCode = s.SemesterCode
                             WHERE r.StudentId = '$id' AND r.SemesterCode = '$s';";
        $registrationsSet = $pdo -> query($sqlRegistrations);
        foreach($registrationsSet as $row){
            print <<<table_body
            <tr>
                <td>{$row['Year']}</td>
                <td>{$row['Term']}</td>
                <td>{$row['CourseCode']}</td>
                <td>{$row['Title']}</td>
                <td></td>
                <td>{$row['WeeklyHours']}</td>
                <td><input type="checkbox" name="checkbox[{$row['CourseCode']}]" value="{$row['SemesterCode']}"></td>
            </tr>
            table_body;
            $totalHours += $row['WeeklyHours'];
        }
        echo "<tr><td></td><td></td><td></td><td></td><td style='font-weight: bold;'>Total Weekly Hours</td><td style='font-weight: bold;'>$totalHours</td><td></td></tr>";
    }

    print <<<HTML
                </table>
                <input type="button" name="delete" id="delete" value="Delete Selected" class="btn btn-primary">
                <input type="submit" name="clear" value="Clear" class="btn btn-primary">
                <input type="submit" name="confirmDelete" id="confirmDelete" hidden>
            </form>
        </div>

        <script>
            document.getElementById("delete").addEventListener("click", function(){
                let text = "The selected registrations will be deleted!"
                if(confirm(text) === true){
                    document.getElementById("confirmDelete").click()
                }
            })
        </script>
    HTML;

?>