<?php

//include('includes/header.inc');
include('includes/DefineSerialItems.php');

class StockAdjustment {

        var $StockID;
        Var $StockLocation;
        var $Controlled;
        var $Serialised;
        var $ItemDescription;
        Var $PartUnit;
        Var $StandardCost;
        Var $DecimalPlaces;
        Var $Quantity;
        var $SerialItems; /*array to hold controlled items*/
		
		var $Narrative;
		var $Reference;

        //Constructor
        function StockAdjustment(){
        	$this->StockID = '';
        	$this->StockLocation = '';
        	$this->Controlled = '';
        	$this->Serialised = '';
        	$this->ItemDescription = '';
        	$this->PartUnit = '';
        	$this->StandardCost = 0;
        	$this->DecimalPlaces = 0;
            $this->SerialItems = array();
            $Quantity =0;
			$this->Narrative='';
			$this->Reference='';
        }

		function processStockAdjustment($stkID, $adjustQty, $stkLocktion, $reference, $prohibitNegativeStock, $narrative){
			$this->StockLocation = $stkLocktion;
			$this->Reference = $reference;
			$this->Narrative = $narrative;
			
			if( $this->StockID != $stkID ){
				$this->StockID = $stkID;
				
				global $db;
				$sql ="SELECT description,
							units,
							mbflag,
							materialcost+labourcost+overheadcost as standardcost,
							controlled,
							serialised,
							decimalplaces
						FROM stockmaster
						WHERE stockid='" . $this->StockID . "'";
				$ErrMsg = _('Unable to load StockMaster info for part'). ':' . $this->StockID;
				$result = DB_query($sql, $db, $ErrMsg);
				$myrow = DB_fetch_row($result);

				if (DB_num_rows($result)==0){
							prnMsg( _('Unable to locate Stock Code').' '.$this->StockID, 'error' );
				} elseif (DB_num_rows($result)>0){

					$this->Controlled = $myrow[4];
					$this->Serialised = $myrow[5];
					$this->SerialItems = array();

					if ($myrow[2]=='D' OR $myrow[2]=='A' OR $myrow[2]=='K'){
						prnMsg( _('The part entered is either or a dummy part or an assembly or kit-set part') . '. ' . _('These parts are not physical parts and no stock holding is maintained for them') . '. ' . _('Stock adjustments are therefore not possible'),'error');
						echo '<HR>';
						echo '<A HREF="'. $rootpath .'/StockAdjustments.php?' . SID .'">'. _('Enter another adjustment'). '</A>';

						include ('includes/footer.inc');
						exit;
					}
				}			
			}
			
			$InputError = false; /*Start by hoping for the best */
			$result = DB_query("SELECT * FROM stockmaster WHERE stockid='" . $this->StockID . "'",$db);
			$myrow = DB_fetch_row($result);
			if (DB_num_rows($result)==0) {
				prnMsg( _('The entered item code does not exist'),'error');
				$InputError = true;
			} elseif (!is_numeric($adjustQty)){
				prnMsg( _('The quantity entered must be numeric'),'error');
				$InputError = true;
			} elseif ($adjustQty==0){
				prnMsg( _('The quantity entered cannot be zero') . '. ' . _('There would be no adjustment to make'),'error');
				$InputError = true;
			} elseif ($this->Controlled==1 AND count($this->SerialItems)==0) {
				prnMsg( _('The item entered is a controlled item that requires the detail of the serial numbers or batch references to be adjusted to be entered'),'error');
				$InputError = true;
			}

			if ( $prohibitNegativeStock==1){
				$SQL = "SELECT quantity FROM locstock
						WHERE stockid='" . $this->StockID . "'
						AND loccode='" . $this->StockLocation . "'";
				$CheckNegResult=DB_query($SQL,$db);
				$CheckNegRow = DB_fetch_array($CheckNegResult);
				if ($CheckNegRow['quantity']+$adjustQty <0){
					$InputError=true;
					prnMsg(_('The system parameters are set to prohibit negative stocks. Processing this stock adjustment would result in negative stock at this location. This adjustment will not be processed.'),'error');
				}
			}
			
			
			if (!$InputError) {
		/*All inputs must be sensible so make the stock movement records and update the locations stocks */

				$AdjustmentNumber = GetNextTransNo(17,$db);
				$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
				$SQLAdjustmentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));

				$Result = DB_Txn_Begin($db);

				// Need to get the current location quantity will need it later for the stock movement
				$SQL="SELECT locstock.quantity
					FROM locstock
					WHERE locstock.stockid='" . $this->StockID . "'
					AND loccode= '" . $this->StockLocation . "'";
				$Result = DB_query($SQL, $db);
				if (DB_num_rows($Result)==1){
					$LocQtyRow = DB_fetch_row($Result);
					$QtyOnHandPrior = $LocQtyRow[0];
				} else {
					// There must actually be some error this should never happen
					$QtyOnHandPrior = 0;
				}

				$SQL = "INSERT INTO stockmoves (
						stockid,
						type,
						transno,
						loccode,
						trandate,
						prd,
						reference,
						qty,
						newqoh,
						narrative)
					VALUES (
						'" . $this->StockID . "',
						17,
						" . $AdjustmentNumber . ",
						'" . $this->StockLocation . "',
						'" . $SQLAdjustmentDate . "',
						" . $PeriodNo . ",
						'" . $this->Reference ."',
						" . $adjustQty . ",
						" . ($QtyOnHandPrior + $adjustQty) . ",
						'" . $this->Narrative ."'
					)";

				//'" . $this->Narrative ."',
				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
				$DbgMsg =  _('The following SQL to insert the stock movement record was used');
				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);


		/*Get the ID of the StockMove... */
				$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

		/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/

				if ($this->Controlled ==1){
					foreach($this->SerialItems as $Item){
					/*We need to add or update the StockSerialItem record and
					The StockSerialMoves as well */

						/*First need to check if the serial items already exists or not */
						$SQL = "SELECT COUNT(*)
							FROM stockserialitems
							WHERE
							stockid='" . $this->StockID . "'
							AND loccode='" . $this->StockLocation . "'
							AND serialno='" . $Item->BundleRef . "'";
						$ErrMsg = _('Unable to determine if the serial item exists');
						$Result = DB_query($SQL,$db,$ErrMsg);
						$SerialItemExistsRow = DB_fetch_row($Result);

						if ($SerialItemExistsRow[0]==1){

							$SQL = "UPDATE stockserialitems SET
								quantity= quantity + " . $Item->BundleQty . "
								WHERE
								stockid='" . $this->StockID . "'
								AND loccode='" . $this->StockLocation . "'
								AND serialno='" . $Item->BundleRef . "'";

							$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
							$DbgMsg =  _('The following SQL to update the serial stock item record was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
						} else {
							/*Need to insert a new serial item record */
							$SQL = "INSERT INTO stockserialitems (stockid,
											loccode,
											serialno,
											quantity)
								VALUES ('" . $this->StockID . "',
								'" . $this->StockLocation . "',
								'" . $Item->BundleRef . "',
								" . $Item->BundleQty . ")";

							$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
							$DbgMsg =  _('The following SQL to update the serial stock item record was used');
							$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
						}


						/* now insert the serial stock movement */

						$SQL = "INSERT INTO stockserialmoves (stockmoveno,
											stockid,
											serialno,
											moveqty)
								VALUES (" . $StkMoveNo . ",
									'" . $this->StockID . "',
									'" . $Item->BundleRef . "',
									" . $Item->BundleQty . ")";
						$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
						$DbgMsg =  _('The following SQL to insert the serial stock movement records was used');
						$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

					}/* foreach controlled item in the serialitems array */
				} /*end if the adjustment item is a controlled item */



				$SQL = "UPDATE locstock SET quantity = quantity + " . $adjustQty . "
						WHERE stockid='" . $this->StockID . "'
						AND loccode='" . $this->StockLocation . "'";

				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
				$DbgMsg = _('The following SQL to update the stock record was used');

				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

				$Result = DB_Txn_Commit($db);

				prnMsg( _('A stock adjustment for'). ' ' . $this->StockID . ' '._('has been created from location').' ' . $this->StockLocation .' '. _('for a quantity of') . ' ' . $adjustQty,'success');

				//unset ($_SESSION['Adjustment']);
			} /* end if there was no input error */				
		
		}
}

Class SalesExchange{
	var $ActiveLine;
	var $LineDetials;
	var $Linenumber;
	var $NewLineEdit;
	
	function SalesExchange(){
		$this->ActiveLine = new SalesExLineDetails();
		$this->Linenumber = 0;
		$this->NewLineEdit = false;
	}
	
	function getSalesExItemDetails($exno){
		
		global $db;		
		if(isset($exno) and $exno!=''){
			$sql = "SELECT salesexchange.exno,
					salesexchange.customerid,
					salesexchange.stkcode,
					salesexchange.qty,
					salesexchange.returndate,
					salesexchange.qtyexchanged,
					salesexchange.exchangedate,
					salesexchange.confirmman,
					salesexchange.reference,
					salesexchange.remark,
					salesexchange.completed
				FROM salesexchange
				WHERE salesexchange.exno = '". $exno ."'
				ORDER BY salesexchange.exno";
			
			$ErrMsg = _('There is a problem selecting the part records to display because');
			$DbgMsg = _('The SQL statement that failed was');
			$SearchResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

			if (DB_num_rows($SearchResult)==0 && $debug==1){
				prnMsg( _('There are no products to display matching the criteria provided'),'warn');
			}
			if (DB_num_rows($SearchResult)==1){
				$myrow=DB_fetch_array($SearchResult);
			}
		}	
		
		//$this->LineDetials = new SalesExLineDetails();
		$this->ActiveLine->FillSalesExLineDetails($myrow['exno'],$myrow['customerid'],$myrow['stkcode'],
			$myrow['qty'],$myrow['returndate'],$myrow['confirmman'],$myrow['qtyexchanged'],$myrow['exchangedate'],$myrow['reference'],'',0 );
	}
	
	function addSalesExItem( $customerid, $stkcode, $qty, $returndate, $confirmman, $reference){
	
		global $db;
		$sql = "INSERT INTO salesexchange (
							customerid,
							stkcode,
							qty,
							returndate,
							confirmman,
							reference)
						VALUES(
							" . $this->customerid . ",
							" . $this->stkcode . ",
							" . $this->qty . ",
							" . $this->returndate . ",'
							" . $this->confirmman . "','
							" . $this->reference . "')";
		$result = DB_query($sql,
					$db ,
					_('The exchange for') . ' ' . $this->customerid  . ' ' ._('could not be inserted'));
		
		if($result){
			$this->Linenumber = $linenumber + 1;
		    return 1;
		}

		return 0;
	}

	function updateSalesExQty( $exno, $rtndate, $qty){
		$this->exno = $exno;
		$this->qty = $qty;
		$this->rtndate = $rtndate;
	
		global $db;
		
		$sql = "UPDATE salesexchange SET
			qty = '" . $this->qty . "',
			returndate = '" . $this->rtndate ."'
			WHERE exno  = '" . $this->exno ."'  ";
			
		$result = DB_query($sql,
					$db ,
					_('The exchange for') . ' ' . $this->exno  . ' ' ._('could not be inserted'));
		
		if($result){
			$this->linenumber = $linenumber + 1;
		    return 1;
		}

		return 0;
	}
	
	function updateSalesExExchangeQty( $exno, $exqty, $remark, $completed ){
		$this->ExNo = $exno;
		$this->TotalQtyExchanged = $exqty;
		$this->LastExDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));
		$this->Remark = $remark;
		$this->Completed = $completed;
		//echo 'Date is:'.$this->LastExDate;
	
		global $db;		
		
		$sql = "UPDATE salesexchange SET
			qtyexchanged = qtyexchanged+'" . $this->TotalQtyExchanged . "',
			exchangedate = '" . $this->LastExDate . "',
			completed = '" . $this->Completed  . "',
			remark = '" . $this->Remark . "'			
			WHERE exno  = '" . $this->ExNo . "' ";
			
		$result = DB_query($sql,
					$db ,
					_('The exchange for') . ' ' . $this->exno  . ' ' ._('could not be inserted'));
		
		if($result){
		    return 1;
		}

		return 0;
	}		
	
	function removeSalesEx($exno, $customerid, $stkcode, $qty){
		//varify the salesexchange hasn't been sent.
		if($qtyexchanged == 0){
								
		}
	}	
	
}

Class SalesExLineDetails{
	var $ExNo;
	var $CustomerID;
	var $StkCode;
	var $Qty;
	var $ReturnDate;
	var $ConfirmMan;
	var $TotalQtyExchanged;
	var $LastExDate;
	var $Reference;
	var $Remark;
	var $Completed;
	
	function SalesExLineDetails(){
	}
	
	function FillSalesExLineDetails(
		$exno            ,
		$customerid      ,
		$stkcode         ,
		$qty             ,
		$returndate      ,
		$confirmman      ,
		$qtyexchanged    ,
		$exchangedate    ,
		$reference       ,
		$remark          ,
		$completed){	     
			$this->ExNo             =     $exno;
			$this->CustomerID       =     $customerid;
			$this->StkCode          =     $stkcode;
			$this->Qty              =     $qty;
			$this->ReturnDate       =     $returndate;
			$this->ConfirmMan       =     $confirmman;
			$this->TotalQtyExchanged=     $qtyexchanged;
			$this->LastExDate     =     $exchangedate;
			$this->Reference        =     $reference;
			$this->Remark           =     $remark;
			$this->Completed	    =     $completed;
		}	
			
}

Class TestFailureTable{
	var $DateStart; 
	var $DateEnd;    
	var $LastAdjuMonth;
	var $TFLineItems;
	var $ActiveLine;
	var $TFLineCount;
	
	function TestFailureTable(){
	    $this->TFLineItems = array();
		$this->ActiveLine = new TestFailLineItems();
		//Set the period from this year's JAN01 to Nev31 by default
		$this->DateStart = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,1,1,Date('y')));
		$this->DateEnd   = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,12,31,Date('y')));
		$this->LastAdjuMonth = 1; //set to 1st month when initialize
		//$this->TFLineCount = 0;
	
		global $db;	
		$SearchString = '%';
		$sql = 'SELECT stockmaster.stockid,
				stockmaster.description,
				stockmaster.units
			FROM stockmaster INNER JOIN stockcategory
			ON stockmaster.categoryid=stockcategory.categoryid
			WHERE stockmaster.mbflag!='."'D'".'
			and stockmaster.mbflag!='."'A'".'
			and stockmaster.mbflag!='."'K'".'
			and stockmaster.discontinued!=1
			and stockmaster.description ' . LIKE . "'$SearchString'
			ORDER BY stockmaster.stockid";
		
		$ErrMsg = _('There is a problem selecting the part records to display because');
		$DbgMsg = _('The SQL statement that failed was');
		$SearchResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

		if (DB_num_rows($SearchResult)==0 && $debug==1){
			prnMsg( _('There are no products to display matching the criteria provided'),'warn');
		}
		
		if (DB_num_rows($SearchResult)==1){

			$myrow=DB_fetch_array($SearchResult);
			DB_data_seek($SearchResult,0);
		}
		
		$lineCount = 0;	
		while($myrow = DB_fetch_array($SearchResult)){
			$this->TFLineItems[$lineCount] = new TestFailLineItems();
			$this->TFLineItems[$lineCount]->StockCode = $myrow[0];
			$this->TFLineCount = $lineCount++;
		}
	}
	
	function getItemFailArray($ItmCode){		
		$DateAfterCriteria = FormatDateforSQL($this->DateStart);	
		$DateBeforeCriteria = FormatDateforSQL($this->DateEnd);
		
		$strYear = date('Y');
		$referenceMark = '#Manf(TestFail)-'.$strYear; // This is the Mark of TestFail, stockmoves.reference;
		unset ($this->ActiveLine);
		$this->ActiveLine->MonthQtyArray = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	
		global $db;		
		if(isset($ItmCode) and $ItmCode!=''){
			$sql = "SELECT  stockmoves.stkmoveno,
					stockmoves.stockid,						
					stockmoves.reference, 
					stockmoves.qty,
					stockmoves.narrative
				FROM stockmoves
				WHERE stockmoves.trandate >= '$DateAfterCriteria' 
				AND stockmoves.trandate <= '$DateBeforeCriteria'
				AND stockmoves.stockid = '$ItmCode'
				AND stockmoves.reference " . LIKE . " '%" . $referenceMark . "%' 
				AND stockmoves.type = 17						
				GROUP BY stockmoves.stkmoveno					
				ORDER BY stockmoves.stkmoveno";
			
			$ErrMsg = _('There is a problem selecting the part records to display because');
			$DbgMsg = _('The SQL statement that failed was');
			$SearchResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);						

			if (DB_num_rows($SearchResult)==0 && $debug==1){
				prnMsg( _('There are no products to display matching the criteria provided'),'warn');
			}		
			
			if (DB_num_rows($SearchResult) >=1 ){
				while ($myrow=DB_fetch_array($SearchResult)){
					if( $myrow[1] != $ItmCode ){
						prnMsg( _('Error:'),'warn');
					} else{					
						//Get month from reference
						$DateEntry = substr($myrow[2], strlen('#Manf(TestFail)-'), 10);
						$month = (int)substr($DateEntry,4,2);
						//echo 'Monthend:'.$month;

						if( $month >0 and $month <13 ){
							if( $month > $this->LastAdjuMonth ){
								$this->LastAdjuMonth = $month;
								//echo 'Adjuuuu'.$this->LastAdjuMonth;
							}
							//echo "Hello,world:";
							$this->ActiveLine->StockCode = $myrow[1];
							$this->ActiveLine->MonthQtyArray[$month] = -$myrow[3]; // convert stock qty as positive
							//echo $this->ActiveLine->MonthQtyArray[3];
						}
					}						
				}
			}		
		}			
	}
}

Class TestFailLineItems{
	var $StockCode;
	var $QtyOnHand;
	var $MonthQtyArray;
	
	function TestFailLineItems(){
		//$this->MonthQtyArray = array('Year'=>0, 'Jan'=>0, 'Feb'=>0, 'Mar'=>0, 'Apr'=>0, 'May'=>0, 'Jun'=>0,
		//								      'Jul'=>0, 'Aug'=>0, 'Sep'=>0, 'Oct'=>0, 'Dec'=>0, 'Nov'=>0);
		$this->MonthQtyArray = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);											  

	}
}	

