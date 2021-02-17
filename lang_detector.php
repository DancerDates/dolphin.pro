<?php
set_time_limit(9999999);
/***************************************************************************

*                            Dolphin Smart Community Builder

*                              -----------------

*     begin                : Mon Mar 23 2006

*     copyright            : (C) 2006 BoonEx Group

*     website              : http://www.boonex.com/

* This file is part of Dolphin - Smart Community Builder

*

* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 

* http://creativecommons.org/licenses/by/3.0/

*

* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;

* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

* See the Creative Commons Attribution 3.0 License for more details. 

* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 

* see license.txt file; if not, write to marketing@boonex.com

***************************************************************************/
 
require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'languages.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
 


//echo "START .....<br /><br />";

echo "<b>The following languages exists on your Site :</b>";
echo "<br /><br />";
 
$res = db_res("SELECT * FROM `sys_localization_languages` ORDER BY `ID`");
while($arr= mysql_fetch_array($res) ) 
{
	$sName = $arr['Name'];
	$sTitle = $arr['Title'];
 	
	if($sName=="en"){
		echo "{$sTitle} - <b>{$sName}.php</b> exists already. Copy this file to create the others";
		echo "<br /><br />";
	}else{
		echo "{$sTitle} - Create a file called <b>{$sName}.php</b>";
		echo "<br /><br />";
	} 
}


INSERT INTO `sys_pre_values` (`Key`, `Value`, `Order`, `LKey`, `LKey2`, `LKey3`, `Extra`, `Extra2`, `Extra3`) VALUES
('SMSCarrier', '<phone>@tms.suncom.com', 10, 'Triton', 'Triton', '', '', '', ''),
('SMSCarrier', '<phone>@utext.com', 10, 'Unicel', 'Unicel', '', '', '', ''),
('SMSCarrier', '<phone>@email.uscc.net', 10, 'US Cellular', 'US Cellular', '', '', '', ''),
('SMSCarrier', '<phone>@txt.bell.ca', 10, 'Solo Mobile', 'Solo Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@messaging.sprintpcs.com', 10, 'Sprint', 'Sprint', '', '', '', ''),
('SMSCarrier', '<phone>@tms.suncom.com', 10, 'Sumcom', 'Sumcom', '', '', '', ''),
('SMSCarrier', '<phone>@mobile.surewest.com', 10, 'Surewest Communicaitons', 'Surewest Communicaitons', '', '', '', ''),
('SMSCarrier', '<phone>@tmomail.net', 10, 'T-Mobile', 'T-Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@msg.telus.com', 10, 'Telus', 'Telus', '', '', '', ''),
('SMSCarrier', '<phone>@tms.suncom.com', 10, 'Triton', 'Triton', '', '', '', ''),
('SMSCarrier', '<phone>@utext.com', 10, 'Unicel', 'Unicel', '', '', '', ''),
('SMSCarrier', '<phone>@uswestdatamail.com', 10, 'US West', 'US West', '', '', '', ''),
('SMSCarrier', '<phone>@vtext.com', 10, 'Verizon', 'Verizon', '', '', '', ''),
('SMSCarrier', '<phone>@vmobl.com', 10, 'Virgin Mobile', 'Virgin Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@vmobile.ca', 10, 'Virgin Mobile Canada', 'Virgin Mobile Canada', '', '', '', ''),
('SMSCarrier', '<phone>@sms.wcc.net', 10, 'West Central Wireless', 'West Central Wireless', '', '', '', ''),
('SMSCarrier', '<phone>@cellularonewest.com', 10, 'Western Wireless', 'Western Wireless', '', '', '', ''),
('SMSCarrier', '<phone>@rpgmail.net', 10, 'Chennai RPG Cellular', 'Chennai RPG Cellular', '', '', '', ''),
('SMSCarrier', '<phone>@airtelchennai.com', 10, 'Chennai Skycell/Airtel', 'Chennai Skycell/Airtel', '', '', '', ''),
('SMSCarrier', '<phone>@sms.comviq.se', 10, 'Comviq', 'Comviq', '', '', '', ''),
('SMSCarrier', '<phone>@airtelmail.com', 10, 'Delhi Aritel', 'Delhi Aritel', '', '', '', ''),
('SMSCarrier', '<phone>@delhi.hutch.co.in', 10, 'Delhi Hutch', 'Delhi Hutch', '', '', '', ''),
('SMSCarrier', '<phone>@t-mobile-sms.de', 10, 'DT T-Mobile', 'DT T-Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@sms.orange.nl', 10, 'Dutchtone/Orange-NL', 'Dutchtone/Orange-NL', '', '', '', ''),
('SMSCarrier', '<phone>@sms.emt.ee', 10, 'EMT', 'EMT', '', '', '', ''),
('SMSCarrier', '<phone>@escotelmobile.com', 10, 'Escotel', 'Escotel', '', '', '', ''),
('SMSCarrier', '<phone>@t-mobile-sms.de', 10, 'German T-Mobile', 'German T-Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@bplmobile.com', 10, 'Goa BPLMobil', 'Goa BPLMobil', '', '', '', ''),
('SMSCarrier', '<phone>@sms.goldentele.com', 10, 'Golden Telecom', 'Golden Telecom', '', '', '', ''),
('SMSCarrier', '<phone>@celforce.com', 10, 'Gujarat Celforce', 'Gujarat Celforce', '', '', '', ''),
('SMSCarrier', '<phone>@jsmtel.com', 10, 'JSM Tele-Page', 'JSM Tele-Page', '', '', '', ''),
('SMSCarrier', '<phone>@escotelmobile.com', 10, 'Kerala Escotel', 'Kerala Escotel', '', '', '', ''),
('SMSCarrier', '<phone>@airtelkol.com', 10, 'Kolkata Airtel', 'Kolkata Airtel', '', '', '', ''),
('SMSCarrier', '<phone>@smsmail.lmt.lv', 10, 'Kyivstar', 'Kyivstar', '', '', '', ''),
('SMSCarrier', '<phone>@e-page.net', 10, 'Lauttamus Communication', 'Lauttamus Communication', '', '', '', ''),
('SMSCarrier', '<phone>@smsmail.lmt.lv', 10, 'LMT', 'LMT', '', '', '', ''),
('SMSCarrier', '<phone>@bplmobile.com', 10, 'Maharashtra BPL Mobile', 'Maharashtra BPL Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@ideacellular.net', 10, 'Maharashtra Idea Cellular', 'Maharashtra Idea Cellular', '', '', '', ''),
('SMSCarrier', '<phone>@text.mtsmobility.com', 10, 'Manitoba Telecom Systems', 'Manitoba Telecom Systems', '', '', '', ''),
('SMSCarrier', '<phone>@mymeteor.ie', 10, 'Meteor', 'Meteor', '', '', '', ''),
('SMSCarrier', '<phone>@m1.com.sg', 10, 'MiWorld', 'MiWorld', '', '', '', ''),
('SMSCarrier', '<phone>@m1.com.sg', 10, 'Mobileone', 'Mobileone', '', '', '', ''),               
('SMSCarrier', '<phone>@page.mobilfone.com', 10, 'Mobilfone', 'Mobilfone', '', '', '', ''),  
('SMSCarrier', '<phone>@ml.bm', 10, 'Mobility Bermuda', 'Mobility Bermuda', '', '', '', ''),
('SMSCarrier', '<phone>@mobistar.be', 10, 'Mobistar Belgium', 'Mobistar Belgium', '', '', '', ''),
('SMSCarrier', '<phone>@sms.co.tz', 10, 'Mobitel Tanzania', 'Mobitel Tanzania', '', '', '', ''),
('SMSCarrier', '<phone>@mobtel.co.yu', 10, 'Mobtel Srbija', 'Mobtel Srbija', '', '', '', ''),
('SMSCarrier', '<phone>@correo.movistar.net', 10, 'Movistar', 'Movistar', '', '', '', ''),
('SMSCarrier', '<phone>@bplmobile.com', 10, 'Mumbai BPL Mobile', 'Mumbai BPL Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@sms.netcom.no', 10, 'Netcom', 'Netcom', '', '', '', ''),
('SMSCarrier', '<phone>@pcs.ntelos.com', 10, 'Ntelos', 'Ntelos', '', '', '', ''),
('SMSCarrier', '<phone>@o2.co.uk', 10, 'O2', 'O2', '', '', '', ''),
('SMSCarrier', '<phone>@o2imail.co.uk', 10, 'O2', 'O2', '', '', '', ''),
('SMSCarrier', '<phone>@mmail.co.uk', 10, 'O2 (M-mail)', 'O2 (M-mail)', '', '', '', ''),
('SMSCarrier', '<phone>@onemail.at', 10, 'One Connect Austria', 'One Connect Austria', '', '', '', ''),
('SMSCarrier', '<phone>@onlinebeep.net', 10, 'OnlineBeep', 'OnlineBeep', '', '', '', ''),
('SMSCarrier', '<phone>@optusmobile.com.au', 10, 'Optus Mobile', 'Optus Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@orange.net', 10, 'Orange', 'Orange', '', '', '', ''),
('SMSCarrier', '<phone>@orangemail.co.in', 10, 'Orange Mumbai', 'Orange Mumbai', '', '', '', ''),
('SMSCarrier', '<phone>@sms.orange.nl', 10, 'Orange  NL/Dutchtone', 'Orange  NL/Dutchtone', '', '', '', ''),
('SMSCarrier', '<phone>@mujoskar.cz', 10, 'Oskar', 'Oskar', '', '', '', ''),
('SMSCarrier', '<phone>@sms.lu', 10, 'P&T Luxembourg', 'P&T Luxembourg', '', '', '', ''),
('SMSCarrier', '<phone>@pcom.ru', 10, 'Personal Communication', 'Personal Communication', '', '', '', ''),
('SMSCarrier', '<phone>@bplmobile.com', 10, 'Pondicherry BPL Mobile', 'Pondicherry BPL Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@sms.primtel.ru', 10, 'Primtel', 'Primtel', '', '', '', ''),
('SMSCarrier', '<phone>@safaricomsms.com', 10, 'Safaricom', 'Safaricom', '', '', '', ''),
('SMSCarrier', '<phone>@satelindogsm.com', 10, 'Satelindo GSM', 'Satelindo GSM', '', '', '', ''),
('SMSCarrier', '<phone>@scs-900.ru', 10, 'SCS-900', 'SCS-900', '', '', '', ''),
('SMSCarrier', '<phone>@sfr.fr', 10, 'SFR France', 'SFR France', '', '', '', ''),
('SMSCarrier', '<phone>@text.simplefreedom.net', 10, 'Simple Freedom', 'Simple Freedom', '', '', '', ''),
('SMSCarrier', '<phone>@mysmart.mymobile.ph', 10, 'Smart Telecom', 'Smart Telecom', '', '', '', ''),
('SMSCarrier', '<phone>@page.southernlinc.com', 10, 'Southern LINC', 'Southern LINC', '', '', '', ''),
('SMSCarrier', '<phone>@mysunrise.ch', 10, 'Sunrise Mobile', 'Sunrise Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@swmsg.com', 10, 'Sunrise Mobile', 'Sunrise Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@freesurf.ch', 10, 'Surewest Communications', '', '', '', ''),
('SMSCarrier', '<phone>@bluewin.ch', 10, 'Swisscom', 'Swisscom', '', '', '', ''),
('SMSCarrier', '<phone>@sms.t-mobile.at', 10, 'T-Mobile Austria', 'T-Mobile Austria', '', '', '', ''),
('SMSCarrier', '<phone>@t-d1-sms.de', 10, 'T-Mobile Germany', 'T-Mobile Germany', '', '', '', ''),
('SMSCarrier', '<phone>@t-mobile.uk.net', 10, 'T-Mobile UK', 'T-Mobile UK', '', '', '', ''),
('SMSCarrier', '<phone>@bplmobile.com', 10, 'Tamil Nadu BPL Mobile', 'Tamil Nadu BPL Mobile', '', '', '', ''),
('SMSCarrier', '<phone>@sms.tele2.lv', 10, 'Tele2 Latvia', 'Tele2 Latvia', '', '', '', ''),
('SMSCarrier', '<phone>@movistar.net', 10, 'Telefonica Movistar', 'Telefonica Movistar', '', '', '', ''),
('SMSCarrier', '<phone>@mobilpost.no', 10, 'Telenor', 'Telenor', '', '', '', ''),
('SMSCarrier', '<phone>@pageme.teletouch.com', 10, 'Teletouch', 'Teletouch', '', '', '', ''),
('SMSCarrier', '<phone>@gsm1800.telia.dk', 10, 'Telia Denmark', 'Telia Denmark', '', '', '', ''),
('SMSCarrier', '<phone>@timnet.com', 10, 'TIM', 'TIM', '', '', '', ''),
('SMSCarrier', '<phone>@alphame.com', 10, 'TSR Wireless', 'TSR Wireless', '', '', '', ''),
('SMSCarrier', '<phone>@sms.umc.com.ua', 10, 'UMC', 'UMC', '', '', '', ''),
('SMSCarrier', '<phone>@sms.uraltel.ru', 10, 'Uraltel', 'Uraltel', '', '', '', ''),
('SMSCarrier', '<phone>@escotelmobile.com', 10, 'Uttar Pradesh Escotel', 'Uttar Pradesh Escotel', '', '', '', ''),
('SMSCarrier', '<phone>@pager.irkutsk.ru', 10, 'Vessotel', 'Vessotel', '', '', '', ''),
('SMSCarrier', '<phone>@sms.vodafone.it', 10, 'Vodafone Italy', 'Vodafone Italy', '', '', '', ''),
('SMSCarrier', '<phone>@c.vodafone.ne.jp', 10, 'Vodafone Japan', 'Vodafone Japan', '', '', '', ''),
('SMSCarrier', '<phone>@h.vodafone.ne.jp', 10, 'Vodafone Japan', 'Vodafone Japan', '', '', '', ''),
('SMSCarrier', '<phone>@t.vodafone.ne.jp', 10, 'Vodafone Japan', 'Vodafone Japan', '', '', '', ''),
('SMSCarrier', '<phone>@vodafone.net', 10, 'Vodafone UK', 'Vodafone UK', '', '', '', ''),
('SMSCarrier', '<phone>@wyndtell.com', 10, 'Wyndtell', 'Wyndtell', '', '', '', ''),
('SMSCarrier', '<phone>@advantagepaging.com', 10, 'Advantage Communications', 'Advantage Communications', '', '', '', ''),
('SMSCarrier', '<phone>@myairmail.com', 10, 'Airtouch Pagers', 'Airtouch Pagers', '', '', '', ''),
('SMSCarrier', '<phone>@alphanow.net', 10, 'AlphaNow', 'AlphaNow', '', '', '', ''),
('SMSCarrier', '<phone>@paging.acswireless.com', 10, 'Ameritech Paging', 'Ameritech Paging', '', '', '', ''),
('SMSCarrier', '<phone>@page.americanmessaging.net', 10, 'American Messaging', 'American Messaging', '', '', '', ''),
('SMSCarrier', '<phone>@clearpath.acswireless.com', 10, 'Ameritech Clearpath', 'Ameritech Clearpath', '', '', '', ''),
('SMSCarrier', '<phone>@archwireless.net', 10, 'Arch Pagers (PageNet)', 'Arch Pagers (PageNet)', '', '', '', ''),
('SMSCarrier', '<phone>@mobile.att.net', 10, 'AT&T', 'AT&T', '', '', '', ''),
('SMSCarrier', '<phone>@mmode.com', 10, 'AT&T Free2Go', 'AT&T Free2Go', '', '', '', ''),
('SMSCarrier', '<phone>@mobile.att.net', 10, 'AT&T PCS', 'AT&T PCS', '', '', '', ''),
('SMSCarrier', '<phone>@dpcs.mobile.att.net', 10, 'AT&T Pocketnet PCS', 'AT&T Pocketnet PCS', '', '', '', ''),
('SMSCarrier', '<phone>@beepwear.net', 10, 'Beepwear', 'Beepwear', '', '', '', ''),
('SMSCarrier', '<phone>@message.bam.com', 10, 'Bell Atlantic', 'Bell Atlantic', '', '', '', ''),
('SMSCarrier', '<phone>@wireless.bellsouth.com', 10, 'Bell South', 'Bell South', '', '', '', ''),
('SMSCarrier', '<phone>@bellsouthtips.com', 10, 'Bell South (Blackberry)', 'Bell South (Blackberry)', '', '', '', ''),
('SMSCarrier', '<phone>@blsdcs.net', 10, 'Bell South Mobility', 'Bell South Mobility', '', '', '', ''),
('SMSCarrier', '<phone>@phone.cellone.net', 10, 'Cellular One (East Coast)', 'Cellular One (East Coast)', '', '', '', ''),
('SMSCarrier', '<phone>@swmsg.com', 10, 'Cellular One (South West)', 'Cellular One (South West)', '', '', '', ''),
('SMSCarrier', '<phone>@cellularone.txtmsg.com', 10, 'Cellular One', 'Cellular One', '', '', '', ''),
('SMSCarrier', '<phone>@cellularone.textmsg.com', 10, 'Cellular One', 'Cellular One', '', '', '', ''),
('SMSCarrier', '<phone>@cell1.textmsg.com', 10, 'Cellular One', 'Cellular One', '', '', '', ''),
('SMSCarrier', '<phone>@sbcemail.com', 10, 'Cellular One', 'Cellular One', '', '', '', ''),
('SMSCarrier', '<phone>@mycellone.com', 10, 'Cellular One (West)', 'Cellular One (West)', '', '', '', ''),
('SMSCarrier', '<phone>@cvcpaging.com', 10, 'Central Vermont Communications', 'Central Vermont Communications', '', '', '', ''),
('SMSCarrier', '<phone>@cingularme.com', 10, 'Cingular', 'Cingular', '', '', '', ''),
('SMSCarrier', '<phone>@pageme.comspeco.net', 10, 'Communication Specialists', 'Communication Specialists', '', '', '', ''),
('SMSCarrier', '<phone>@cookmail.com', 10, 'Cook Paging', 'Cook Paging', '', '', '', ''),
('SMSCarrier', '<phone>@corrwireless.net', 10, 'Corr Wireless Communications', 'Corr Wireless Communications', '', '', '', ''),
('SMSCarrier', '<phone>@page.hit.net', 10, 'Digi-Page/Page Kansas', 'Digi-Page/Page Kansas', '', '', '', ''),
('SMSCarrier', '<phone>@sendabeep.net', 10, 'Galaxy Corporation', 'Galaxy Corporation', '', '', '', ''),
('SMSCarrier', '<phone>@webpager.us', 10, 'GCS Paging', 'GCS Paging', '', '', '', ''),
('SMSCarrier', '<phone>@epage.porta-phone.com', 10, 'GrayLink/Porta-Phone', 'GrayLink/Porta-Phone', '', '', '', ''),
('SMSCarrier', '<phone>@gte.pagegate.net', 10, 'GTE', 'GTE', '', '', '', ''),
('SMSCarrier', '<phone>@page.infopagesystems.com', 10, 'Infopage Systems', 'Infopage Systems', '', '', '', ''),
('SMSCarrier', '<phone>@inlandlink.com', 10, 'Indiana Paging Co', 'Indiana Paging Co', '', '', '', ''),
('SMSCarrier', '<phone>@pagemci.com', 10, 'MCI', 'MCI', '', '', '', ''),
('SMSCarrier', '<phone>@page.metrocall.com', 10, 'Metrocall', 'Metrocall', '', '', '', ''),
('SMSCarrier', '<phone>@page.mobilcom.net', 10, 'Mobilecom PA', 'Mobilecom PA', '', '', '', ''),
('SMSCarrier', '<phone>@beepone.net', 10, 'Morris Wireless', 'Morris Wireless', '', '', '', ''),
('SMSCarrier', '<phone>@isp.com', 10, 'Motient', 'Motient', '', '', '', ''),
('SMSCarrier', '<phone>@page.nextel.com', 10, 'Nextel', 'Nextel', '', '', '', ''),
('SMSCarrier', '<phone>@omnipointpcs.com', 10, 'Omnipoint', 'Omnipoint', '', '', '', ''),
('SMSCarrier', '<phone>@pacbellpcs.net', 10, 'Pacific Bell', 'Pacific Bell', '', '', '', ''),
('SMSCarrier', '<phone>@pagemart.net', 10, 'PageMart', 'PageMart', '', '', '', ''),
('SMSCarrier', '<phone>@pmcl.net', 10, 'PageMart Canada', 'PageMart Canada', '', '', '', ''),
('SMSCarrier', '<phone>@pagegate.pagenet.ca', 10, 'PageNet Canada', 'PageNet Canada', '', '', '', ''),
('SMSCarrier', '<phone>@page1nw.com', 10, 'PageOne Northwest', 'PageOne Northwest', '', '', '', ''),
('SMSCarrier', '<phone>@pcsone.net', 10, 'PCS One', 'PCS One', '', '', '', ''),
('SMSCarrier', '<phone>@voicestream.net', 10, 'Powertel', 'Powertel', '', '', '', ''),
('SMSCarrier', '<phone>@mobilecell1se.com', 10, 'Price Communications', 'Price Communications', '', '', '', ''),
('SMSCarrier', '<phone>@email.uscc.net', 10, 'Primeco', 'Primeco', '', '', '', ''),
('SMSCarrier', '<phone>@page.propage.net', 10, 'ProPage', 'ProPage', '', '', '', ''),
('SMSCarrier', '<phone>@pager.qualcomm.com', 10, 'Qualcomm', 'Qualcomm', '', '', '', ''),
('SMSCarrier', '<phone>@ram-page.com', 10, 'RAM Page', 'RAM Page', '', '', '', ''),
('SMSCarrier', '<phone>@paging.acswireless.com', 10, 'SBC Ameritech Paging', 'SBC Ameritech Paging', '', '', '', ''),
('SMSCarrier', '<phone>@email.skytel.com', 10, 'Skytel Pagers', 'Skytel Pagers', '', '', '', ''),
('SMSCarrier', '<phone>@page.stpaging.com', 10, 'ST Paging', 'ST Paging', '', '', '', ''),
('SMSCarrier', '<phone>@myairmail.com', 10, 'Verizon Pagers', 'Verizon Pagers', '', '', '', ''),
('SMSCarrier', '<phone>@myvzw.com', 10, 'Verizon PCS', 'Verizon PCS', '', '', '', ''),           
('SMSCarrier', '<phone>@voicestream.net', 10, 'VoiceStream', 'VoiceStream', '', '', '', ''),
('SMSCarrier', '<phone>@airmessage.net', 10, 'WebLink Wireless', 'WebLink Wireless', '', '', '', ''),
('SMSCarrier', '<phone>@pagemart.net', 10, 'WebLink Wireless', 'WebLink Wireless', '', '', '', ''),
('SMSCarrier', '<phone>@sms.wcc.net', 10, 'West Central Wireless', 'West Central Wireless', '', '', '', '');
