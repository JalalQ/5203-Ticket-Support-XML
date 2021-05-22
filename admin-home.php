<?php

$doc = new DOMDocument();

//make sure the XML is nicely formatted
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

$doc->load("XML/ticket.xml");

$root = $doc->documentElement;

session_start();

//To access this page, the user must be logged in as a admin, otherwise
//the user would be directed to an error page, and asked to log in.
if ($_SESSION['status'] != "admin") {
    header("Location: ./error.php");
}

$openDateElement = $doc->getElementsByTagName("dateTimeOfIssue");

$trows = '';
$counter = 0;

//for displaying all the tickets which have been created by any client users.
if ($root->hasChildNodes()) {
    $children = $root->childNodes;

    foreach($children as $node) {
        $trows .= "<tr>";

        $trows .= "<td>" . $node->getAttribute('id') . "</td>";
        $trows .= "<td>" . substr($openDateElement->item($counter)->nodeValue, 0, 10) . "</td>";
        $trows .= "<td>" . $node->getAttribute('creatorUserName') . "</td>";
        $trows .= "<td class='status'>" . $node->getAttribute('status') . "</td>";

        $trows .= "<td><form action=\"admin-detail.php\" method=\"post\">";
        $trows .= "<input type=\"hidden\" name=\"id\" value=" . $node->getAttribute('id') . " />";
        $trows .= "<input type=\"submit\" class=\"btn btn-success\" name=\"detailTicket\" value=\"View\"/>";
        $trows .= "</td></form>";

        $trows .= "</tr>";

        $counter++;
    }

}


?>

<!DOCTYPE html>
<html lang="en">
<?php include 'library/head.php'; ?>

    <body>
    <?php include 'library/header.php'; ?>

        <div class="page-wrapper">
            <main>
                <h1>Ticket Support System</h1>

                <table>
                    <tr id="header-row">
                        <th>Ticket ID</th>
                        <th>Date Opened</th>
                        <th>Created by</th>
                        <th>Status</th>
                        <th></th>
                    </tr>

                    <?php print $trows; ?>

                </table>

            </main>

        </div>
    <script type="text/javascript" src="script/ticket.js"></script>
    </body>
</html>