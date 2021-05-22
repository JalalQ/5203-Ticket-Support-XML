<?php

//This forms displays error message if any unauthorized user visit a webpage they are not authorized to visit.

session_start();

$errorMsg = "";

if (isset($_SESSION['status'])) {
    switch ($_SESSION['status']) {
        case "admin":
            $errorMsg = "You are currently logged in as an admin, and hence can not access
                    client web page. Visit the <a href=\"./index.php\">Home</a> page to
                    log out, and log in as a client user.";
            break;
        case "client":
            $errorMsg = "You are currently logged in as an client, and hence can not access
                    admin web page. Visit the <a href=\"./index.php\">Home</a> page to
                    log out, and log in as a admin user.";
            break;
    }
}
else {
    $errorMsg = "You are currently not logged in. Visit <a href=\"./index.php\">Home</a> page to log in.";
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'library/head.php'; ?>

<body>
<?php include 'library/header.php'; ?>

<div class="page-wrapper">
    <main>
        <h1>Error</h1>
        <p class='validation'>
            <?= $errorMsg ?>
        </p>
    </main>
</div>
</body>
</html>
