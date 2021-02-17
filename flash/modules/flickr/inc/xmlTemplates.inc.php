<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

$aXmlTemplates = array (
    "element" => array (
        //2 => '<element id="#1#" isCategory="true"><title><![CDATA[#2#]]></title></element>',
        //5 => '<element id="#1#" image="#2#" elements="#3#" isCategory="true"><title><![CDATA[#4#]]></title><description><![CDATA[#5#]]></description></element>',
        6 => '<element id="#1#" image="#2#" param="#5#" isCategory="true"><title><![CDATA[#3#]]></title><description><![CDATA[#4#]]></description><value><![CDATA[#6#]]></value></element>',
        7 => '<element id="#1#" image="#2#" elements="#7#" param="#5#" isCategory="true"><title><![CDATA[#3#]]></title><description><![CDATA[#4#]]></description><value><![CDATA[#6#]]></value></element>',
        15 => '<element id="#1#" user="#2#" author="#3#" image="#4#" play="#5#" save="#6#" user="#7#" date="#8#" rating="#9#" voted="#10#" vote="#11#" favorite="#12#" views="#13#"><title><![CDATA[#14#]]></title><tags><![CDATA[#15#]]></tags></element>'
    ),
    
    "vote" => array (
        2 => '<vote rating="#1#" voted="#2#" />'
    ),
    
    "result" => array (
        1 => '<result value="#1#" />',
        2 => '<result value="#1#" status="#2#" />',
        5 => '<result value="#1#" status="#2#" page="#3#" itemsPerPage="#4#" itemsAll="#5#" />'
    )
);
