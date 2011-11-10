<?php
/* $Revision: 1.5 $ */
/* Steve Kitchen */
/* Up front menu for language file maintenance */

$PageSecurity = 15;

include ('includes/session.inc');

$title = _('UTILITY PAGE') . ' ' . _('that helps maintain language files');

include('includes/header.inc');

/* check if we have gettext - we're useless without it ... */

if (!function_exists('gettext')){
	prnMsg (_('gettext is not installed on this system') . '. ' . _('You cannot use the language files without it'),'error');
	exit;
}

if (!is_writable('./locale/' . $_SESSION['Language'])) {
	prnMsg(_('You do not have write access to the required files please contact your system administrator'),'error');
}
else
{
	echo '<P><A HREF="' . $rootpath . '/Z_poRebuildDefault.php?' . SID . '">'.  _('Rebuild the System Default Language File') . '</A>';
	echo '<P><A HREF="' . $rootpath . '/Z_poAddLanguage.php?' . SID . '">' . _('Add a New Language to the System') .'</A>';
	echo '<P><A HREF="' . $rootpath . '/Z_poEditLangHeader.php?' . SID . '">'. _('Edit a Language File Header') . '</A>';
	echo '<P><A HREF="' . $rootpath . '/Z_poEditLangModule.php?' . SID . '">'. _('Edit a Language File Module') . '</A>';
	echo '<P><A HREF="' . $rootpath . '/Z_poEditLangRemaining.php?' . SID . '">'. _('Edit Remaining Strings For This Language') . '</A>';
	echo '<P><A HREF="' . $rootpath . '/locale/'.$_SESSION['Language'].'/LC_MESSAGES/messages.po' . SID . '">'. _('Download messages.po file') . '</A>';
	echo '<P><A HREF="' . $rootpath . '/locale/'.$_SESSION['Language'].'/LC_MESSAGES/messages.mo' . SID . '">'. _('Download messages.mo file') . '</A>';
}

include('includes/footer.inc');

?>
