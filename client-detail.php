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

//whenever the user wishes to see detail about any particular ticket.
//form is on the client-home page.
if (isset($_POST['detailTicket'])) {
    // minus one, as array index starts from 0.
    $id = $_POST['id']-1;
}

//WRITE OPERATIONS
//when the user submits a reply message. The submitted values in the form are fetched, and then the message
//is added in the xml file. Once it has been added in the XML file, through the read and display operation
//it is then displayed.
if (isset($_POST['submitReply'])) {
    $id = $_POST['id'];

    $postedMsg = $_POST['message'];

    $count= $root->childNodes[$id]->getElementsByTagName("message")->length;

    $newMessage = $doc->createElement("message");

    //https://www.php.net/manual/en/function.date.php
    $dtz = new DateTimeZone("America/Toronto");
    $dt = new DateTime("now", $dtz);

    $newTimeMsg = $doc->createElement("dateTimeOfMessage", $dt->format("Y-m-d") . "T" . $dt->format("H:i:s"));
    $newContent = $doc->createElement("messageContent", $postedMsg);
    $newMessage->setAttribute("authorUsername", $_SESSION['valid_user']);

    $newMessage->appendChild($newTimeMsg);
    $newMessage->appendChild($newContent);

    $root->childNodes[$id]->getElementsByTagName("messages")->item(0)->appendChild($newMessage);

    //save the XML document. The XML file is validated using a schema.
    $doc->save("XML/ticket.xml");
}


// DOM READ OPERATION
// The following information is fetched and displayed on the page.
$issueElement = $doc->getElementsByTagName("issue");
$subjectElement = $doc->getElementsByTagName("subject");
$iDesElement = $doc->getElementsByTagName("issueDescription");
$openDateElement = $doc->getElementsByTagName("dateTimeOfIssue");

$status = $root->childNodes[$id]->getAttribute('status');
$creator = $root->childNodes[$id]->getAttribute('creatorUserName');
$category = $issueElement->item($id)->getAttribute('category');
$urgency = $issueElement->item($id)->getAttribute('urgent');

$subject = $subjectElement->item($id)->nodeValue;
$issueDes = $iDesElement->item($id)->nodeValue;

$openDate = substr($openDateElement->item($id)->nodeValue, 0, 10);
$openDate .= " " . substr($openDateElement->item($id)->nodeValue, 11, -3);

$messages = $root->childNodes[$id]->getElementsByTagName("message");
$messageCor = "";

//the following loop prints out the messages associated with a particular ticket.
foreach ($messages as $message) {

    $messageCor .= "<article class=\"message-div\"> <p class=\"message-head\"> <span class=\"values\">";
    $messageCor .= $message->getAttribute('authorUsername') . "</span>, on ";
    $messageCor .= "<time>" . $message->getElementsByTagName("dateTimeOfMessage")->item(0)->nodeValue;
    $messageCor .= "</time> wrote: </p> <p class=\"message\">";
    $messageCor .= $message->getElementsByTagName("messageContent")->item(0)->nodeValue . "</p></article>";

}

//If it is a newly created ticket with no messages.
if ($messages->length==0) {
    $messageCor .= "<p>There are currently no messages for this ticket.</p>";
}


?>

<!DOCTYPE html>
<html lang="en">
<?php include 'library/head.php'; ?>

<body>
<?php include 'library/header.php'; ?>

<div class="page-wrapper">
    <main>
        <a href="./client-home.php" id="back-link"><< Back to List of Tickets</a>

        <section class="msg-coll">

            <h1 class="hdr">Detail for Ticket ID # <?= $id+1; ?></h1>

            <h2>Status: <span class='status'><?= $status; ?></span></h2>

            <h3>Created by: <span class="values"><?= $creator; ?></span></h3>
            <h3>Category: <span class="values"><?= $category; ?></span></h3>
            <h3>Urgency: <span class="values"><?= $urgency; ?></span></h3>

            <article class="message-div">
                <p class="message-head">
                    <span class="values"><?= $subject; ?>. </span>
                    Submitted on: <time><?= $openDate; ?></time></p>
                <p class="message"><?= $issueDes; ?></p>
            </article>
        </section>

        <!--Displays all the messages for the ticket-->
        <section class="msg-coll">
            <h2 class="hdr">Messages Correspondence</h2>
            <?php print $messageCor; ?>
        </section>

        <!-- Form for the user to post a reply -->
        <form method="post" action="client-detail.php">

            <fieldset>
                <h2 class="hdr">Submit a New Message for this Ticket</h2>
                <div class="fields">
                    <input type="hidden" name="id" value=<?= $id;?> />
                    <p class="row">
                        <label for="message">Message:</label>
                        <input type="text" id="message" name="message" required="required"
                               class="field-msg"/>
                    </p>
                    <p class="row">
                        <input type="submit" class="btn btn-success" name="submitReply" value="Submit" />
                    </p>

                </div>
            </fieldset>

        </form>

    </main>

</div>
<script type="text/javascript" src="script/ticket.js"></script>
</body>
</html>