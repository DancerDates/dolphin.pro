<?php
require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
if ( !( $logged['admin'] = member_auth( 1, false ) ) )
	if ( !( $logged['member'] = member_auth( 0, true ) ) )
		if ( !( $logged['aff'] = member_auth( 2, false )) )
			$logged['moderator'] = member_auth( 3, false );
			
require_once('OpenInviter/frontend.php');
$_page['name_index']=1;
$_page['header'] = _t( "Invite your friends" );
$_page['header_text'] = _t( "Invite your friends." );
$_page_cont[1]['page_main_code']=$contents;
PageCode();
?>