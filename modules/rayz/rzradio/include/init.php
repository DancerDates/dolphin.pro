<?php

/**
 * @file
 * Module initialization class.
 */

class RzradioInit {
  /**
   * Module details and info.
   */
  public static $aRzInfo = array(
    'module' => "rzradio",
    'title' => "Radio",
    'desc' => "Radio Browser",
    'version' => "1.0.0",
    'author' => "rayzzz.com",
    'email' => "rayzexpert@gmail.com",
    'url' => "http://rayzzz.com/redirect.php?action=about&widget=radio&target=rz",
    'min_width' => "800",
    'width' => "100%",
    'height' => "600",
  );
  /**
   * Module installation tables details.
   */
  public static $aDBTables = array(
    'rzradio_elements' => array(
      'fields' => array(
        'ID' => array(
          'type' => 'int',
          'not null' => TRUE,
          'auto_increment' => TRUE,
          'length' => 20,
        ),
        'User' => array(
          'type' => 'varchar',
          'not null' => TRUE,
          'default' => '',
          'length' => 36,
        ),
        'Title' => array(
          'type' => 'varchar',
          'not null' => TRUE,
          'default' => '',
          'length' => 255,
        ),
        'PlaylistUrl' => array(
          'type' => 'varchar',
          'not null' => TRUE,
          'default' => '',
          'length' => 255,
        ),
        'Stream' => array(
          'type' => 'varchar',
          'not null' => TRUE,
          'default' => '',
          'length' => 255,
        ),
        'Featured' => array(
          'type' => 'int',
          'not null' => TRUE,
          'default' => 0,
          'unsigned' => TRUE,
          'length' => 4,
        ),
        'Category' => array(
          'type' => 'int',
          'not null' => TRUE,
          'default' => 0,
          'unsigned' => TRUE,
          'length' => 4,
        ),
        'Parent' => array(
          'type' => 'int',
          'not null' => TRUE,
          'default' => 0,
          'unsigned' => TRUE,
          'length' => 11,
        ),
        'Ord' => array(
          'type' => 'int',
          'not null' => TRUE,
          'default' => 0,
          'unsigned' => TRUE,
          'length' => 11,
        ),
      ),
      'primary key' => array('ID'),
    ),

    'rzradio_favorites' => array(
      'fields' => array(
        'ID' => array(
          'type' => 'int',
          'not null' => TRUE,
          'auto_increment' => TRUE,
          'length' => 20,
        ),
        'User' => array(
          'type' => 'varchar',
          'not null' => TRUE,
          'default' => '',
          'length' => 36,
        ),
        'Element' => array(
          'type' => 'int',
          'not null' => TRUE,
          'default' => 0,
          'unsigned' => TRUE,
          'length' => 11,
        ),
      ),
      'primary key' => array('ID'),
    ),
  );
  /**
   * Module installation tables values.
   */
  public static $aDBInserts = array(
    0 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("1", "Alternative", "1", "1"),
    ),
    1 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category"),
      'values' => array("2", "Blues", "Blues", "2", "1"),
    ),
    2 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("3", "Classical", "3", "1"),
    ),
    3 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("4", "Country", "4", "1"),
    ),
    4 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("5", "Decades", "5", "1"),
    ),
    5 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("6", "Easy Listening", "6", "1"),
    ),
    6 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("7", "Electronic", "7", "1"),
    ),
    7 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category"),
      'values' => array("8", "Folk", "Folk", "8", "1"),
    ),
    8 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("9", "Inspirational", "9", "1"),
    ),
    9 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("10", "International", "10", "1"),
    ),
    10 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("11", "Jazz", "11", "1"),
    ),
    11 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("12", "Latin", "12", "1"),
    ),
    12 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("13", "Metal", "13", "1"),
    ),
    13 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category"),
      'values' => array("14", "Misc", "Misc", "14", "1"),
    ),
    14 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category"),
      'values' => array("15", "New Age", "Newage", "15", "1"),
    ),
    15 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("16", "Pop", "16", "1"),
    ),
    16 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("17", "Public Radio", "17", "1"),
    ),
    17 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("18", "R&B and Urban", "18", "1"),
    ),
    18 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("19", "Rap", "19", "1"),
    ),
    19 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category"),
      'values' => array("20", "Reggae", "Reggae", "20", "1"),
    ),
    20 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("21", "Rock", "21", "1"),
    ),
    21 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category"),
      'values' => array("22", "Soundtracks", "Soundtracks", "22", "1"),
    ),
    22 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Ord", "Category"),
      'values' => array("23", "Talk", "23", "1"),
    ),
    23 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("24", "Britpop", "Britpop", "1", "1", "1"),
    ),
    24 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("25", "College", "College", "2", "1", "1"),
    ),
    25 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("26", "Dream Pop", "Dreampop", "3", "1", "1"),
    ),
    26 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("27", "Emo", "Emo", "4", "1", "1"),
    ),
    27 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("28", "Goth", "Goth", "5", "1", "1"),
    ),
    28 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("29", "Grunge", "Grunge", "6", "1", "1"),
    ),
    29 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("30", "Hardcore", "Hardcore", "7", "1", "1"),
    ),
    30 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("31", "Indie", "Indie", "8", "1", "1"),
    ),
    31 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("32", "Industrial", "Industrial", "9", "1", "1"),
    ),
    32 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("33", "LoFi", "Lofi", "10", "1", "1"),
    ),
    33 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("34", "Wave", "Wave", "11", "1", "1"),
    ),
    34 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("35", "Noise", "Noise", "12", "1", "1"),
    ),
    35 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("36", "Punk", "Punk", "13", "1", "1"),
    ),
    36 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("37", "Ska", "Ska", "14", "1", "1"),
    ),
    37 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("38", "Xtreme", "Xtreme", "15", "1", "1"),
    ),
    38 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("39", "Baroque", "Baroque", "1", "1", "3"),
    ),
    39 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("40", "Opera", "Opera", "2", "1", "3"),
    ),
    40 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("41", "Piano", "Piano", "3", "1", "3"),
    ),
    41 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("42", "Romantic", "Romantic", "4", "1", "3"),
    ),
    42 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("43", "Symphony", "Symphony", "5", "1", "3"),
    ),
    43 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("44", "Americana", "Americana", "1", "1", "4"),
    ),
    44 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("45", "Bluegrass", "Bluegrass", "2", "1", "4"),
    ),
    45 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("46", "Classic Country", "Country", "3", "1", "4"),
    ),
    46 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("47", "Honky Tonk", "Honky", "4", "1", "4"),
    ),
    47 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("48", "Western", "Western", "5", "1", "4"),
    ),
    48 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("49", "00s", "00s", "1", "1", "5"),
    ),
    49 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("50", "40s", "40s", "2", "1", "5"),
    ),
    50 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("51", "50s", "50s", "3", "1", "5"),
    ),
      51 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("52", "60s", "60s", "4", "1", "5"),
    ),
    52 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("53", "70s", "70s", "5", "1", "5"),
    ),
    53 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("54", "80s", "80s", "6", "1", "5"),
    ),
    54 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("55", "90s", "90s", "7", "1", "5"),
    ),
    55 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("56", "Lounge", "Lounge", "1", "1", "6"),
    ),
    56 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("57", "Space", "Space", "2", "1", "6"),
    ),
    57 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("58", "Acid", "Acid", "1", "1", "7"),
    ),
    58 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("59", "Ambient", "Ambient", "2", "1", "7"),
    ),
    59 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("60", "Breakbeat", "Breakbeat", "3", "1", "7"),
    ),
    60 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("61", "Dance", "Dance", "4", "1", "7"),
    ),
    61 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("62", "Demo", "Demo", "5", "1", "7"),
    ),
    62 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("63", "Disco", "Disco", "6", "1", "7"),
    ),
    63 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("64", "Downtempo", "Downtempo", "7", "1", "7"),
    ),
    64 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("65", "Drum and Bass", "Drum", "8", "1", "7"),
    ),
    65 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("66", "Dubstep", "Dubstep", "9", "1", "7"),
    ),
    66 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("67", "Electro", "Electro", "10", "1", "7"),
    ),
    67 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("68", "Garage", "Garage", "11", "1", "7"),
    ),
    68 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("69", "House", "House", "12", "1", "7"),
    ),
    69 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("70", "Jungle", "Jungle", "13", "1", "7"),
    ),
    70 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("71", "Progressive", "Progressive", "14", "1", "7"),
    ),
    71 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("72", "Techno", "Techno", "15", "1", "7"),
    ),
    72 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("73", "Trance", "Trance", "16", "1", "7"),
    ),
    73 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("74", "Trip Hop", "Trip", "17", "1", "7"),
    ),
    74 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("75", "Christian", "Christian", "1", "1", "9"),
    ),
    75 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("76", "Gospel", "Gospel", "2", "1", "9"),
    ),
    76 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("77", "Praise and Worship", "Worship", "3", "1", "9"),
    ),
    77 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("78", "Southern Gospel", "Southern", "4", "1", "9"),
    ),
    78 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("79", "African", "African", "1", "1", "10"),
    ),
    79 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("80", "Arabic", "Arabic", "2", "1", "10"),
    ),
    80 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("81", "Asian", "Asian", "3", "1", "10"),
    ),
    81 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("82", "Bollywood", "Bollywood", "4", "1", "10"),
    ),
    82 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("83", "Caribbean", "Caribbean", "5", "1", "10"),
    ),
    83 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("84", "Celtic", "Celtic", "6", "1", "10"),
    ),
    84 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("85", "European", "European", "7", "1", "10"),
    ),
    85 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("86", "French", "French", "8", "1", "10"),
    ),
    86 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("87", "German", "German", "9", "1", "10"),
    ),
    87 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("88", "Greek", "Greek", "10", "1", "10"),
    ),
    88 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("89", "Indian", "Indian", "11", "1", "10"),
    ),
    89 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("90", "Islamic", "Islamic", "12", "1", "10"),
    ),
    90 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("91", "Japanese", "Japanese", "13", "1", "10"),
    ),
    91 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("92", "Russian", "Russian", "14", "1", "10"),
    ),
    92 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("93", "Soca", "Soca", "15", "1", "10"),
    ),
    93 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("94", "Tamil", "Tamil", "16", "1", "10"),
    ),
    94 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("95", "Turkish", "Turkish", "17", "1", "10"),
    ),
    95 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("96", "Zouk", "Zouk", "18", "1", "10"),
    ),
    96 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("97", "Avant Garde", "Avant Garde", "1", "1", "11"),
    ),
    97 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("98", "Jazz", "Jazz", "2", "1", "11"),
    ),
    98 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("99", "Swing", "Swing", "3", "1", "11"),
    ),
    99 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("100", "Bachata", "Bachata", "1", "1", "12"),
    ),
    100 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("101", "Bossa", "Bossa", "2", "1", "12"),
    ),
    101 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("102", "Cumbia", "Cumbia", "3", "1", "12"),
    ),
    102 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("103", "Flamenco", "Flamenco", "4", "1", "12"),
    ),
    103 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("104", "Latin", "Latin", "5", "1", "12"),
    ),
    104 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("105", "Mariachi", "Mariachi", "6", "1", "12"),
    ),
    105 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("106", "Merengue", "Merengue", "7", "1", "12"),
    ),
    106 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("107", "Reggaeton", "Reggaeton", "8", "1", "12"),
    ),
    107 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("108", "Salsa", "Salsa", "9", "1", "12"),
    ),
    108 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("109", "Tango", "Tango", "10", "1", "12"),
    ),
    109 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("110", "Black Metal", "Black", "1", "1", "13"),
    ),
    110 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("111", "Death Metal", "Death", "2", "1", "13"),
    ),
    111 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("112", "Heavy Metal", "Heavy", "3", "1", "13"),
    ),
    112 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("113", "Thrash Metal", "Thrash", "4", "1", "13"),
    ),
    113 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("114", "JPOP", "JPOP", "1", "1", "16"),
    ),
    114 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("115", "KPOP", "KPOP", "2", "1", "16"),
    ),
    115 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("116", "Oldies", "Oldies", "3", "1", "16"),
    ),
    116 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("117", "Soft Rock", "Soft", "4", "1", "16"),
    ),
    117 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("118", "Top 40", "Top40", "5", "1", "16"),
    ),
    118 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("119", "News", "News", "1", "1", "17"),
    ),
    119 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("120", "Sports", "Sports", "2", "1", "17"),
    ),
    120 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("121", "Talk", "Talk", "3", "1", "17"),
    ),
    121 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("122", "Funk", "Funk", "1", "1", "18"),
    ),
    122 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("123", "Motown", "Motown", "2", "1", "18"),
    ),
    123 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("124", "Soul", "Soul", "3", "1", "18"),
    ),
    124 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("125", "Urban", "Urban", "4", "1", "18"),
    ),
    125 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("126", "Dancehall", "Dancehall", "1", "1", "20"),
    ),
    126 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("127", "Dub", "Dub", "2", "1", "20"),
    ),
    127 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("128", "Classic Rock", "Rock", "1", "1", "21"),
    ),
    128 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("129", "JROCK", "JROCK", "2", "1", "21"),
    ),
    129 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("130", "Prog Rock", "Prog", "3", "1", "21"),
    ),
    130 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("131", "Psychedelic", "Psychedelic", "4", "1", "21"),
    ),
    131 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("132", "Rockabilly", "Rockabilly", "5", "1", "21"),
    ),
    132 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("133", "Comedy", "Comedy", "1", "1", "23"),
    ),
    133 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("134", "Community", "Community", "2", "1", "23"),
    ),
    134 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("135", "Government", "Government", "3", "1", "23"),
    ),
    135 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("136", "News", "News", "4", "1", "23"),
    ),
    136 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("137", "Scanner", "Scanner", "5", "1", "23"),
    ),
    137 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("138", "Sports", "Sports", "6", "1", "23"),
    ),
    138 => array(
      'table' => "rzradio_elements",
      'columns' => array("ID", "Title", "Stream", "Ord", "Category", "Parent"),
      'values' => array("139", "Technology", "Technology", "7", "1", "23"),
    ),
  );
}
