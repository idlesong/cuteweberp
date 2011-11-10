<?php

/* $Revision: 1.33 $ */

include('includes/DefineReceiptClass.php');

$PageSecurity = 3;
include('includes/session.inc');

$title = _('Receipt Entry');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

$msg='';

if (isset($_SESSION['msg_NewReceipt'])){
	unset($_SESSION['ReceiptBatch']->Items);
	unset($_SESSION['ReceiptBatch']);
	unset($_SESSION['CustomerRecord']);
}

/*
* always display a customer receipt
*/
echo "<script>
function openSubpage(subpageURL){

window.open(subpageURL, 'location=no', 'scrollbars=yes', 'resizable=yes');

//window.alert(subpageURL);

}
</script>";


echo '<p class=page_title_text>Enter receipts</p>';
echo '<p align=right>';
echo "<input onclick=openSubpage('/weberp/h5SearchCustomers.php'); type='button' value='Customer' />";
echo '</p>';
$dispLocation = $_SESSION['PO']->Location;
$dispDelAddrFirst = $_SESSION['PO']->DelAdd1.' '.$_SESSION['PO']->DelAdd2.' '.$_SESSION['PO']->DelAdd3;
$dispDelAddrLast  = $_SESSION['PO']->DelAdd4.' '.$_SESSION['PO']->DelAdd5.' '.$_SESSION['PO']->DelAdd6;
$dispContact = $_SESSION['PO']->Initiator;
$dispRequistion = $_SESSION['PO']->RequisitionNo;
$dispExRate = $_SESSION['PO']->ExRate;
$dispComments = $_SESSION['PO']->Comments;

//$dispSupplierID = $_SESSION['PO']->SupplierID;	
$dispSupplierID = $_SESSION['defaultSupplierID'];	
$dispSupplierName = $_SESSION['PO']->SupplierName;

//echo $_SESSION['PO']->SupplierID;	

//<TD><INPUT TYPE=text NAME=DelAdd1 SIZE=41 MAXLENGTH=40 Value='" . $_POST['DelAdd1'] . "'></TD>
//$dispSupplierID='Silan';

echo "<table class='commontable' > 
<caption> Wafer PO header</caption>
<tr><td>PO NO.</td><td>PO11-011-001</td><td>Quotation NO.</td><td>QO011-003-022</td></tr>
<tr><td>Buyer:</td><td></td><td>Vender:</td><td>".$dispSupplierID."</td></tr>
<tr><td>Deliver To:</td><td>". $dispLocation ."</td><td>Vender:</td><td>".$dispSupplierName."</td></tr>
<tr><td>Address1-3:</td><td>". $dispDelAddrFirst ."</td><td>Address1-3:<td></td></tr>
<tr><td>Address4-6:</td><td>". $dispDelAddrLast."</td><td>Address4-6:<td></td></tr>
<tr><td>Contact:</td><td>".$dispContact."</td><td>Contact:<td></td></tr>
<tr><td>Telephone:</td><td>". $dispContact ."</td><td>Telephone:<td></td></tr>
<tr><td>Email:</td><td></td><td>Email:<td></td></tr>
</table>";

?>