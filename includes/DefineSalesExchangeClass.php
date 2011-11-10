<?php
/* $Revision: 1.6 $ */
/* definition of the Sales exchange class */

Class SalesExchangeStock {
	var $exno;
	var $customerid;
	var $stkcode;
	var $qty;
	var $returndate;
	var $confirmman;
	var $qtyexchanged;
	var $qtysentthistime;
	var $exchangedate;
	var $reference;
	var $remark;
	var $completed;
	
	var $linenumber;
	
	function SalesExchangeStock(){
	}
	
	function add_salesexchange( $exno, $customerid, $stkcode, $qty, $returndate, $confirmman, $reference){
	
		global $db;
		$sql = "INSERT INTO salesexchange (exno,
							customerid,
							stkcode,
							qty,
							returndate,
							confirmman,
							reference)
						VALUES(" . $this->exno . ",
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
			$this->linenumber = $linenumber + 1;
		    return 1;
		}

		return 0;
	}

	function update_salesexchange( $exno, $customerid, $stkcode, $qty, $returndate, $confirmman, $reference){
	
		global $db;
		$sql = "UPDATE salesexchange (exno,
							customerid,
							stkcode,
							qty,
							returndate,
							confirmman,
							reference)
						VALUES(" . $this->exno . ",
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
			$this->linenumber = $linenumber + 1;
		    return 1;
		}

		return 0;
	}	
	
	
	function remove_salesexchange($exno, $customerid, $stkcode, $qty){
		//varify the salesexchange hasn't been sent.
		if($qtyexchanged == 0){
			
			
			
		}
	}
		
}










































?>
