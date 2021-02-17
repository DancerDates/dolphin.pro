<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by Rayz Expert. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Rayz Expert.
* This notice may not be removed from the source code.
*
***************************************************************************/

$aInfo = array(
    'mode' => "paid",
    'title' => "Internet Photos 3.0 Trial",
    'version' => "3.0.0002",
    'code' => "flickr_3.0.0000_free",
    'author' => "Rayz",
    'authorUrl' => "http://rayzzz.com/redirect.php?action=author"
);
$aModules = array(
    'browse' => array(
        'caption' => 'Internet Photos Browse',
        'parameters' => array('user', 'password'),
        'js' => array(),
        'inline' => false,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 650),
                                'minSize' => array('width' => 755, 'height' => 650),
        'div' => array(
            'style' => array(
                'margin' => '10px'
            )
        )
    ),
    'user' => array(
        'caption' => 'Internet Photos Featured',
        'parameters' => array('mode', 'user', 'password', 'member'),
        'js' => array(),
        'inline' => false,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 650),
                                'minSize' => array('width' => 755, 'height' => 650),
        'div' => array(
            'style' => array(
                'margin' => '10px'
            )
        )
    ),
    'search' => array(
        'caption' => 'Internet Photos Search',
        'parameters' => array('user', 'password'),
        'js' => array(),
        'inline' => false,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => "100%", 'height' => 650),
                                'minSize' => array('width' => 755, 'height' => 650),
        'div' => array(
            'style' => array(
                'margin' => '10px'
            )
        )
    ),
    'admin' => array(
        'caption' => 'Internet Photos Admin',
        'parameters' => array('nick', 'password'),
        'js' => array(),
        'inline' => false,
        'vResizable' => false,
        'hResizable' => false,
        'reloadable' => true,
        'layout' => array('top' => 0, 'left' => 0, 'width' => 755, 'height' => 550),
                                'minSize' => array('width' => 755, 'height' => 550),
        'div' => array()
    )
);
