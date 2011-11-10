<?php
/* $Revision: 1.17 $ */
/* R&OS PHP-pdf class code to head up a new page */

if (!$FirstPage){ /* only initiate a new page if its not the first */
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin;

$pdf->addJpegFromFile($_SESSION['LogoFile'],$Page_Width/2 -80,$YPos-40,0,60);
//$FontSize =15;
$FontSize =24;
if ($InvOrCredit=='Invoice') {
        //$pdf->addText($Page_Width - 200, $YPos, $FontSize, _('I N V O I C E') . ' ');
        $pdf->addText($Page_Width - 500, $YPos-100, $FontSize, _('I N V O I C E') . ' ');
} else if($InvOrCredit=='Packinglist'){   
	 $pdf->addText($Page_Width - 550, $YPos-100, $FontSize, _('P A C K I N G   L I S T') . ' ');    
} else {
	$pdf->addText($Page_Width - 350, $YPos-100, $FontSize, _('CREDIT NOTE') . ' ');
}

$XPos = $Page_Width - 265;
$YPos -= 111;
$pdf->line($Page_Width - 500, $Page_Height - $Top_Margin - 40,$Page_Width - $Left_Margin+20, $Page_Height - $Top_Margin - 40);
/*draw a nice curved corner box around the billing details */
/*from the top right */
//$pdf->partEllipse($XPos+225,$YPos+93,0,90,10,10);
/*line to the top left */
//$pdf->line($XPos+225, $YPos+103,$XPos, $YPos+103);
/*Dow top left corner */
//$pdf->partEllipse($XPos, $YPos+93,90,180,10,10);
/*Do a line to the bottom left corner */
//$pdf->line($XPos-10, $YPos+93,$XPos-10, $YPos);
/*Now do the bottom left corner 180 - 270 coming back west*/
//$pdf->partEllipse($XPos, $YPos,180,270,10,10);
/*Now a line to the bottom right */
//$pdf->line($XPos, $YPos-10,$XPos+225, $YPos-10);
/*Now do the bottom right corner */
//$pdf->partEllipse($XPos+225, $YPos,270,360,10,10);
/*Finally join up to the top right corner where started */
//$pdf->line($XPos+235, $YPos,$XPos+235, $YPos+93);

/*draw a nice curved corner box around the billing details */
/*from the top right */
$pdf->partEllipse($XPos+225,$YPos+13,0,90,10,10);
/*line to the top left */
$pdf->line($XPos+225, $YPos+23,$XPos, $YPos+23);
/*Dow top left corner */
$pdf->partEllipse($XPos, $YPos+13,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($XPos-10, $YPos+13,$XPos-10, $YPos);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($XPos, $YPos,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($XPos, $YPos-10,$XPos+225, $YPos-10);
/*Now do the bottom right corner */
$pdf->partEllipse($XPos+225, $YPos,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($XPos+235, $YPos,$XPos+235, $YPos+13);

$YPos = $Page_Height - $Top_Margin - 10;

$FontSize = 10;

//$pdf->addText($Page_Width-268, $YPos-13, $FontSize, _('Number'));
//$pdf->addText($Page_Width-180, $YPos-13, $FontSize, $FromTransNo);
//$pdf->addText($Page_Width-268, $YPos-26, $FontSize, _('Customer Code'));
//$pdf->addText($Page_Width-180, $YPos-26, $FontSize, $myrow['debtorno'] . ' ' . _('Branch') . ' ' . $myrow['branchcode']);
if ($InvOrCredit=='Packinglist') {
	$pdf->addText($Page_Width-268, $YPos-89, $FontSize, _('Delivery Date :'));
	$pdf->addText($Page_Width-180, $YPos-89, $FontSize, ConvertSQLDate($myrow['trandate']));
} else {
	$pdf->addText($Page_Width-268, $YPos-89, $FontSize, _('Date :'));
	$pdf->addText($Page_Width-180, $YPos-89, $FontSize, ConvertSQLDate($myrow['trandate']));	
}	


if ($InvOrCredit=='Invoice' || $InvOrCredit=='Packinglist') {

	$pdf->addText($Page_Width-268, $YPos-98, $FontSize, _('Order No :'));
	$pdf->addText($Page_Width-180, $YPos-98, $FontSize, 'SO'.$FromTransNo);
	//$pdf->addText($Page_Width-268, $YPos-65, $FontSize, _('Order Date'));
	//$pdf->addText($Page_Width-180, $YPos-65, $FontSize, ConvertSQLDate($myrow['orddate']));
	//$pdf->addText($Page_Width-268, $YPos-78, $FontSize, _('Dispatch Detail'));
	//$pdf->addText($Page_Width-180, $YPos-78, $FontSize, $myrow['shippername'] . '-' . $myrow['consignment']);
	//$pdf->addText($Page_Width-268, $YPos-91, $FontSize, _('Dispatched From'));
	//$pdf->addText($Page_Width-180, $YPos-91, $FontSize, $myrow['locationname']);
}


$pdf->addText($Page_Width-268, $YPos-107, $FontSize, _('Page :'));
$pdf->addText($Page_Width-180, $YPos-107, $FontSize, $PageNumber);

/*End of the text in the right side box */

/*Now print out the company name and address in the middle under the logo */
//$XPos = $Page_Width/2 -90;
//$YPos = $Page_Height - $Top_Margin-60;
$XPos = $Page_Width - 265;
$YPos = $Page_Height - $Top_Margin - 20;
//$pdf->addText($XPos, $YPos, $FontSize, $_SESSION['CompanyRecord']['coyname']);

$FontSize=8;
//$pdf->addText($XPos, $YPos-10, $FontSize, $_SESSION['TaxAuthorityReferenceName'] . ': ' . $_SESSION['CompanyRecord']['gstno']);
//$pdf->addText($XPos, $YPos-19,$FontSize, $_SESSION['CompanyRecord']['regoffice1']);
//$pdf->addText($XPos, $YPos-28,$FontSize, $_SESSION['CompanyRecord']['regoffice2']);
$pdf->addText($XPos, $YPos-32,$FontSize+2, _('ADDR') . ':' . $_SESSION['CompanyRecord']['regoffice1'] . ',' .$_SESSION['CompanyRecord']['regoffice2'] . ',' .$_SESSION['CompanyRecord']['regoffice3'] . ',' . $_SESSION['CompanyRecord']['regoffice4'] . ',' . $_SESSION['CompanyRecord']['regoffice5'] . ',' .$_SESSION['CompanyRecord']['regoffice6']);
//$pdf->addText($XPos, $YPos-46, $FontSize, $_SESSION['CompanyRecord']['regoffice6']);
$pdf->addText($XPos, $YPos-43, $FontSize+2, _('TEL') . ':' . $_SESSION['CompanyRecord']['telephone'] . ' ' . _('FAX') . ': ' . $_SESSION['CompanyRecord']['fax']);
$pdf->addText($XPos, $YPos-53, $FontSize+2, _('Email') . ': ' . $_SESSION['CompanyRecord']['email']);

/*Now the customer charged to details top left */

$XPos = $Left_Margin;
$YPos = $Page_Height - $Top_Margin;

$FontSize=10;

$pdf->addText($XPos, $YPos, $FontSize, _('BILL TO') . ':');
$XPos +=60;

if ($myrow['invaddrbranch']==0){
	$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
	$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['address1'].' '.$myrow['address2']);
	//$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['address2']); //next line:$YPos-42
	//$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['address3'] . ' ' . $myrow['address4']);
} else {
	$pdf->addText($XPos, $YPos, $FontSize, $myrow['name']);
	$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['brpostaddr1'].' '.$myrow['brpostaddr2']);
	//$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['brpostaddr2']); //next line:$YPos-42
	//$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['brpostaddr3'] . ' ' . $myrow['brpostaddr4'] . ' ' . $myrow['brpostaddr5'] . ' ' . $myrow['brpostaddr6']);
}


$XPos -=60;
$YPos -=($line_height*2);

if ($InvOrCredit=='Invoice'|| $InvOrCredit=='Packinglist') {

	$pdf->addText($XPos, $YPos, $FontSize, _('SHIP TO') . ':');
	$XPos +=60;
// Before trying to call htmlspecialchars_decode, check that its supported, if not substitute a compatible version
if (!function_exists('htmlspecialchars_decode')) {
        function htmlspecialchars_decode($str) {
                $trans = get_html_translation_table(HTML_SPECIALCHARS);

                $decode = ARRAY();
                foreach ($trans AS $char=>$entity) {
                        $decode[$entity] = $char;
                }

                $str = strtr($str, $decode);

                return $str;
        }
}
	$pdf->addText($XPos, $YPos, $FontSize, htmlspecialchars_decode($myrow['deliverto']));
	$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['deladd1'].' '.$myrow['deladd2']);
//	$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['deladd2']); // next:$YPos-42
	$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd5']);
	
	$pdf->addText($XPos-60, $YPos-42, $FontSize, 'ATTN:'.$myrow['contactname']);
	$pdf->addText($XPos-60, $YPos-56, $FontSize, 'TEL:'.$myrow['phoneno']);
	$pdf->addText($XPos-60, $YPos-70, $FontSize, 'FAX:'.$myrow['faxno']);	 
	
	$XPos -=80;
}
if ($InvOrCredit=='Credit'){
/* then its a credit note */

	$pdf->addText($XPos, $YPos, $FontSize, _('Charge Branch') . ':');
	$XPos +=80;
	$pdf->addText($XPos, $YPos, $FontSize, $myrow['brname']);
	$pdf->addText($XPos, $YPos-14, $FontSize, $myrow['braddress1']);
	$pdf->addText($XPos, $YPos-28, $FontSize, $myrow['braddress2']);
	$pdf->addText($XPos, $YPos-42, $FontSize, $myrow['braddress3'] . ' ' . $myrow['braddress4'] . ' ' . $myrow['braddress5'] . ' ' . $myrow['braddress6']);
	$XPos -=80;
}

$XPos = $Left_Margin;

$YPos = $Page_Height - $Top_Margin - 80;
/*draw a line under the company address and charge to address
$pdf->line($XPos, $YPos,$Right_Margin, $YPos); */

$XPos = $Page_Width/2;

$XPos = $Left_Margin;
$YPos -= ($line_height*2);

$pdf->addText($Left_Margin, $YPos-8, $FontSize, _('All amounts stated in') . ' - ' . $myrow['currcode']);

/*draw a box with nice round corner for entering line items */
/*90 degree arc at top right of box 0 degrees starts a bottom */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+400,0,90,10,10);
/*line to the top left */
$pdf->line($Page_Width-$Right_Margin-10, $Bottom_Margin+410,$Left_Margin+10, $Bottom_Margin+410);
/*Dow top left corner */
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+400,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($Left_Margin, $Bottom_Margin+400,$Left_Margin, $Bottom_Margin+10);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+10,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($Left_Margin+10, $Bottom_Margin,$Page_Width-$Right_Margin-10, $Bottom_Margin);
/*Now do the bottom right corner */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+10,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+10,$Page_Width-$Right_Margin, $Bottom_Margin+400);


$YPos -= ($line_height*2);
/*Set up headings */
$FontSize=10;
$pdf->addText($Left_Margin + 2, $YPos, $FontSize, _('Sales Invoice # ') . ':');
$pdf->addText($Left_Margin+90, $YPos, $FontSize, 'SO'.$FromTransNo);
//$pdf->addText($Left_Margin+90, $YPos, $FontSize, $myrow['taxref']);


/*Print a vertical line */
$pdf->line($Left_Margin+248, $YPos+$line_height+3,$Left_Margin+248, $YPos - 8);

$pdf->addText($Left_Margin + 252, $YPos, $FontSize, _('Customer PO#') . ':');
//$pdf->addText($Left_Margin+330, $YPos, $FontSize, $myrow['customerref']);

/*Print a vertical line */
$pdf->line($Left_Margin+450, $YPos+$line_height+3,$Left_Margin+450,$YPos-8);

$pdf->addText($Left_Margin+453+180, $YPos, $FontSize, _('Sales Person') . ':');
$pdf->addText($Left_Margin+530+180, $YPos, $FontSize, $myrow['salesmanname']);

$YPos -= 8;
/*draw a line */
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);

$YPos -= 12;

$TopOfColHeadings = $YPos;
if ($InvOrCredit=='Packinglist') {
	$pdf->addText($Left_Margin+5, $YPos, $FontSize, _('Item Code'));
	$pdf->addText($Left_Margin+100, $YPos, $FontSize, _('Description'));
	$pdf->addText($Left_Margin+382, $YPos, $FontSize, _('QTY.(PCS)'));
	$pdf->addText($Left_Margin+455, $YPos, $FontSize, _('CARTONS(CTNS)'));
	$pdf->addText($Left_Margin+545, $YPos, $FontSize, _('CARTON #'));
	$pdf->addText($Left_Margin+595, $YPos, $FontSize, _('G.W.(KG)'));
	$pdf->addText($Left_Margin+640, $YPos, $FontSize, _('N.W.(KG)'));	
	$pdf->addText($Left_Margin+690, $YPos, $FontSize, _('VOLUME(CBM)'));
} else {
	$pdf->addText($Left_Margin+5, $YPos, $FontSize, _('Item Code'));
	$pdf->addText($Left_Margin+100, $YPos, $FontSize, _('Description'));
	$pdf->addText($Left_Margin+382, $YPos, $FontSize, _('Unit Price'));
	$pdf->addText($Left_Margin+485, $YPos, $FontSize, _('Quantity'));
	$pdf->addText($Left_Margin+555, $YPos, $FontSize, _('UOM'));
	$pdf->addText($Left_Margin+595, $YPos, $FontSize, _('Discount'));
	$pdf->addText($Left_Margin+690, $YPos, $FontSize, _('Extended Price'));	
}

$YPos-=8;

/*draw a line */
$pdf->line($XPos, $YPos,$Page_Width-$Right_Margin, $YPos);

$YPos -= ($line_height);

?>
