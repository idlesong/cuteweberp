<?php
/* $Revision: 1.16 $ */
/*
	R & OS PHP-PDF class code to set up a new page
	a new page is implicit on the establishment of a new pdf object so
	only for subsequent pages
*/
// Order Layout: don't change these var
$YPosOrderHeader  = $Bottom_Margin+480;
$YPosOrderDetails = $Bottom_Margin+380;
$YPosOrderRemark = $Bottom_Margin+130;
$YPosConfirm = $Bottom_Margin+50;

if ($PageNumber>1){
	$pdf->newPage();
}

$YPos = $Page_Height - $Top_Margin - ($line_height*2);
$pdf->addJpegFromFile($_SESSION['LogoFile'],$Left_Margin,$YPos,0,60);

$FontSize=24;
$XPos = $Page_Width/2 + 120;
$YPos = $Page_Height - $Top_Margin - $line_height;
$pdf->addText($XPos,$YPos+$line_height,$FontSize, _('PURCHASE ORDER '));

$XPos = $Page_Width/2;
$YPos -= $line_height; 
if ($ViewingOnly!=0) {
	$FontSize=16;
	$pdf->addText($XPos ,$YPos,$FontSize, _('FOR VIEWING ONLY') . ', ' . _('DO NOT SEND TO SUPPLIER') );
	$pdf->addText($XPos ,$YPos-$line_height,$FontSize, _('SUPPLIERS') . ' - ' . _('THIS IS NOT AN ORDER') );
}

$YPos -= ($line_height*3);
$FontSize=10;
$XPos = $Page_Width-$Right_Margin-50;
$pdf->addText($XPos,$YPos +40, $FontSize, _('Page') . ': ' .$PageNumber);


/*Now print out the company name and address */
/* Quincy : Name Address and contact(sicomm format) */
$XPos = $Left_Margin;
$YPos = $YPosOrderHeader-$line_height;

$FontSize = 12;
$pdf->addText($XPos,$YPos,$FontSize, _('PO NO:'). ' ' . $OrderNo);
$pdf->addText($Page_Width/2 + 5, $YPos, $FontSize, _('Order Date:'));
$pdf->addText($Page_Width/2 + 75, $YPos, $FontSize, ConvertSQLDate($POHeader['orddate']));

$FontSize=10;
//$pdf->addText($Left_Margin,$YPos, $FontSize, $_SESSION['TaxAuthorityReferenceName'] . ' ' . $_SESSION['CompanyRecord']['gstno']);
$pdf->addText($XPos,$YPos-(0.8*$line_height), $FontSize, 'Buyer:'.$_SESSION['CompanyRecord']['coyname']);
$pdf->addText($XPos,$YPos-(1.6*$line_height), $FontSize, _('ADDR'). ': ' . $_SESSION['CompanyRecord']['regoffice1'] . ',' .$_SESSION['CompanyRecord']['regoffice2'] . ',' .$_SESSION['CompanyRecord']['regoffice3'] . ',' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);
$pdf->addText($XPos,$YPos-(2.4*$line_height), $FontSize, _('TEL'). ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' ._('FAX').': ' . $_SESSION['CompanyRecord']['fax'] );
$pdf->addText($XPos,$YPos-(3.2*$line_height), $FontSize, _('Email'). ': ' . $_SESSION['CompanyRecord']['email']);

$XPos = $Page_Width/2 + 5;
$YPos = $YPosOrderHeader-2*$line_height;

$pdf->addText($XPos+1,$YPos, $FontSize, 'Vender:'.$POHeader['suppname']);
$pdf->addText($XPos+1,$YPos-$line_height, $FontSize, $POHeader['address1']);
$pdf->addText($XPos+1,$YPos-(2*$line_height), $FontSize, $POHeader['address2']);
$pdf->addText($XPos+1,$YPos-(3*$line_height), $FontSize, $POHeader['address3']);
$pdf->addText($XPos+1,$YPos-(4*$line_height), $FontSize, $POHeader['address4']);

/*end of the small table showing the coy name and delivery address details */

/*
$YPos -= $line_height;
$pdf->addText($XPos,$YPos, $FontSize, _('Initiator').': ');
$pdf->addText($XPos+70,$YPos, $FontSize, $POHeader['initiator']);
*/

$YPos = $YPosOrderRemark-2*$line_height;
$pdf->addText($Left_Margin,$YPos, $FontSize, _('Comments') );
$pdf->addText($Left_Margin+100,$YPos, $FontSize, _('All amounts stated in').' - ' . $POHeader['currcode']);
$LeftOvers = $pdf->addTextWrap($XPos, $YPos+$line_height,250,$FontSize,$POHeader['comments'], 'left');
if (strlen($LeftOvers)>0){
	$LeftOvers = $pdf->addTextWrap($XPos, $YPos-$line_height,250,$FontSize,$LeftOvers, 'left');
}

$XPos = $Left_Margin;

/*draw a square grid for order header */ 
$pdf->line($Left_Margin, $YPosOrderHeader, $Page_Width-$Right_Margin, $YPosOrderHeader); // Top 
$pdf->line($Left_Margin, $YPosOrderHeader-$line_height, $Page_Width-$Right_Margin, $YPosOrderHeader-$line_height); // Top+1st Line
$pdf->line($Page_Width-$Right_Margin, $YPosOrderHeader, $Page_Width-$Right_Margin, $YPosOrderDetails+10); //Right
$pdf->line($Left_Margin, $YPosOrderDetails+10, $Page_Width-$Right_Margin, $YPosOrderDetails+10); //Bottom
$pdf->line($Left_Margin, $YPosOrderHeader, $Left_Margin, $YPosOrderDetails+10); //Left
$pdf->line($Page_Width/2, $YPosOrderHeader, $Page_Width/2, $YPosOrderDetails+10); //Middle

$YPos -= ($line_height*.5);
/*draw a square grid for entering line items */
$pdf->line($Left_Margin, $YPosOrderDetails, $Page_Width-$Right_Margin, $YPosOrderDetails); // Top 
$pdf->line($Page_Width-$Right_Margin, $YPosOrderDetails, $Page_Width-$Right_Margin, $YPosOrderRemark+20); //Right
$pdf->line($Page_Width-$Right_Margin, $YPosOrderRemark+20, $Left_Margin, $YPosOrderRemark+20); //Bottom
$pdf->line($Left_Margin, $YPosOrderDetails, $Left_Margin, $YPosOrderRemark+20); //Left


/*draw a square grid for order remarks */ 
$pdf->line($Left_Margin, $YPosOrderRemark, $Page_Width-$Right_Margin, $YPosOrderRemark); // Top
$pdf->line($Left_Margin, $YPosOrderRemark-$line_height, $Page_Width-$Right_Margin, $YPosOrderRemark-$line_height); // Top2  
$pdf->line($Page_Width-$Right_Margin, $YPosOrderRemark, $Page_Width-$Right_Margin, $YPosConfirm); //Right
$pdf->line($Left_Margin, $YPosConfirm, $Page_Width-$Right_Margin, $YPosConfirm); //Bottom
$pdf->line($Left_Margin, $YPosOrderRemark, $Left_Margin, $YPosConfirm); //Left
$pdf->line($Left_Margin+100, $YPosOrderRemark, $Left_Margin+100, $YPosConfirm); //Left2

/*draw a square grid for order confirm */ 

$pdf->line($Left_Margin, $Bottom_Margin, $Left_Margin+200, $Bottom_Margin); //  
$pdf->line($Page_Width-$Right_Margin-200, $Bottom_Margin, $Page_Width-$Right_Margin, $Bottom_Margin); // 

$YPos = $YPosOrderDetails-$line_height;
/*Set up headings */
$FontSize=10;
$pdf->addText($Left_Margin+1,$YPos, $FontSize, _('Code') );
$pdf->addText($Left_Margin+95,$YPos, $FontSize, _('Item Description') );
$pdf->addText($Left_Margin+400,$YPos, $FontSize, _('Quantity') );
$pdf->addText($Left_Margin+450,$YPos, $FontSize, _('Unit') );
$pdf->addText($Left_Margin+485,$YPos, $FontSize, _('Date Reqd'));
$pdf->addText($Left_Margin+600,$YPos, $FontSize, _('Price') );
$pdf->addText($Left_Margin+680,$YPos, $FontSize, _('Total') );

/*draw a line: 1st line atfter Header top*/ 
$YPos -=($line_height*0.4);
$pdf->line($Left_Margin, $YPos, $Page_Width-$Right_Margin, $YPos);

/*Now the delivery details */
$XPos = $Left_Margin;
$YPos = $YPosOrderRemark;
$FontSize=10;
$pdf->addText($XPos+2,$YPos-$line_height, $FontSize, _('Destination') );
$pdf->addText($XPos+100,$YPos-$line_height, $FontSize, $POHeader['deladd1'].''.$POHeader['deladd2'].''.$POHeader['deladd3'].
                 ', '.$POHeader['deladd4'].''.$POHeader['deladd5'].''.$POHeader['deladd6']);

/*YPostion for ItemDetails*/
$YPos = $YPosOrderDetails-2*$line_height;
?>