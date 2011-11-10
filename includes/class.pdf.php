<?php
/*
	This class is an extension to the fpdf class using a syntax that the original reports were written in
	(the R &OS pdf.php class) - due to limitation of this class for foreign character support this wrapper class
	was written to allow the same code base to use the more functional fpdf.class by Olivier Plathey
	
*	Wrapper for use R&OSpdf API with fpdf.org class
*	Janusz Dobrowolski <janusz@iron.from.pl>
*	David Luo <davidluo188@yahoo.com.cn>
	extended for Chinese/Japanese/Korean support by Phil Daintree
	
	Chinese GB&BIG5 support by Edward Yang <edward.yangcn@gmail.com>
	
*/

define('FPDF_FONTPATH','./fonts/');
include ('includes/fpdf.php');

if ($_SESSION['Language']=='zh_CN' OR $_SESSION['Language']=='zh_HK' OR $_SESSION['Language']=='zh_TW' OR $_SESSION['Language'] == 'zh_TW.UTF-8'){
	include('FPDF_Chinese.php');
//	if($_SESSION['Language'] == 'zh_TW.UTF-8')
		define ('FPDF_UNICODE_ENCODING', 'UCS-2BE');
} elseif ($_SESSION['Language']=='ja_JP'){
	include('FPDF_Japanese.php');
}elseif ($_SESSION['Language']=='ko_KR'){
	include('FPDF_Korean.php');
} else {
	class PDF_Language extends FPDF {
	}
}

class Cpdf extends PDF_Language {

		var $charset;     // input charset. User must add proper fonts by add font functions like AddUniCNShwFont
  		var $isUnicode;   // whether charset belongs to Unicode
  	
	
	function Cpdf($pageSize=array(0,0,612,792)) {
	
		$this->PDF_Language( 'P', 'pt',array($pageSize[2]-$pageSize[0],$pageSize[3]-$pageSize[1]));
		$this->setAutoPageBreak(0);
		$this->AddPage();
	
		$this->SetLineWidth(1);
	
		$this->cMargin = 0;
			
		// Next three lines should be here for any fonts genarted with 'makefont' utility
		if($_SESSION['Language'] == 'zh_TW.UTF-8' or $_SESSION['Language']=='zh_CN' )
		{
			
    		$this->charset = 'UTF8';
   			$this->isUnicode = in_array ($this->charset, array ('UTF8', 'UTF16', 'UCS2'));
   			
   			//$this->AddUniCNShwFont('uni'); 
			$this->AddUniGBhwFont('uni');
   		
		}
		elseif ($_SESSION['Language']=='zh_TW' or $_SESSION['Language']=='zh_HK'){
			$this->AddBig5Font();
		//} elseif ($_SESSION['Language']=='zh_CN'){
		//	$this->AddGBFont();
		}elseif ($_SESSION['Language']=='ja_JP'){
			$this->AddSJISFont();
		}elseif ($_SESSION['Language']=='ko_KR'){
			$this->AddUHCFont();
		} else {
		//	$this->AddFont('helvetica');
		//	$this->AddFont('helvetica','I');
		//	$this->AddFont('helvetica','B');
		}
	}
	
	function selectFont($FontName) {
		
		$type = '';
		if(strpos($FontName, 'Oblique')) {
			$type = 'I';
		}
		if(strpos($FontName, 'Bold')) {
			$type = 'B';
		}
		if($_SESSION['Language'] == 'zh_TW.UTF-8' or $_SESSION['Language']=='zh_CN')
		{
			$FontName = 'uni';
		}
		elseif ($_SESSION['Language']=='zh_TW' or $_SESSION['Language']=='zh_HK'){
			$FontName = 'Big5';
		//} elseif ($_SESSION['Language']=='zh_CN'){
		//	$FontName = 'GB';
		} elseif ($_SESSION['Language']=='ja_JP'){
			$FontName = 'SJIS';
		} elseif ($_SESSION['Language']=='ko_KR'){
			$FontName = 'UHC';
		} else {
			$FontName ='helvetica';
		}
		$this->SetFont($FontName, $type);
	}
	
	function newPage() {
		$this->AddPage();
	}
	
	function line($x1,$y1,$x2,$y2) {
		FPDF::line($x1, $this->h-$y1, $x2, $this->h-$y2);
	}
	
	function addText($xb,$yb,$size,$text)//,$angle=0,$wordSpaceAdjust=0) 
															{
		$text = html_entity_decode($text);
		$this->SetFontSize($size);
		$this->Text($xb, $this->h-$yb, $text);
	}
	
	function addInfo($label,$value){
		if($label=='Title') {
			$this->SetTitle($value);
		} 
		if ($label=='Subject') {
			$this->SetSubject($value);
		}
		if($label=='Creator') {
			// The Creator info in source is not exactly it should be ;) 
			$value = str_replace( "ros.co.nz", "fpdf.org", $value );
			$value = str_replace( "R&OS", "", $value );
			$this->SetCreator( $value );
		}
		if($label=='Author') {
			$this->SetAuthor($value);
		}
	}
	
	function addJpegFromFile($img,$x,$y,$w=0,$h=0){
		$this->Image($img, $x, $this->h-$y-$h, $w, $h);
	}
	
	/*
	* Next Two functions are adopted from R&OS pdf class
	*/
	
	/**
	* draw a part of an ellipse
	*/
	function partEllipse($x0,$y0,$astart,$afinish,$r1,$r2=0,$angle=0,$nSeg=8) {
		$this->ellipse($x0,$y0,$r1,$r2,$angle,$nSeg,$astart,$afinish,0);
	}
	
	/**
	* draw an ellipse
	* note that the part and filled ellipse are just special cases of this function
	*
	* draws an ellipse in the current line style
	* centered at $x0,$y0, radii $r1,$r2
	* if $r2 is not set, then a circle is drawn
	* nSeg is not allowed to be less than 2, as this will simply draw a line (and will even draw a 
	* pretty crappy shape at 2, as we are approximating with bezier curves.
	*/
	function ellipse($x0,$y0,$r1,$r2=0,$angle=0,$nSeg=8,$astart=0,$afinish=360,$close=1,$fill=0) {
		
		if ($r1==0){
		return;
		}
		if ($r2==0){
		$r2=$r1;
		}
		if ($nSeg<2){
		$nSeg=2;
		}
		
		$astart = deg2rad((float)$astart);
		$afinish = deg2rad((float)$afinish);
		$totalAngle =$afinish-$astart;
		
		$dt = $totalAngle/$nSeg;
		$dtm = $dt/3;
		
		if ($angle != 0){
		$a = -1*deg2rad((float)$angle);
		$tmp = "\n q ";
		$tmp .= sprintf('%.3f',cos($a)).' '.sprintf('%.3f',(-1.0*sin($a))).' '.sprintf('%.3f',sin($a)).' '.sprintf('%.3f',cos($a)).' ';
		$tmp .= sprintf('%.3f',$x0).' '.sprintf('%.3f',$y0).' cm';
		$x0=0;
		$y0=0;
		}
		
		$t1 = $astart;
		$a0 = $x0+$r1*cos($t1);
		$b0 = $y0+$r2*sin($t1);
		$c0 = -$r1*sin($t1);
		$d0 = $r2*cos($t1);
		
		$tmp.="\n".sprintf('%.3f',$a0).' '.sprintf('%.3f',$b0).' m ';
		for ($i=1;$i<=$nSeg;$i++){
		// draw this bit of the total curve
		$t1 = $i*$dt+$astart;
		$a1 = $x0+$r1*cos($t1);
		$b1 = $y0+$r2*sin($t1);
		$c1 = -$r1*sin($t1);
		$d1 = $r2*cos($t1);
		$tmp.="\n".sprintf('%.3f',($a0+$c0*$dtm)).' '.sprintf('%.3f',($b0+$d0*$dtm));
		$tmp.= ' '.sprintf('%.3f',($a1-$c1*$dtm)).' '.sprintf('%.3f',($b1-$d1*$dtm)).' '.sprintf('%.3f',$a1).' '.sprintf('%.3f',$b1).' c';
		$a0=$a1;
		$b0=$b1;
		$c0=$c1;
		$d0=$d1;    
		}
		if ($fill){
		//$this->objects[$this->currentContents]['c']
		$tmp.=' f';
		} else {
		if ($close){
		$tmp.=' s'; // small 's' signifies closing the path as well
		} else {
		$tmp.=' S';
		}
		}
		if ($angle !=0){
		$tmp .=' Q';
		}
		$this->_out($tmp);
	}
	
	function Stream() {
	$this->Output('','I');
	}
	
	function addTextWrap($xb, $yb, $w, $h, $txt, $align='J', $border=0, $fill=0) {
		$txt = html_entity_decode($txt);
		$this->x = $xb;
		$this->y = $this->h - $yb - $h;
		
		switch($align) {
			case 'right':
			$align = 'R'; break;
			case 'center':    
			$align = 'C'; break;
			default: 
			$align = 'L';
		
		}
		$this->SetFontSize($h);
		$cw=&$this->CurrentFont['cw'];
		if($w==0) {
			$w=$this->w-$this->rMargin-$this->x;
		}
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$s=str_replace("\n",' ',$s);
		$s = trim($s).' ';
		$nb=strlen($s);
		$b=0;
		if ($border) {
			if ($border==1) {
				$border='LTRB';
				$b='LRT';
				$b2='LR';
			} else {
				$b2='';
				if(is_int(strpos($border,'L'))) {
					$b2.='L';
				}
				if(is_int(strpos($border,'R'))) {
					$b2.='R';
				}
				$b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
			}
		}
		$sep=-1;
		$i=0;
		$l= $ls=0;
		$ns=0;
		while($i<$nb) {
		
			$c=$s{$i};
		
			if($c==' ' AND $i>0) {
				$sep=$i;
				$ls=$l;
				$ns++;
			}
			$l+=$cw[$c];
			if($l>$wmax)
			break;
			else 
			$i++;
		}
		if($sep==-1) {
			if($i==0) $i++;
			
			if($this->ws>0) {
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$sep = $i;
		} else {
			if($align=='J') {
			$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
				$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
			}	
		}
		
		$this->Cell($w,$h,substr($s,0,$sep),$b,2,$align,$fill);
		$this->x=$this->lMargin;
		
		return substr($s,$sep);
	}
		function AddUniCNShwFont ($family='Uni', $name='PMingLiU')  // name for Kai font is DFKai-SB
		  {
		    //Add Unicode font with half-witdh Latin, character code must be utf16be
		    for($i=32;$i<=126;$i++)
		      $cw[chr($i)]=500;
		    $CMap='UniCNS-UCS2-H';  // for compatible with PDF 1.3 (Adobe-CNS1-0), 1.4 (Adobe-CNS1-3), 1.5 (Adobe-CNS1-3)
		    //$CMap='UniCNS-UTF16-H';  // for compatible with 1.5 (Adobe-CNS1-4)
		    $registry=array('ordering'=>'CNS1','supplement'=>0);
		    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
		  }

		  function AddUniCNSFont ($family='Uni', $name='PMingLiU')  
		  {
		    //Add Unicode font with propotional Latin, character code must be utf16be
		    $cw=$GLOBALS['Big5_widths'];
		    $CMap='UniCNS-UCS2-H';
		    $registry=array('ordering'=>'CNS1','supplement'=>0);
		    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
		  }

		  function AddUniGBhwFont ($family='uGB', $name='AdobeSongStd-Light-Acro')//'AdobeSongStd-Light')  
		  {
		    //Add Unicode font with half-witdh Latin, character code must be utf16be
		    for($i=32;$i<=126;$i++)
		      $cw[chr($i)]=500;
		    $CMap='UniGB-UCS2-H';  
		    $registry=array('ordering'=>'GB1','supplement'=>4);
		    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
		  }

		  function AddUniGBFont ($family='uGB', $name='AdobeSongStd-Light')  
		  {
		    //Add Unicode font with propotional Latin, character code must be utf16be
		    $cw=$GLOBALS['GB_widths'];
		    $CMap='UniGB-UCS2-H';
		    $registry=array('ordering'=>'GB1','supplement'=>4);
		    $this->AddCIDFonts($family,$name,$cw,$CMap,$registry);
		  }

		  // redefinition of FPDF functions

		  function GetStringWidth ($s)
		  {
		    //Get width of a string in the current font
		    if ($this->isUnicode) {
		      $txt = mb_convert_encoding ($s, FPDF_UNICODE_ENCODING, $this->charset);
		      $oEnc = mb_internal_encoding();
		      mb_internal_encoding (FPDF_UNICODE_ENCODING);
		      $w = $this->GetUniStringWidth ($txt);
		      mb_internal_encoding ($oEnc);
		      return $w;
		    } else
		      return parent::GetStringWidth($s);
		  }

		  function Text ($x, $y, $txt)
		  {
		    if ($this->isUnicode) {
		      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
		      $oEnc = mb_internal_encoding();
		      mb_internal_encoding (FPDF_UNICODE_ENCODING);
		      $this->UniText ($x, $y, $txt);
		      mb_internal_encoding ($oEnc);
		    } else 
		      parent::Text ($x, $y, $txt);
		  }

		  function Cell ($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
		  {
		    if ($this->isUnicode) {
		    	  	
		      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
		      $oEnc = mb_internal_encoding();
		      mb_internal_encoding (FPDF_UNICODE_ENCODING);		   
		      $this->UniCell ($w, $h, $txt, $border, $ln, $align, $fill, $link);
		      mb_internal_encoding ($oEnc);
		    } else 
		      parent::Cell ($w, $h, $txt, $border, $ln, $align, $fill, $link);
		  }

		  function MultiCell ($w,$h,$txt,$border=0,$align='J',$fill=0)
		  {
		    if ($this->isUnicode) {
		      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
		      $oEnc = mb_internal_encoding();
		      mb_internal_encoding (FPDF_UNICODE_ENCODING);
		      $this->UniMultiCell ($w, $h, $txt, $border, $align, $fill);
		      mb_internal_encoding ($oEnc);
		    } else {
		      parent::MultiCell ($w, $h, $txt, $border, $align, $fill);
		    }
		  }

		  function Write ($h,$txt,$link='')
		  {
		    if ($this->isUnicode) {
		      $txt = mb_convert_encoding ($txt, FPDF_UNICODE_ENCODING, $this->charset);
		      $oEnc = mb_internal_encoding();
		      mb_internal_encoding (FPDF_UNICODE_ENCODING);
		      $this->UniWrite ($h, $txt, $link);
		      mb_internal_encoding ($oEnc);
		    } else {
		      parent::Write ($h, $txt, $link);
		    }
		  }

		  // implementation in Unicode version 

		  function GetUniStringWidth ($s)
		  {
		    //Unicode version of GetStringWidth()
		    $l=0;
		    $cw=&$this->CurrentFont['cw'];
		    $nb=mb_strlen($s);
		    $i=0;
		    while($i<$nb) {
		      $c=mb_substr($s,$i,1);
		      $ord = hexdec(bin2hex($c));
		      if($ord<128) {
			$l+=$cw[chr($ord)];
		      } else {
			$l+=1000;
		      }
		      $i++;
		    }
		    return $l*$this->FontSize/1000;
		  }

		  function UniText ($x, $y, $txt)
		  {
		    // copied from parent::Text but just modify the line below
		    $s=sprintf('BT %.2f %.2f Td <%s> Tj ET',$x*$this->k,($this->h-$y)*$this->k, bin2hex($txt));

		    if($this->underline && $txt!='')
		      $s.=' '.$this->_dounderline($x,$y,$txt);
		    if($this->ColorFlag)
		      $s='q '.$this->TextColor.' '.$s.' Q';
		    $this->_out($s);
		  }

		  function UniCell ($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
		  {
		    // copied from parent::Text but just modify the line with an output "BT %.2f %.2f Td <%s> Tj ET" ...
		    $k=$this->k;
		    if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
		      {
			//Automatic page break
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0)
			  {
			    $this->ws=0;
			    $this->_out('0 Tw');
			  }
			$this->AddPage($this->CurOrientation);
			$this->x=$x;
			if($ws>0)
			  {
			    $this->ws=$ws;
			    $this->_out(sprintf('%.3f Tw',$ws*$k));
			  }
		      }
		    if($w==0)
		      $w=$this->w-$this->rMargin-$this->x;
		    $s='';
		    if($fill==1 || $border==1)
		      {
			if($fill==1)
			  $op=($border==1) ? 'B' : 'f';
			else
			  $op='S';
			$s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		      }
		    if(is_string($border))
		      {
			$x=$this->x;
			$y=$this->y;
			if(strpos($border,'L')!==false)
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			if(strpos($border,'T')!==false)
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
			if(strpos($border,'R')!==false)
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			if(strpos($border,'B')!==false)
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		      }
		    if($txt!=='')
		      {
			if($align=='R')
			  $dx=$w-$this->cMargin-$this->GetUniStringWidth($txt);
			elseif($align=='C')
			  $dx=($w-$this->GetUniStringWidth($txt))/2;
			else
			  $dx=$this->cMargin;
			if($this->ColorFlag)
			  $s.='q '.$this->TextColor.' ';
			$s.=sprintf('BT %.2f %.2f Td <%s> Tj ET',($this->x+$dx)*$k,
				    ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,bin2hex($txt));
			if($this->underline)
			  $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			if($this->ColorFlag)
			  $s.=' Q';
			if($link)
			  $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetUniStringWidth($txt),$this->FontSize,$link);
		      }
		    if($s)
		      $this->_out($s);
		    $this->lasth=$h;
		    if($ln>0)
		      {
			//Go to next line
			$this->y+=$h;
			if($ln==1)
			  $this->x=$this->lMargin;
		      }
		    else
		      $this->x+=$w;
		     
		  }

		  function UniMultiCell($w,$h,$txt,$border=0,$align='L',$fill=0)
		  {
		    //Unicode version of MultiCell()

		    $enc = mb_internal_encoding();

		    $cw=&$this->CurrentFont['cw'];
		    if($w==0)
		      $w=$this->w-$this->rMargin-$this->x;
		    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		    $s = $txt;
		    $nb=mb_strlen($s);
		    if ($nb>0 && mb_substr($s,-1)==mb_convert_encoding("\n", $enc, $this->charset))
		      $nb--;
		    $b=0;
		    if($border)
		      {
			if($border==1)
			  {
			    $border='LTRB';
			    $b='LRT';
			    $b2='LR';
			  }
			else
			  {
			    $b2='';
			    if(is_int(strpos($border,'L')))
			      $b2.='L';
			    if(is_int(strpos($border,'R')))
			      $b2.='R';
			    $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
			  }
		      }
		    $sep=-1;
		    $i=0;
		    $j=0;
		    $l=0;
		    $nl=1;
		    while($i<$nb)
		      {
			//Get next character
			$c=mb_substr($s,$i,1);
			$ord = hexdec(bin2hex($c));
			$ascii = ($ord < 128);
			if($c==mb_convert_encoding("\n", $enc, $this->charset))
			  {
			    //Explicit line break
			    $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
			    $i++;
			    $sep=-1;
			    $j=$i;
			    $l=0;
			    $nl++;
			    if($border && $nl==2)
			      $b=$b2;
			    continue;
			  }
			if(!$ascii || $c==mb_convert_encoding(' ', $enc, $this->charset))
			  {
			    $sep=$i;
			    $ls=$l;
			  }
			$l+=$ascii ? $cw[chr($ord)] : 1000;
			if($l>$wmax)
			  {
			    //Automatic line break
			    if($sep==-1 || $i==$j)
			      {
				if($i==$j)
				  $i++; //=$ascii ? 1 : 2;
				$this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
			      }
			    else
			      {
				$this->UniCell($w,$h,mb_substr($s,$j,$sep-$j),$b,2,$align,$fill);
				$i=(mb_substr($s,$sep,1)==mb_convert_encoding(' ', $enc, $this->charset)) ? $sep+1 : $sep;
			      }
			    $sep=-1;
			    $j=$i;
			    $l=0;
			    $nl++;
			    if($border && $nl==2)
			      $b=$b2;
			  }
			else
			  $i++; //=$ascii ? 1 : 2;
		      }
		    //Last chunk
		    if($border && is_int(strpos($border,'B')))
		      $b.='B';
		    $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),$b,2,$align,$fill);
		    $this->x=$this->lMargin;
		  }

		  function UniWrite($h,$txt,$link='')
		  {
		    //Unicode version of Write()
		    $enc = mb_internal_encoding();
		    $cw=&$this->CurrentFont['cw'];
		    $w=$this->w-$this->rMargin-$this->x;
		    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		    $s = $txt;

		    $nb=mb_strlen($s);
		    $sep=-1;
		    $i=0;
		    $j=0;
		    $l=0;
		    $nl=1;
		    while($i<$nb)
		      {
			//Get next character
			$c=mb_substr($s,$i,1);
			//Check if ASCII or MB
			$ord = hexdec(bin2hex($c));
			$ascii=($ord < 128);
			if($c==mb_convert_encoding("\n", $enc, $this->charset))
			  {
			    //Explicit line break
			    $this->UniCell($w,$h,mb_substr($s,$j,$i-$j),0,2,'',0,$link);
			    $i++;
			    $sep=-1;
			    $j=$i;
			    $l=0;
			    if($nl==1)
			      {
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			      }
			    $nl++;
			    continue;
			  }
			if(!$ascii || $c==mb_convert_encoding(' ', $enc, $this->charset))
			  $sep=$i;
			$l+=$ascii ? $cw[chr($ord)] : 1000;
			if($l>$wmax)
			  {
			    //Automatic line break
			    if($sep==-1 || $i==$j)
			      {
				if($this->x>$this->lMargin)
				  {
				    //Move to next line
				    $this->x=$this->lMargin;
				    $this->y+=$h;
				    $w=$this->w-$this->rMargin-$this->x;
				    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				    $i++;
				    $nl++;
				    continue;
				  }
				if($i==$j)
				  $i++; //=$ascii ? 1 : 2;
				$this->UniCell($w,$h,mb_substr($s,$j,$i-$j),0,2,'',0,$link);
			      }
			    else
			      {
				$this->UniCell($w,$h,mb_substr($s,$j,$sep-$j),0,2,'',0,$link);
				$i=(mb_substr($s,$sep,1)==mb_convert_encoding(' ', $enc, $this->charset)) ? $sep+1 : $sep;
			      }
			    $sep=-1;
			    $j=$i;
			    $l=0;
			    if($nl==1)
			      {
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			      }
			    $nl++;
			  }
			else
			  $i++; //=$ascii ? 1 : 2;
		      }
		    //Last chunk
		    if($i!=$j)
		      $this->UniCell($l/1000*$this->FontSize,$h,mb_substr($s,$j,$i-$j),0,0,'',0,$link);
		  }	
} // end of class

?>
