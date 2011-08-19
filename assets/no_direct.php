<?php
	/* This file stops people from directly accessing a file that requires this file in the top of it.
	 *		<?php require_once(ASSETS.'/no_direct.php'); ?>
	 *		If the file wasn't included from the index.php page, then ASSETS won't be defined and
	 *		thus no_direct.php won't be able to be included which will throw a fatal error stopping
	 *		the requested page from executing.
	 * 
	 */
?>