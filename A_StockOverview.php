<?php

/* $Revision: 1.23 $ */


$PageSecurity = 2;

include('includes/session.inc');

$title = _('Stock Overview');

include('includes/header.inc');

if (isset($_GET['StockID'])){
	$StockID = trim(strtoupper($_GET['StockID']));
} elseif (isset($_POST['StockID'])){
	$StockID = trim(strtoupper($_POST['StockID']));
} else {
	$StockID = '';
}

//$_SESSION['CustomerID'] = 'HYT';
// This is already linked from this page
/////////////////////////////////////////////////////////////////
/* displays item options if there is one and only one selected */

echo '<P CLASS="page_title_text">  Stock:  '.$StockID.' </P>';
echo '<HR>';
echo '<P CLASS="page_subtitle_text">  >>Overview: </P>';
//echo "<input onclick=\"javascript:window.location.href='Stocks.php?&StockID=$StockID'\"  type='button' value='Item Detail' />";
//echo "<input onclick=\"javascript:window.location.href='StockCostUpdate.php?&StockID=$StockID'\"  type='button' value='Standard Cost' />";
//echo "<input onclick=\"javascript:window.location.href='PurchData.php?&StockID=$StockID'\"  type='button' value='Purchsing Data' />";
//echo "<input onclick=\"javascript:window.location.href='Prices.php?&Item=$StockID'\"  type='button' value='Pricing' />";


//if (!isset($_POST['Search']) AND (isset($_POST['Select']) OR isset($_SESSION['SelectedStockItem']))) {
if(isset($StockID)){
/*
    if (isset($_POST['Select'])) {
        $_SESSION['SelectedStockItem'] = $_POST['Select'];
        $StockID = $_POST['Select'];
        unset($_POST['Select']);
    } else {
        $StockID = $_SESSION['SelectedStockItem'];
    }
*/
    $result = DB_query("SELECT stockmaster.description,
                            stockmaster.mbflag,
                            stockcategory.stocktype,
                            stockmaster.units,
                            stockmaster.decimalplaces,
                            stockmaster.controlled,
                            stockmaster.serialised,
                            stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS cost,
                            stockmaster.discontinued,
                            stockmaster.eoq,
                            stockmaster.volume,
                            stockmaster.kgs
                            FROM stockmaster INNER JOIN stockcategory
                            ON stockmaster.categoryid=stockcategory.categoryid
                            WHERE stockid='" . $StockID . "'",$db);
    $myrow = DB_fetch_array($result);

    $Its_A_Kitset_Assembly_Or_Dummy = false;
    $Its_A_Dummy = false;
    $Its_A_Kitset = false;
    $Its_A_Labour_Item = false;
////////////////////////////////////////////////////
	$DispMbflag = 'Purchased Item';
    switch ($myrow['mbflag']) {
        case 'A':
            $DispMbflag='Assembly Item';
            $Its_A_Kitset_Assembly_Or_Dummy = True;
            break;
        case 'K':
            $DispMbflag='Kitset Item';
            $Its_A_Kitset_Assembly_Or_Dummy = True;
            $Its_A_Kitset = True;
            break;
        case 'D':
            $DispMbflag = 'Service/Labour Item';
            $Its_A_Kitset_Assembly_Or_Dummy = True;
            $Its_A_Dummy = True;
            if ($myrow['stocktype']=='L'){
                $Its_A_Labour_Item = True;
            }
            break;
        case 'B':
            $DispMbflag = 'Purchased Item';
            break;
        default:
            $DispMbflag = 'Manufactured Item';
            break;
    }

	$DispSerialised = 'N/A';
	if ($myrow['serialised'] == 1) {
        $DispSerialised = 'serialised';
    } elseif ($myrow['controlled'] == 1) {
        $DispSerialised = 'Batchs/Lots';
    } else {
        $DispSerialised = 'N/A';
    }
    $PriceResult = DB_query("SELECT typeabbrev, price FROM prices
                                WHERE currabrev ='" . $_SESSION['CompanyRecord']['currencydefault'] . "'
                                AND typeabbrev = '" . $_SESSION['DefaultPriceList'] . "'
                                AND debtorno=''
                                AND branchcode=''
                                AND stockid='".$StockID."'",
                                $db);
    if ($myrow['mbflag'] == 'K' OR $myrow['mbflag'] == 'A') {
        $CostResult = DB_query("SELECT SUM(bom.quantity*
                        (stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost)) AS cost
                    FROM bom INNER JOIN
                        stockmaster
                    ON bom.component=stockmaster.stockid
                    WHERE bom.parent='" . $StockID . "'
                    AND bom.effectiveto > '" . Date("Y-m-d") . "'
                    AND bom.effectiveafter < '" . Date("Y-m-d") . "'",
                    $db);
        $CostRow = DB_fetch_row($CostResult);
        $Cost = $CostRow[0];
    } else {
        $Cost = $myrow['cost'];
    }

	$DispPrice = '';
	$DispPriceUnit ='';
    if (DB_num_rows($PriceResult) == 0) {
        $DispPrice = 'No Default Price Set in Home Currency';
        $Price = 0;
    } else {
        $PriceRow = DB_fetch_row($PriceResult);
        $Price = $PriceRow[1];
        //echo $PriceRow[0] . '</td><td align=right>' . number_format($Price, 2) . '</td>
		$DispPriceUnit = $PriceRow[0];
		$DispPrice = number_format($Price, 2);
            
		if ($Price > 0) {
			$GP = number_format(($Price - $Cost) * 100 / $Price, 2);
		} else {
			$GP = _('N/A');
		}
/*
        while ($PriceRow = DB_fetch_row($PriceResult)) {
            $Price = $PriceRow[1];
            echo '<tr><td></td><th>' . $PriceRow[0] . '</th><td align=right>' . number_format($Price,2) . '</td>
            <th align=right>' . _('Gross Profit') . '</th><td align=right>';
            if ($Price > 0) {
                $GP = number_format(($Price - $Cost) * 100 / $Price, 2);
            } else {
                $GP = _('N/A');
            }
            echo $GP.'%'. '</td></tr>';
            echo '</td></tr>';		
        }
*/			
    }
    if ($myrow['mbflag'] == 'K' OR $myrow['mbflag'] == 'A') {
        $CostResult = DB_query("SELECT SUM(bom.quantity*
                        (stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost)) AS cost
                    FROM bom INNER JOIN
                        stockmaster
                    ON bom.component=stockmaster.stockid
                    WHERE bom.parent='" . $StockID . "'
                    AND bom.effectiveto > '" . Date("Y-m-d") . "'
                    AND bom.effectiveafter < '" . Date("Y-m-d") . "'",
                    $db);
        $CostRow = DB_fetch_row($CostResult);
        $Cost = $CostRow[0];
    } else {
        $Cost = $myrow['cost'];
    }
	
	//$ModifyLink = $rootpath . '/SelectOrderItems.php?' . SID . 'ModifyOrderNumber=' .$myrow['orderno'];
	//<A HREF='%s'>%s</A>
	//echo '&nbsp;&nbsp;<a href="' . $rootpath . '/SelectOrderItems.php?' . SID . '&NewOrder=Yes">' . _('Add Sales Order') . '</a>';
	$EditImg =$rootpath.'/css/'.$theme.'/images/edit_inline.gif';
	$InquiryImg =$rootpath.'/css/'.$theme.'/images/view_inline.gif';	
	
	$ItemDetailEditLink = $rootpath . '/Stocks.php?' . SID . 'StockID=' .$StockID;	
	$PriceEditLink = $rootpath . '/Prices.php?' . SID . 'Item=' .$StockID;	
	$StdCostEditLink = $rootpath . '/StockCostUpdate.php?' . SID . 'StockID=' .$StockID;
	
	if( $DispMbflag == 'Manufactured Item'){
		$MbViewLink = $rootpath . '/BOMInquiry.php?' . SID . 'StockID=' .$StockID;
		$MbEditLink = $rootpath . '/BOMs.php?' . SID . 'StockID=' .$StockID;
	}else {
		$MbViewLink = $rootpath . '/PurchData.php?' . SID . 'StockID=' .$StockID;
		$MbEditLink = '';
	}
	
    echo '<TABLE class="commontable">';
	echo '<TR><TH width =12% align=right>' . _('Item Name:') . '</TH><TD width=38%>'.$StockID.'<A HREF='.$ItemDetailEditLink.'> edit<img src='.$EditImg.'></A></TD>
	<TH align=right>' . _('EOQ') . ':</TH><TD>' . number_format($myrow['eoq'],$myrow['decimalplaces']) .'</TD></TR>';
    echo '<TR><TH align=right>' . _('Item type:') . '</TH><TD>'.$DispMbflag.'<A HREF='.$MbViewLink.'> view<img src='.$InquiryImg.'></A>  <A HREF='.$MbEditLink.'> edit<img src='.$EditImg.'></A></TD>
	<TH width=12% align=right>' . _('Sell Price') .'</TH><TD>'.$Price.'<A HREF='.$PriceEditLink.'> edit<img src='.$EditImg.'></A></TD></TR>';
    echo '<TR><TH align=right>' . _('Control Level:') .'</TH><TD>'.$DispSerialised.'</TD>
	<TH align=right>' . _('Cost') . '</TH><TD>' . number_format($Cost,3) . ''.'<A HREF='.$StdCostEditLink.'> edit<img src='.$EditImg.'></A></TD></TR>';	
    echo '<TR><TH align=right>' . _('Units') . ':</TH><TD>' . $myrow['units'] . '</TD>
	<TH align=right>' . _('Gross Profit') . '</th><td>'.$GP.'%'. '</td></TR>';	
	

    echo '<TR><TH align=right>' . _('Volume') . ':</TH><TD>' . number_format($myrow['volume'], 3) . '</TD>';
    echo '<TR><TH align=right>' . _('Weight') . ':</TH><TD>' . number_format($myrow['kgs'], 3) . '</TD>
	<TH align=right>' . _('Special Price') . '</th><td><A HREF=Prices_Customer.php?&Item='.$StockID.'>for customer '.$_SESSION['CustomerID'].'</A></td></TR>';


   // Item Category Property mod: display the item properties
       $CatValResult = DB_query("SELECT categoryid
                    FROM stockmaster
                WHERE stockid='" . $StockID . "'", $db);
               $CatValRow = DB_fetch_row($CatValResult);
               $CatValue = $CatValRow[0];

       $sql = "SELECT stkcatpropid,
            label,
            controltype,
            defaultvalue
        FROM stockcatproperties
        WHERE categoryid ='" . $CatValue . "'
        AND reqatsalesorder =0
        ORDER BY stkcatpropid";

       $PropertiesResult = DB_query($sql,$db);
       $PropertyCounter = 0;
       $PropertyWidth = array();

       while ($PropertyRow = DB_fetch_array($PropertiesResult)) {

               $PropValResult = DB_query("SELECT value
                            FROM stockitemproperties
                    WHERE stockid='" . $StockID . "'
                    AND stkcatpropid =" . $PropertyRow['stkcatpropid'],
                                                                       $db);
               $PropValRow = DB_fetch_row($PropValResult);
               $PropertyValue = $PropValRow[0];

               echo '<tr><th align="right">' . $PropertyRow['label']. ':</th>';
               switch ($PropertyRow['controltype']) {
                       case 0; //textbox
                               echo '<td align=right width=60><input type="text" name="PropValue' . 
                               	$PropertyCounter . '" value="'. $PropertyValue.'">';
                               break;
                       case 1; //select box
                               $OptionValues = explode(',',$PropertyRow['defaultvalue']);
                                echo '<td align=left width=60><select name="PropValue' .$PropertyCounter . '">';
                               foreach ($OptionValues as $PropertyOptionValue) {
                                       if ($PropertyOptionValue == $PropertyValue) {
                                               echo '<option selected value="' . $PropertyOptionValue . '">' .$PropertyOptionValue . '</option>';
                                       } else {
                                               echo '<option value="' . $PropertyOptionValue . '">' .$PropertyOptionValue . '</option>';
                                       }
                               }
                               echo '</select>';
                               break;
                       case 2; //checkbox
                               echo '<td align=left width=60><input type="checkbox" name="PropValue' . $PropertyCounter . '"';
                               if ($PropertyValue==1){
                                       echo ' checked';
                               }
                               echo '>';
                               break;
               } //end switch
               echo '</td></tr>';
               $PropertyCounter++;
       } //end loop round properties for the item category


    echo '<TD >';


    $QOH = 0;
    switch ($myrow['mbflag']) {
        case 'A':
        case 'D':
        case 'K':
            $QOH = _('N/A');
            $QOO = _('N/A');
            break;
        case 'M':
        case 'B':
            $QOHResult = DB_query("SELECT sum(quantity)
                        FROM locstock
                        WHERE stockid = '" . $StockID . "'",
                                        $db);
            $QOHRow = DB_fetch_row($QOHResult);
            $QOH = number_format($QOHRow[0],$myrow['decimalplaces']);

            $QOOResult = DB_query("SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd)
                                    FROM purchorderdetails
                                    WHERE purchorderdetails.itemcode='" . $StockID . "'",
                                    $db);
            if (DB_num_rows($QOOResult) == 0){
                $QOO = 0;
            } else {
                $QOORow = DB_fetch_row($QOOResult);
                $QOO = $QOORow[0];
            }
            //Also the on work order quantities
            $sql = "SELECT SUM(woitems.qtyreqd-woitems.qtyrecd) AS qtywo
                FROM woitems INNER JOIN workorders
                ON woitems.wo=workorders.wo
                WHERE workorders.closed=0
                AND woitems.stockid='" . $StockID . "'";
            $ErrMsg = _('The quantity on work orders for this product cannot be retrieved because');
            $QOOResult = DB_query($sql,$db,$ErrMsg);

            if (DB_num_rows($QOOResult) == 1) {
                $QOORow = DB_fetch_row($QOOResult);
                $QOO +=  $QOORow[0];
            }
            $QOO = number_format($QOO,$myrow['decimalplaces']);
            break;
    }
    $Demand = 0;
    $DemResult = DB_query("SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                FROM salesorderdetails INNER JOIN salesorders
                ON salesorders.orderno = salesorderdetails.orderno
                WHERE salesorderdetails.completed=0
                AND salesorders.quotation=0
                AND salesorderdetails.stkcode='" . $StockID . "'",
                            $db);

    $DemRow = DB_fetch_row($DemResult);
    $Demand = $DemRow[0];
    $DemAsComponentResult = DB_query("SELECT  SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
                FROM salesorderdetails,
                    salesorders,
                    bom,
                    stockmaster
                WHERE salesorderdetails.stkcode=bom.parent
                AND salesorders.orderno = salesorderdetails.orderno
                AND salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0
                AND bom.component='" . $StockID . "'
                AND stockmaster.stockid=bom.parent
                AND stockmaster.mbflag='A'
                    AND salesorders.quotation=0",
                    $db);

    $DemAsComponentRow = DB_fetch_row($DemAsComponentResult);
    $Demand += $DemAsComponentRow[0];
    //Also the demand for the item as a component of works orders

    $sql = "SELECT SUM(qtypu*(woitems.qtyreqd - woitems.qtyrecd)) AS woqtydemo
                FROM woitems INNER JOIN worequirements
                ON woitems.stockid=worequirements.parentstockid
                INNER JOIN workorders
                ON woitems.wo=workorders.wo
                AND woitems.wo=worequirements.wo
                WHERE  worequirements.stockid='" . $StockID . "'
                AND workorders.closed=0";

    $ErrMsg = _('The workorder component demand for this product cannot be retrieved because');
    $DemandResult = DB_query($sql,$db,$ErrMsg);

    if (DB_num_rows($DemandResult) == 1) {
        $DemandRow = DB_fetch_row($DemandResult);
        $Demand += $DemandRow[0];
    }
	
	echo '</TABLE>';


	
echo '<P CLASS="page_subtitle_text">  >>Status: </P>';	
echo "<input onclick=\"javascript:window.location.href='StockMovements.php?&StockID=$StockID'\"  type='button' value='Show Stock Movements' />";
echo "<input onclick=\"javascript:window.location.href='StockUsage.php?&StockID=$StockID'\"  type='button' value='Show Stock Usage' />";
echo "<input onclick=\"javascript:window.location.href='StockAdjustments.php?&StockID=$StockID'\"  type='button' value='Quantity Adjustment' />";
	echo '<TABLE class="searchtable">';
    echo '<TR><TH>' . _('Quantity On Hand') . ':</TH><TD>' . $QOH . '</TD>';
    echo '<TH>' . _('Quantity Demand') . ':</TH><TD >' . number_format($Demand,$myrow['decimalplaces']) . '</TD>';
    echo '<TH>' . _('Quantity On Order') . ':</TH><TD>' . $QOO . '</TD></TR>
                </TABLE>';

}


include('includes/footer.inc');

?>
