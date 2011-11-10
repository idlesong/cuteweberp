<?php

/* $Revision: 1.20 $ */

$PageSecurity = 10;

include('includes/session.inc');

$title = _('Please Add a Packing PO');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
// echo '<P CLASS="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/transactions.png" TITLE="' . _('Search') . '" ALT="">' . ' ' . $title;
echo '<p class=page_title_text>Packing PO</p>';
echo '<hr>';

$_SESSION['UserPackingVendor'] = 'TSHT'; 
if (isset($_REQUEST['WO']) and $_REQUEST['WO']!=''){
	$_POST['WO'] = $_REQUEST['WO'];
    $EditingExisting = true;
} else {
    $_POST['WO'] = GetNextTransNo(40,$db);
    $InsWOResult = DB_query("INSERT INTO workorders (wo,
                                                     loccode,
                                                     requiredby,
                                                     startdate,
						     vendorid)
                                     VALUES (" . $_POST['WO'] . ",
                                            '" . $_SESSION['UserStockLocation'] . "',
                                            '" . Date('Y-m-d') . "',
                                            '" . Date('Y-m-d'). "',
					    '" . $_SESSION['UserPackingVendor'] . "')",
                              $db);
}

if (isset($_GET['NewItem'])){
	$NewItem = $_GET['NewItem'];
}

if (!isset($_POST['StockLocation'])){
	if (isset($_SESSION['UserStockLocation'])){
		$_POST['StockLocation']=$_SESSION['UserStockLocation'];
	}
}

if (!isset($_POST['PackingVendor'])){
	if (isset($_SESSION['UserPackingVendor'])){
		$_POST['PackingVendor']=$_SESSION['UserPackingVendor'];
	}
}

if(0){
if (isset($_POST['Search'])){

	If ($_POST['Keywords'] AND $_POST['StockCode']) {
		prnMsg(_('Stock description keywords have been used in preference to the Stock code extract entered'),'warn');
	}
	If (strlen($_POST['Keywords'])>0) {
			//insert wildcard characters in spaces
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);

		$i=0;
		$SearchString = '%';
		while (strpos($_POST['Keywords'], ' ', $i)) {
			$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
			$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
			$i=strpos($_POST['Keywords'],' ',$i) +1;
		}
		$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
					FROM stockmaster,
					stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.description " . LIKE . " '$SearchString'
					AND stockmaster.discontinued=0
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		}

	} elseif (strlen($_POST['StockCode'])>0){

		$_POST['StockCode'] = strtoupper($_POST['StockCode']);
		$SearchString = '%' . $_POST['StockCode'] . '%';

		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		}
	} else {
		if ($_POST['StockCat']=='All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued=0
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND mbflag='M'
					ORDER BY stockmaster.stockid";
		  }
	}

	$SQL = $SQL . ' LIMIT ' . $_SESSION['DisplayRecordsMax'];

	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL used to get the part selection was');
	$SearchResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

	if (DB_num_rows($SearchResult)==0 ){
		prnMsg (_('There are no products available meeting the criteria specified'),'info');

		if ($debug==1){
			prnMsg(_('The SQL statement used was') . ':<BR>' . $SQL,'info');
		}
	}
	if (DB_num_rows($SearchResult)==1){
		$myrow=DB_fetch_array($SearchResult);
		$NewItem = $myrow['stockid'];
		DB_data_seek($SearchResult,0);
	}

} //end of if search
}

if( isset( $_SESSION['msg_SelectedItem'] ) and $_SESSION['msg_SelectedItem'] != ''){
	$NewItem = $_SESSION['msg_SelectedItem'];
	unset($_SESSION['msg_SelectedItem']);
}

if (isset($NewItem) AND isset($_POST['WO'])){
      $InputError=false;
	  $CheckItemResult = DB_query("SELECT mbflag,
						eoq
					FROM stockmaster
					WHERE stockid='" . $NewItem . "'",
					$db);
	  if (DB_num_rows($CheckItemResult)==1){
	  		$CheckItemRow = DB_fetch_array($CheckItemResult);
	  		$EOQ = $CheckItemRow['eoq'];
	  		if ($CheckItemRow['mbflag']!='M'){
	  			prnMsg(_('The item selected cannot be addded to a work order because it is not a manufactured item'),'warn');
	  			$InputError=true;
	  		}
	  } else {
	  		prnMsg(_('The item selected cannot be found in the database'),'error');
	  		$InputError = true;
	  }
	  $CheckItemResult = DB_query("SELECT stockid
					FROM woitems
					WHERE stockid='" . $NewItem . "'
					AND wo=" .$_POST['WO'],
					$db);
	  if(DB_num_rows($CheckItemResult)==1){
	  		prnMsg(_('This item is already on the work order and cannot be added again'),'warn');
	  		$InputError=true;
	  }


	  if ($InputError==false){
		$CostResult = DB_query("SELECT SUM((materialcost+labourcost+overheadcost)*bom.quantity) AS cost
                                                        FROM stockmaster INNER JOIN bom
                                                        ON stockmaster.stockid=bom.component
                                                        WHERE bom.parent='" . $NewItem . "'
                                                        AND bom.loccode='" . $_POST['StockLocation'] . "'",
                             $db);
        	$CostRow = DB_fetch_row($CostResult);
		if (is_null($CostRow[0]) OR $CostRow[0]==0){
				$Cost =0;
				prnMsg(_('The cost of this item as accumulated from the sum of the component costs is nil. This could be because there is no bill of material set up ... you may wish to double check this'),'warn');
		} else {
				$Cost = $CostRow[0];
		}
		if (!isset($EOQ) OR $EOQ==0){
			$EOQ=1;
		}
		$sql[] = "INSERT INTO woitems (wo,
	                             stockid,
	                             qtyreqd,
	                             stdcost)
	         VALUES ( " . $_POST['WO'] . ",
                         '" . $NewItem . "',
                         " . $EOQ . ",
                          " . $Cost . "
                          )";

		$sql[] = "INSERT INTO worequirements (wo,
                                            parentstockid,
                                            stockid,
                                            qtypu,
                                            stdcost,
                                            autoissue)
      	                 SELECT " . $_POST['WO'] . ",
        	                           bom.parent,
                                       bom.component,
                                       bom.quantity,
                                       (materialcost+labourcost+overheadcost)*bom.quantity,
                                       autoissue
                         FROM bom INNER JOIN stockmaster
                         ON bom.component=stockmaster.stockid
                         WHERE parent='" . $NewItem . "'
                         AND loccode ='" . $_POST['StockLocation'] . "'";

         //run the SQL from either of the above possibilites
         $ErrMsg = _('The work order item could not be added');
         foreach ($sql as $sql_stmt){
                 $result = DB_query($sql_stmt,$db,$ErrMsg);
         } //end for each $sql statement
         unset($NewItem);
      } //end if there were no input errors
} //adding a new item to the work order


if (isset($_POST['submit']) OR isset($_POST['confirm'])) { //The update button has been clicked

	$Input_Error = false; //hope for the best
     for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
       	if (!is_numeric($_POST['OutputQty'.$i])){
	       	prnMsg(_('The quantity entered must be numeric'),'error');
	        $Input_Error = true;
        } elseif ($_POST['OutputQty'.$i]<=0){
		    prnMsg(_('The quantity entered must be a positive number greater than zero'),'error');
		    $Input_Error = true;
        }
     }
	 
	 if( $_POST['NumberOfOutputs'] <=0 ) {
		prnMsg(_('Quincy: Please add some item before update'),'error');
		$Input_Error = true;
	 }		
	 
     if (!Is_Date($_POST['RequiredBy'])){
	    prnMsg(_('The required by date entered is in an invalid format'),'error');
	    $Input_Error = true;
	 }

	if ($Input_Error == false) {

		$SQL_ReqDate = FormatDateForSQL($_POST['RequiredBy']);
		$QtyRecd=0;

		for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
				$QtyRecd+=$_POST['RecdQty'.$i];
		}

		if ($QtyRecd==0){ //can only change factory location if Qty Recd is 0
				$sql[] = "UPDATE workorders SET requiredby='" . $SQL_ReqDate . "',
				vendorid='" . $_POST['PackingVendor'] . "',
				loccode='" . $_POST['StockLocation'] . "'
			        	    WHERE wo=" . $_POST['WO'];
		} else {
				prnMsg(_('The factory where this work order is made can only be updated if the quantity received on all output items is 0'),'warn');
				$sql[] = "UPDATE workorders SET requiredby='" . $SQL_ReqDate . "'
							WHERE wo=" . $_POST['WO'];
		}

    	for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
    		if (!isset($_POST['NextLotSNRef'.$i])) {
    			$_POST['NextLotSNRef'.$i]='';
    		}
    			if (isset($_POST['QtyRecd'.$i]) and $_POST['QtyRecd'.$i]>$_POST['OutputQty'.$i]){
    					$_POST['OutputQty'.$i]=$_POST['QtyRecd'.$i]; //OutputQty must be >= Qty already reced
    			}
    			if ($_POST['RecdQty'.$i]==0){ // can only change location cost if QtyRecd=0
	    				$CostResult = DB_query("SELECT SUM((materialcost+labourcost+overheadcost)*bom.quantity) AS cost
                                                        FROM stockmaster INNER JOIN bom
                                                        ON stockmaster.stockid=bom.component
                                                        WHERE bom.parent='" . $_POST['OutputItem'.$i] . "'
                                                        AND bom.loccode='" . $_POST['StockLocation'] . "'",
    		                         $db);
        				$CostRow = DB_fetch_row($CostResult);
						if (is_null($CostRow[0])){
							$Cost =0;
							prnMsg(_('The cost of this item as accumulated from the sum of the component costs is nil. This could be because there is no bill of material set up ... you may wish to double check this'),'warn');
						} else {
							$Cost = $CostRow[0];
						}
						$sql[] = "UPDATE woitems SET qtyreqd =  ". $_POST['OutputQty' . $i] . ",
    			                                 nextlotsnref = '". $_POST['NextLotSNRef'.$i] ."',
    			                                 stdcost =" . $Cost . "
    			                  WHERE wo=" . $_POST['WO'] . "
                                  AND stockid='" . $_POST['OutputItem'.$i] . "'";
      			} else {
    			    	$sql[] = "UPDATE woitems SET qtyreqd =  ". $_POST['OutputQty' . $i] . ",
    			                                 nextlotsnref = '". $_POST['NextLotSNRef'.$i] ."'
    			                  WHERE wo=" . $_POST['WO'] . "
                                  AND stockid='" . $_POST['OutputItem'.$i] . "'";
                }
        }

		//run the SQL from either of the above possibilites
        $ErrMsg = _('The work order could not be added/updated');
        foreach ($sql as $sql_stmt){
        //	echo '<BR>' . $sql_stmt;
            $result = DB_query($sql_stmt,$db,$ErrMsg);

        }

	    prnMsg(_('The work order has been updated, click issue to link continue'),'success');

        for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
          	     unset($_POST['OutputItem'.$i]);
                 unset($_POST['OutputQty'.$i]);
                 unset($_POST['QtyRecd'.$i]);
                 unset($_POST['NetLotSNRef'.$i]);
        }
		
		if(isset($_POST['submit'])){
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL='. $rootpath . '/WorkOrderIssue.php?' . SID . '&WO=' .  $_REQUEST['WO'] . '&StockID=' . $_POST['submit'] . '">';
			prnMsg(_('You should automatically be forwarded to the entry of the delivery details page') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
			   '<a href="' . $rootpath . '/DeliveryDetails.php?' . SID . '">' . _('click here') . '</a> ' . _('to continue') . 'info');
			exit;	
		}
		//echo "<br><a href='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>" . _('Enter a new work order') . "</A>";
		//echo "<br><a href='" . $rootpath . "/SelectWorkOrder.php?" . SID . "'>" . _('Select an existing work order') . "</A>";
		//echo '<br><a href="'. $rootpath . '/WorkOrderCosting.php?' . SID . '&WO=' .  $_REQUEST['WO'] . '">' . _('Go to Costing'). '</A>';
		//echo "<br><br>";
	}
} elseif (isset($_POST['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete=false; //always assume the best

	// can't delete it there are open work issues
	$HasTransResult = DB_query("SELECT * FROM stockmoves
                                    WHERE (stockmoves.type= 26 OR stockmoves.type=28)
                                          AND reference LIKE '%" . $_POST['WO'] . "%'",$db);
	if (DB_num_rows($HasTransResult)>0){
		prnMsg(_('This work order cannot be deleted because it has issues or receipts related to it'),'error');
		$CancelDelete=true;
	}

	if ($CancelDelete==false) { //ie all tests proved ok to delete
		// delete the work order requirements
    		$sql="DELETE FROM worequirements WHERE wo=" . $_POST['WO'];
		$ErrMsg=_('The work order requirements could not be deleted');
    		$result = DB_query($sql,$db,$ErrMsg);
                //delete the items on the work order
		$sql = "DELETE FROM woitems WHERE wo=" . $_POST['WO'];
                $result = DB_query($sql,$db,$ErrMsg);
		// delete the actual work order
		$sql="DELETE FROM workorders WHERE wo=" . $_POST['WO'];
    		$ErrMsg=_('The work order could not be deleted');
		$result = DB_query($sql,$db,$ErrMsg);
		prnMsg(_('The work order has been deleted'),'success');

		echo "<P><A HREF='" . $rootpath . "/SelectWorkOrder.php?" . SID . "'>" . _('Select an existing outstanding work order') . "</A>";
		unset($_POST['WO']);
		for ($i=1;$i<=$_POST['NumberOfOutputs'];$i++){
          	     unset($_POST['OutputItem'.$i]);
                 unset($_POST['OutputQty'.$i]);
                 unset($_POST['QtyRecd'.$i]);
                 unset($_POST['NetLotSNRef'.$i]);
        }
        include('includes/footer.inc');
        exit;
    }
}

echo '<FORM METHOD="post" action="' . $_SERVER['PHP_SELF'] . '" name="form">';

echo '<TABLE class="commontable"><caption>Packing PO Header</caption>';

$sql="SELECT workorders.loccode,
	             requiredby,
                 startdate,
                 costissued,
		 vendorid,
                 closed
                FROM workorders INNER JOIN locations
                ON workorders.loccode=locations.loccode
                WHERE workorders.wo=" . $_POST['WO'];

$WOResult = DB_query($sql,$db);
if (DB_num_rows($WOResult)==1){
	$myrow = DB_fetch_array($WOResult);
	$_POST['StartDate'] = ConvertSQLDate($myrow['startdate']);
	$_POST['CostIssued'] = $myrow['costissued'];
	$_POST['PackingVendor'] = $myrow['vendorid'];
	$_POST['Closed'] = $myrow['closed'];
	$_POST['RequiredBy'] = ConvertSQLDate($myrow['requiredby']);
	$_POST['StockLocation'] = $myrow['loccode'];
	$ErrMsg =_('Could not get the work order items');
	$WOItemsResult = DB_query('SELECT woitems.stockid,
										stockmaster.description,
										qtyreqd,
										qtyrecd,
										stdcost,
										nextlotsnref,
										controlled,
										serialised
								FROM woitems INNER JOIN stockmaster
								ON woitems.stockid=stockmaster.stockid
								WHERE wo=' .$_POST['WO'],$db,$ErrMsg);

	$NumberOfOutputs=DB_num_rows($WOItemsResult);
	$i=1;
	while ($WOItem=DB_fetch_array($WOItemsResult)){
				$_POST['OutputItem' . $i]=$WOItem['stockid'];
				$_POST['OutputItemDesc'.$i]=$WOItem['description'];
				$_POST['OutputQty' . $i]= $WOItem['qtyreqd'];
		  		$_POST['RecdQty' .$i] =$WOItem['qtyrecd'];
		  		$_POST['NextLotSNRef' .$i]=$WOItem['nextlotsnref'];
		  		$_POST['Controlled'.$i] =$WOItem['controlled'];
		  		$_POST['Serialised'.$i] =$WOItem['serialised'];
		  		$i++;
	}
}

echo '<input type=hidden name="WO" value=' .$_POST['WO'] . '>';
echo '<tr><td>' . _('Packing PO#') . ':</td><td>' . $_POST['WO'] . '</td>';
/*
echo '<tr><th class="label">' . _('Packing Vendor') .':</td> <td><select name="PackingVendor">';
$LocResult = DB_query('SELECT code,description FROM workcentres',$db);
while ($LocRow = DB_fetch_array($LocResult)){
	if ($_POST['PackingVendor']==$LocRow['code']){
		echo '<option selected value="' . $LocRow['code'] .'">' . $LocRow['description'] . '</option>';
	} else {
		echo '<option value="' . $LocRow['code'] .'">' . $LocRow['description'] . '</option>';
	}
}
echo '</select></td></tr>';
*/
echo '<td>' . _('Packing Vendor') .':</td> <td><select name="PackingVendor">';
$LocResult = DB_query('SELECT code,description FROM workcentres',$db);
while ($LocRow = DB_fetch_array($LocResult)){
	if ($_POST['PackingVendor']==$LocRow['code']){
		echo '<option selected value="' . $LocRow['code'] .'">' . $LocRow['description'] . '</option>';
	} else {
		echo '<option value="' . $LocRow['code'] .'">' . $LocRow['description'] . '</option>';
	}
}
echo '</select></th></tr>';

if (!isset($_POST['StartDate'])){
	$_POST['StartDate'] = Date($_SESSION['DefaultDateFormat']);
}

echo '<input type="hidden" name="StartDate" value="' . $_POST['StartDate'] . '">';

echo '<tr><td>' . _('Packing PO Date') . ':</td><td>' . $_POST['StartDate'] . '</td>';

if (!isset($_POST['RequiredBy'])){
	$_POST['RequiredBy'] = Date($_SESSION['DefaultDateFormat']);
}

echo '<td>' . _('Comfirm Date') . ':</td>
		  <td><input type="textbox" name="RequiredBy"  size=12 maxlength=12 value="' . $_POST['RequiredBy'] . '" onChange="return isDate(this, this.value, '."'".$_SESSION['DefaultDateFormat']."'".')'.'"'.'></td></tr>';

if (isset($WOResult)){
	echo '<tr><td>' . _('Accumulated Costs') . ':</td>
			  <td>' . number_format($myrow['costissued'],2) . '</td><td colspan="2"></td></tr>';
}
echo '</table>
		<P><table class="commontable"><caption>Order Summary</caption>';
echo '<tr><th>' . _('Output Item') . '</th>
		  <th>' . _('Qty Required') . '</th>
		  <th>' . _('Qty Received') . '</th>
		  <th>' . _('Balance Remaining') . '</th>
		  <th>' . _('ISSUE TO') . '</th>		  
		  <th>' . _('Next Lot/SN Ref') . '</th>
		  </tr>';

if (isset($NumberOfOutputs)){
	for ($i=1;$i<=$NumberOfOutputs;$i++){
		echo '<tr><td><input type="hidden" name="OutputItem' . $i . '" value="' . $_POST['OutputItem' .$i] . '">' . $_POST['OutputItem' . $i] . ' </td>
		  		<td><input type="text" STYLE="text-align: right" name="OutputQty' . $i . '" value=' . $_POST['OutputQty' . $i] . ' size=10 onKeyPress="return restrictToNumbers(this, event)" maxlength=10></td>
		  		<td><input type="hidden" name="RecdQty' . $i . '" value=' . $_POST['RecdQty' .$i] . '>' . $_POST['RecdQty' .$i] .'</td>
		  		<td align="right">' . ($_POST['OutputQty' . $i] - $_POST['RecdQty' .$i]) . '</td>				
				<td><input type="submit" name="submit" value="' . $_POST['OutputItem' .$i] . '"></td>';
				
		if ($_POST['Controlled'.$i]==1){
			echo '<td><input type=textbox name="NextLotSNRef' .$i . '" value="' . $_POST['NextLotSNRef'.$i] . '"></td>';
		}
		echo '</tr>';
	}
	echo '<input type=hidden name="NumberOfOutputs" value=' . ($i -1).'>';
}

//Add enter newline button
echo '<tr><td><input onclick=openSubpage("h5ItemsSearch.php") type="button" value="Select products" /></td><td colspan="5"></td></tr>'; 
echo '</table>';

echo '<hr><P align="right"><input type=submit name="confirm" value="' . _('Update and Place Order') . '">';

echo '<INPUT TYPE=SUBMIT NAME="delete" VALUE="' . _('Delete This Packing PO') . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');"></p>';


if (!isset($_GET['NewItem']) or $_GET['NewItem']=='') {
	echo "<script>defaultControl(document.forms[0].StockCode);</script>";
} else {
	echo "<script>defaultControl(document.forms[0].OutputQty".$_GET['Line'].");</script>";
}


echo '</FORM>';

include('includes/footer.inc');

?>