<?php 

header('Access-Control-Allow-Origin: *'); 
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

require_once dirname ( dirname ( __FILE__ ) ) . "/bl/models/pDesk_tickets.php";
require_once dirname ( dirname ( __FILE__ ) ) . "/classes/globalFunctions.php";

$strMethod = "";
$strID = "";

if ($_POST['method'] == ""){
    $strMethod = $formJson["method"];
    $strID = $_POST['ID'];
}
else {
    $strMethod = $_POST['method'];
    $strID = $_POST['ID'];
}

if ($strMethod == "" && $_GET['method'] != "" ) {
    $strMethod  = $_GET['method'];
    $strID = $_GET['ID'];
}

$tickets = new pDesk_tickets($strID);
$globalFunctions = new globalFunctions();

switch($strMethod) {
    case "ticketSave":
        $tickets->parentId = $_POST["parentTicketID"];
        $tickets->subject = "";
        $tickets->description = $_POST["ticketResponse"];
        $tickets->status = $_POST["ticketStatus"];    
        $tickets->organizationID = $globalFunctions->getOrganizationID();
        $tickets->createdBy = $globalFunctions->getUserID();
        $tickets->assignUserID = $_POST["ticketAssign"]; 
        $tickets->createdDate = globalFunctions::convertStringtoDate(globalFunctions::getDateOrTime("dateTime"), "dateTime");
        $strID = $tickets->save();

        if ($_POST["parentTicketID"] != 0) {
            $ticketsStatus = new pDesk_tickets( $_POST["parentTicketID"] );
            $ticketsStatus->status = $_POST["ticketStatus"];
            $strID = $ticketsStatus->save();
        }
        
        echo $strID;
        break;
    case "getTicketDetails":
        $ticketID  = $_GET['ticketID'];
        $result = $tickets->getTicketDetails( $ticketID );
        $result = $tickets->toJson;
        echo $result;
        break;
    case "ticketDelete":
        $ticketID  = $_GET['ticketID'];
        $userID = $globalFunctions->getUserID();
        $result = $tickets->deleteTicketDetails( $ticketID, $userID );
        $result = $tickets->toJson;
        echo $result;
        break;        
}


?>