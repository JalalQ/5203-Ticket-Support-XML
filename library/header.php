<?php

$panelLink = "";

if (isset($_SESSION['status'])) {

    if (($_SESSION['status']) == "admin") {
        $panelLink .= "<li><a href='./admin-home.php'>Admin Panel</a></li>";
    } elseif (($_SESSION['status']) == "client") {
        $panelLink .= "<li><a href='./client-home.php'>Client Panel</a></li>";
    }
}

?>

<header id="header">
    <div class="page-wrapper">

        <!-- MAIN NAVIGATION-->
        <nav id="main-navigation">
            <h3 class="hidden">Main navigation</h3>
            <ul class="menu">
                <li><a href="./index.php">Home</a></li>
                <?= $panelLink; ?>
            </ul>
        </nav>

    </div>
</header>
