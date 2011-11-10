<?php

$PageSecurity = 4;

include('includes/DefineStockAdjustment.php');
include('includes/SQL_CommonFunctions.inc');

/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');
$title = _('Manuf (Test Fail) ');
include('includes/header.inc');

echo '<P CLASS="page_title_text">  Manufacture:Monthly test fail</p>';
echo '<HR>';

/*
* Get the item and value, before Submit or save 
*/
if (isset($_POST['SaveTFAdju']) ){
	foreach ($_POST as $key => $value) {
		if (strstr($key,"itm")) {
			$_SESSION['NewMonthTF'][substr($key,3)] = trim($value);
		}
	}
}

/*
*  Submit Test Fail Adjuestment
*/
If (isset($_SESSION['NewMonthTF']) && isset($_POST['SubmitTFAdju'])){
	$TFSTOCKAdjust = new StockAdjustment;
		
	$monthYear = mktime(0,0,0,$_SESSION['TestFail']->LastAdjuMonth+1,1,date('Y'));
	$monthStamp = date('Ym', $monthYear);
	//echo 'monthStamp is '.$monthStamp;
	
	$referenceStr = '#Manf(TestFail)-'.$monthStamp;

	foreach($_SESSION['NewMonthTF'] as $NewItem => $NewItemQty) {
		//Negotive protection
		if($NewItemQty > 0)	{
			$TFSTOCKAdjust->processStockAdjustment($NewItem, -$NewItemQty, 'HZ', $referenceStr, 1, '');
			//echo 'Item:'.$NewItem.'Qty:'.$NewItemQty;
		}
	}		
	unset ( $TFSTOCKAdjust );
}

/*
*	Search items by default
*/
$_POST['RefreshTestFailTable'] = true;
if (isset($_POST['RefreshTestFailTable'])) {
	unset( $_SESSION['TestFail'] );
	unset( $_SESSION['TestFail']->TFLineItems );
	
	$_SESSION['TestFail']= new TestFailureTable;
}



/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/
echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';

if (count($_SESSION['TestFail']->TFLineItems)>0){	   
	
	echo "<table class='commontable'>";
	echo '<caption> Monthly test fail </caption>';
	
	$tableheader = '<tr>
			<th>' . _('Code') . '</th>
			<th>' . _('M01') . '</th>
			<th>' . _('M02') . '</th>
			<th>' . _('M03') . '</th>
			<th>' . _('M04') . '</th>
			<th>' . _('M05') . '</th>
			<th>' . _('M06') . '</th>
			<th>' . _('M07') . '</th>
			<th>' . _('M08') . '</th>
			<th>' . _('M09') . '</th>
			<th>' . _('M10') . '</th>
			<th>' . _('M11') . '</th>
			<th>' . _('M12') . '</th>			
			
			</tr>';
	echo $tableheader;

	//Get Items TestFail Qty by line
	$_SESSION['TestFail']->TFLineCount=0;	
	foreach ($_SESSION['TestFail']->TFLineItems as $GetTFLines) {
		$_SESSION['TestFail']->getItemFailArray($GetTFLines->StockCode);
		if( isset($_SESSION['TestFail']->ActiveLine) and ($GetTFLines->StockCode == $_SESSION['TestFail']->ActiveLine->StockCode)){			
			$_SESSION['TestFail']->TFLineItems[$_SESSION['TestFail']->TFLineCount] = $_SESSION['TestFail']->ActiveLine;
		}
		$_SESSION['TestFail']->TFLineCount++;
	}	
	
	foreach ($_SESSION['TestFail']->TFLineItems as $TFLine) {	
		echo '<input type="hidden" name="StockCode" value=' . ($TFLine->StockCode) .'>';
				
		if( isset($_POST['SaveTFAdju']) ){
		    //echo '======'.$_SESSION['NewMonthTF']["$TFLine->StockCode"].'<br>';  
			$SavedValue = $_SESSION['NewMonthTF']["$TFLine->StockCode"];
		}else{
			$SavedValue = '';
		}		
		
		echo '<td>'.$TFLine->StockCode.'</td>';
		
		for($i=0; $i<12; $i++){ 
			if( $i < $_SESSION['TestFail']->LastAdjuMonth ){
				echo '<td align=right>'. $TFLine->MonthQtyArray[$i+1].'</td>';
			}else if($i == $_SESSION['TestFail']->LastAdjuMonth){
				if( isset($_POST['InputTestFailQty'] )){
					echo '<td><font size=1><input class="number" onKeyPress="return restrictToNumbers(this, event)"   type="textbox" size=6 name="itm'.$TFLine->StockCode.'" value=0>';
				}else{ //Status: saved input | not input					
					echo '<td align=right background=green>'.$SavedValue.'</td>';
				}	
			}else{
				echo '<td></td>';
			}
		}
		echo '</tr>';	
	}
}	

echo '</table>';

if( isset($_POST['InputTestFailQty'] )){ // Some Value has been input
	echo '<p  align="right" ><input type=submit name="CancelInput" value="Cancel the Input" /><input type=submit name="SaveTFAdju" value="Save Month Fail" /></p>';		
}elseif( isset($_POST['SaveTFAdju'] ) ){
	    echo '<p  align="right" ><input type=submit name="InputTestFailQty" value="Edit the input value" /> <input type=submit name="SubmitTFAdju" value="Confirm and Submit" /></p>';
}else{
    echo '<p  align="right" ><input type=submit name="InputTestFailQty" value="Input Monthly Test Fail" /> </p>';		
}	
echo '</form>';

include('includes/footer.inc');
?>
