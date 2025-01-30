<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en" style="position: relative; min-height: 100%;">
<head>
	<title>Student Portal</title>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.6/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="Common/css/Site.css">
</head>
<body style="padding-top: 50px; margin-bottom: 60px;">
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
      <div class="container-fluid">
        
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li class="active"><a href="Index.php">Home</a></li>
            <li><a href="CourseSelection.php">Course Selection</a></li>
            <li><a href="CurrentRegistration.php">Enrolled Courses</a></li>

              <?php
                if(isset($_SESSION["login"])){
                    print "<li><a href='Logout.php'>Log out</a></li>";
                }
                else{
                    print "<li><a href='Login.php'>Log in</a></li>";
                }
              ?>
          </ul>
        </div>
      </div>  
    </nav>
