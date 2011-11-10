<?php

/* $Revision: 1.38 $ */
	// Titles and screen header
	// Needs the file config.php loaded where the variables are defined for
	//  $rootpath
	//  $title - should be defined in the page this file is included with

	/*The module link codes are hard coded in a switch statement below to determine the options to show for each tab */
	$ModuleLink = array('orders', 'AR', 'AP', 'PO', 'stock', 'manuf', 'GL', 'system');
	/*The headings showing on the tabs accross the main index used also in WWW_Users for defining what should be visible to the user */
	$ModuleList = array(_('| Sales |'), _('| Receivables |'), _('| Payables |'), _('| Purchases |'), _('| Inventory |'), _('| Manufacturing |'), _('| General Ledger |'), _('| Setup |'));	
	$ModuleRef = array('A_SalesHome.php','A_FinanceHome.php', 'index.php?&Application=AP', 'index.php?&Application=PO', 
				'index.php?&Application=stock', 'A_ManufHome.php', 'index.php?&Application=GL', 'index.php?&Application=system');
	
	if (!headers_sent()){
		//header('Content-type: text/html; charset=' . _('iso-8859-1'));
		header('Content-type: text/html; charset=' . _('utf8'));
	}
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

	echo '<html xmlns="http://www.w3.org/1999/xhtml"><head><title>' . $title . '</title>';
	echo '<link rel="shortcut icon" href="'. $rootpath.'/favicon.ico" />';
	echo '<link rel="icon" href="' . $rootpath.'/favicon.ico" />';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=' . _('utf8') . '" />';
	echo '<link href="'.$rootpath. '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
	echo '<script type="text/javascript" src = "'.$rootpath.'/javascripts/MiscFunctions.js"></script>';
		
	echo "<script>
	function openSubpage(subpageURL){
	window.open(subpageURL, 'location=no', 'scrollbars=yes', 'resizable=yes');
	//window.alert(subpageURL);
	}
	
	function openParaSubpage(subpageURL,para,paraValue){
	subpageURL = subpageURL + para  + paraValue; 
	window.open(subpageURL, 'location=no', 'scrollbars=yes', 'resizable=yes');
	//window.alert(subpageURL);
	}
	
	function refreshParent(){
		window.opener.location.href = window.opener.location.href;
		if (window.opener.progressWindow)
		{
			window.opener.progressWindow.close();
		}
		window.close();
	}
	</script>";		
	echo '</head>';

	echo '<body>';
	
	echo '<header>';
	echo '<img src="' . $rootpath.'/logo.jpg" alt="serverLogo" /></br>';
	
	echo '<div id="myMenu">';
	echo '<a href="' . $rootpath . '/UserSettings.php?' . SID . '">' . _('Welcome') . ': ' . stripslashes($_SESSION['UsersRealName']) . '!</a>';
	echo '<a href="' . $rootpath . '/Logout.php?' . SID . '" onclick="return confirm(\'' . _('Are you sure you wish to logout?') . '\');">[Logout]</a>';
	echo 'My menu >>';
	if (count($_SESSION['AllowedPageSecurityTokens'])!=1){
	  /* Security settings DO allow seeing the main menu */
		$i=0;
		while ($i < count($ModuleLink)){
			// This determines if the user has display access to the module see config.php and header.inc
			// for the authorisation and security code
			if ($_SESSION['ModulesEnabled'][$i]==1)	{

				// If this is the first time the application is loaded then it is possible that
				// SESSION['Module'] is not set if so set it to the first module that is enabled for the user
				if (!isset($_SESSION['Module'])OR $_SESSION['Module']==''){
					$_SESSION['Module']=$ModuleLink[$i];
				}
				if ($ModuleLink[$i] == $_SESSION['Module']){
					//echo "<A HREF='". $_SERVER['PHP_SELF'] .'?'. SID . '&Application='. $ModuleLink[$i] ."'>". $ModuleList[$i] .'</A>';
					echo '<A href='.$ModuleRef[$i].'>'. $ModuleList[$i] .'</A>';
				} else {
					//echo "<A HREF='". $_SERVER['PHP_SELF'] .'?'. SID . '&Application='. $ModuleLink[$i] ."'>". $ModuleList[$i] .'</A>';
					echo '<A href='.$ModuleRef[$i].'>'. $ModuleList[$i] .'</A>';
				}
			}
			$i++;
		}
	}		
	echo '</div>';
	echo '</header>';
	
	$_SESSION['Shortcuts']['Link1'] = '<a href="' .  $rootpath . '/index.php?' . SID . '">' . _('My Home') . '</a></br>';
	$_SESSION['Shortcuts']['Link2'] = '<a href="' .  $rootpath . '/SelectProduct.php?' . SID . '">' . _('Products') . '</a></br>';
	$_SESSION['Shortcuts']['Link3'] = '<a href="' .  $rootpath . '/SelectCustomer.php?' . SID . '">' . _('Customers') . '</a></br>';
	$_SESSION['Shortcuts']['Link4'] = '<a href="' .  $rootpath . '/SelectSupplier.php?' . SID . '">' . _('Suppliers') . '</a>';
	
	echo '<nav></br>';
		echo '<a href=' .$rootpath . '/index.php? ><img src=' . $rootpath.'/css/iconhome.png alt=iconhome /></a>'.' '.' ';	
		echo '<a href=' .$rootpath . '/SelectProduct.php? ><img src=' . $rootpath.'/css/iconitems.png alt=iconitems /></a> '.' ';		
		echo '<a href=' .$rootpath . '/SelectCustomer.php? ><img src=' . $rootpath.'/css/iconcustomers.png alt=serverLogo /> </a>'.' ';
		echo '<a href=' .$rootpath . '/SelectSupplier.php? ><img src=' . $rootpath.'/css/iconsuppliers.png alt=iconsuppliers /></a>'.' ';		
	echo 'Shortcuts:</br>';
	echo $_SESSION['Shortcuts']['Link1'].' '.$_SESSION['Shortcuts']['Link2'].' '.$_SESSION['Shortcuts']['Link3'].' '.$_SESSION['Shortcuts']['Link4'];
	echo '</nav>';
	echo '<section>';
	
?>