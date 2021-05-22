<?php

$doc = new DOMDocument();
$doc->load("XML/user.xml");

$root = $doc->documentElement;

$validation = "";
$loggedIn = false;

$welcomeMsg = "";
$logOutBtn = "";

session_start();

if(isset($_POST['logOut'])) {
    unset($_SESSION['valid_user']);
    unset($_SESSION['first_name']);
    unset($_SESSION['status']);
    session_destroy();
    $welcomeMsg = "";
    $logOutBtn = "";
}

//When the user clicks on the login button, it compares the username and password.
if(isset($_POST['logIn'])) {
    $username = $_POST['username'];

    //plain-text password is converted into md5 hash string.
    //https://www.php.net/manual/en/function.md5.php
    $password = md5($_POST['password']);

    $loginElement = $doc->getElementsByTagName("logInDetails");

    //assign session variable only if the current session is NOT ongoing.
    if (!isset($_SESSION['valid_user'])) {
        //linear search to evaluate whether it matches any log in record.
        foreach ($loginElement as $userDetail) {
            //echo $userDetail->getAttribute('userName');
            if (($username == $userDetail->getAttribute('userName')) and
                ($password == $userDetail->getAttribute('userPassword'))) {

                $_SESSION['valid_user'] = $username;
                $_SESSION['first_name'] = $userDetail->parentNode->getElementsByTagName("firstName")->item(0)->nodeValue;
                $loggedIn = true;

                if ($userDetail->getAttribute('userType') == "admin") {
                    $_SESSION['status'] = "admin";
                    header("Location: ./admin-home.php");
                } else {
                    $_SESSION['status'] = "client";
                    header("Location: ./client-home.php");
                }
            }
        }
    }

    //Login Form validation Feedback.
    if (($loggedIn==false) and (!isset($_SESSION['valid_user']))) {
        $validation .= "<p class='validation'>Invalid User Name and/ or Password.<br> 
                        Refer to XML/user.xml file for User Name and Password Information.</p>";

    }

}

//outputs message for a logged in user. Also provides form for logging out.
if (isset($_SESSION['valid_user'])) {
    $path = "./" . $_SESSION['status'] . "-home.php";
    $welcomeMsg .= "<p id='welcomMsg'> Welcome back " . $_SESSION['first_name'] .
                ". You are already logged in. <a href=\"" . $path . "\">Click here</a> to visit user panel.</p>";

    $logOutBtn .= "<form method=\"post\" action=\"index.php\" id='form-logout'>";
    $logOutBtn .= "<input type=\"submit\" class=\"btn btn-logout\" name=\"logOut\" value=\"Sign Out\"/></form>";
}


?>


<!DOCTYPE html>
<html lang="en">
<?php include 'library/head.php'; ?>

<body>
<?php include 'library/header.php'; ?>

<div class="page-wrapper">
    <main>
        <!--<h1>Ticket Support System</h1>-->
        <?= $logOutBtn; ?>
        <?= $welcomeMsg; ?>

        <!--user log in form.-->
        <form method="post" action="index.php">

            <fieldset id="fieldset-login">
                <h2 class="hdr">Welcome to Ticket Support System</h2>
                <div class="fields">
                    <p class="row">
                        <label for="username">User Name:</label>
                        <input type="text" id="username" name="username" class="field-large" required="required"
                               autofocus="autofocus"/>
                    </p>
                    <p class="row">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="field-large" required="required"/>
                    </p>
                    <p class="row">
                        <input type="submit" class="btn btn-success" id="btn-login" name="logIn" value="Sign In"/>
                    </p>
                    <?= $validation; ?>
                </div>
            </fieldset>

        </form>

    </main>

</div>
</body>
</html>
