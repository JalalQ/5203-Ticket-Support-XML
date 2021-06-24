<?php

$doc = new DOMDocument();

//make sure the XML is nicely formatted
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

$doc->load("XML/ticket.xml");

$root = $doc->documentElement;
session_start();

//To access this page, the user must be logged in as a client, otherwise
//the user would be directed to an error page, and asked to log in.
if ($_SESSION['status'] != "client") {
    header("Location: ./error.php");
}

// After a client user submits a new ticket. The values submitted in the form are fetched
// and a new ticket is created and the ticket.xml file updated.
if (isset($_POST['newTicket'])) {
    echo $count= $root->getElementsByTagName("ticket")->length;
    $ticket = $doc->createElement("ticket");
    $ticket->setAttribute("id", ($root->getElementsByTagName("ticket")->length+1));
    $ticket->setAttribute("creatorUserName", $_SESSION['valid_user']);
    $ticket->setAttribute("status", "Open");

    $issue = $doc->createElement("issue");
    $issue->setAttribute("category", $_POST['category']);
    $issue->setAttribute("urgent", $_POST['urgency']);

    $subject = $doc->createElement("subject", $_POST['subject']);

    $description = $doc->createElement("issueDescription", $_POST['description']);

    //https://www.php.net/manual/en/function.date.php
    $dtz = new DateTimeZone("America/Toronto");
    $dt = new DateTime("now", $dtz);
    $newTimeMsg = $doc->createElement("dateTimeOfIssue", $dt->format("Y-m-d") . "T" . $dt->format("H:i:s"));

    $issue->appendChild($subject);
    $issue->appendChild($description);
    $issue->appendChild($newTimeMsg);

    $ticket->appendChild($issue);
	
	//Add messages elements, so whenever a new message is created, it would be appended inside it.
	$messages = $doc->createElement("messages");
	$ticket->appendChild($messages);
	
    $root->appendChild($ticket);

    $doc->save("XML/ticket.xml");

}

$openDateElement = $doc->getElementsByTagName("dateTimeOfIssue");

$trows = '';
$counter = 0;

//Tickets are listed if there exists tickets in the xml file.
if ($root->hasChildNodes()) {
    $children = $root->childNodes;

    foreach($children as $node) {

        // The "client" users can only see their own tickets, and not
        // the tickets of other clients.
        if ($node->getAttribute('creatorUserName') == $_SESSION['valid_user']) {
            $trows .= "<tr>";

            $trows .= "<td>" . $node->getAttribute('id') . "</td>";
            $trows .= "<td>" . substr($openDateElement->item($counter)->nodeValue, 0, 10) . "</td>";
            $trows .= "<td>" . $node->getAttribute('creatorUserName') . "</td>";
            $trows .= "<td class='status'>" . $node->getAttribute('status') . "</td>";

            $trows .= "<td><form action=\"client-detail.php\" method=\"post\">";
            $trows .= "<input type=\"hidden\" name=\"id\" value=" . $node->getAttribute('id') . " />";
            $trows .= "<input type=\"submit\" class=\"btn btn-success\" name=\"detailTicket\" value=\"View\"/>";
            $trows .= "</td></form>";

            $trows .= "</tr>";
        }

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

        <section>
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
        </section>

        <!-- Form to submit a new ticket -->
        <form method="post" action="client-home.php">

            <fieldset>
                <h2 class="hdr">Submit a New Ticket</h2>
                <div class="fields">
                    <p class="row">
                        <!-- Drop down menu for list of categorical data -->
                        <label for="category">Category:</label>
                        <select name="category" id="category">
                            <option value="battery">Battery</option>
                            <option value="internet">Internet</option>
                            <option value="software">Software</option>
                            <option value="printer">Printer</option>
                            <option value="screen">Screen</option>
                        </select>
                    </p>
                    <p class="row">
                        <label for="urgency">Urgent:</label>
                        <select name="urgency" id="urgency">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </p>
                    <p class="row">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required="required"
                               class="field-large"/>
                    </p>
                    <p class="row">
                        <label for="description">Description:</label>
                        <input type="text" id="description" name="description" required="required"
                               class="field-msg"/>
                    </p>
                    <p class="row">
                        <input type="submit" class="btn btn-success" name="newTicket" value="Submit"/>
                    </p>

                </div>
            </fieldset>

        </form>

    </main>

</div>
<script type="text/javascript" src="script/ticket.js"></script>
</body>
</html>