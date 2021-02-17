<?php

/**
 * @file
 * Module integration class.
 */
 
$s_file = realpath(dirname(__FILE__) . '/../../../../inc/header.inc.php');
if (file_exists($s_file)) {
  require_once($s_file);
}
else {
  die("Init file is not found");
}	
require_once(BX_DIRECTORY_PATH_INC . "db.inc.php");
require_once(BX_DIRECTORY_PATH_INC . "utils.inc.php");
require_once(BX_DIRECTORY_PATH_INC . "languages.inc.php");
require_once(BX_DIRECTORY_PATH_INC . "membership_levels.inc.php");
require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

class RzIntegration extends RzBase {
  /**
   * Database tables prefix.
   */
  protected $dbPrefix;
  /**
   * Site home url.
   */
  protected $sSiteUrl;
  /**
   * Site login user url.
   */
  protected $sLoginUrl;

  /**
   * Class constructor.
   */
  public function __construct($s_path, $s_url) {
    parent::__construct($s_path, $s_url);
    $this->oDb = new RzDbConnect(DATABASE_HOST, DATABASE_PORT, DATABASE_SOCK, DATABASE_NAME, DATABASE_USER, DATABASE_PASS);
    $this->oDb->connect();
    $this->dbPrefix = '';
    $this->sSiteUrl = BX_DOL_URL_ROOT;
    $this->sLoginUrl = $this->sSiteUrl . "join.php";
  }

  /**
   * Checks user login and password.
   */
  protected function loginUser($s_name, $s_password, $b_login = FALSE) {
    $s_field = $b_login ? "NickName" : "ID";
    $s_id = $this->oDb->getValue("SELECT ID FROM Profiles WHERE " . $s_field . "='" . $s_name . "' AND Password='" . $s_password . "' LIMIT 1");
    return !empty($s_id);
  }

  /**
   * Checks admin login and password.
   */
  protected function loginAdmin($s_id, $s_password) {
    $s_name = $this->oDb->getValue("SELECT NickName FROM Profiles WHERE ID='" . $s_id . "' AND Password='" . $s_password . "' AND (Role & 2) LIMIT 1");			
    return !empty($s_name);
  }

  /**
   * Gets user info.
   */
  protected function getUserInfo($s_id, $b_nick = FALSE) {
    $s_where_part = ($b_nick ? "NickName" : "ID") . " = '" . $s_id . "'";
    $a_user = $this->oDb->getArray("SELECT * FROM Profiles WHERE " . $s_where_part . " LIMIT 1");
    if (empty($a_user['ID'])) {
      return array("id" => 0, "nick" => "", "sex" => "M", "age" => 25, "desc" => "", "photo" => "", "profile" => "");
    }
    $o_base_functions = bx_instance("BxBaseFunctions");
    $s_sex = !empty($a_user['Sex']) && $a_user['Sex'] == "female" ? "F" : "M";
    $s_photo = $o_base_functions->getMemberAvatar($s_id);
    if (empty($s_photo)) {
      $s_photo = $this->sUrl . "data/" . ($s_sex == "M" ? "male.jpg" : "female.jpg");
    }
    $s_nick = $GLOBALS['oFunctions']->getUserTitle($s_id);
    $s_age = isset($a_user['DateOfBirth']) ? $this->getAge($a_user['DateOfBirth']) : "25";
    $s_desc = $GLOBALS['oFunctions']->getUserInfo($s_id);
    $s_profile = getParam('enable_modrewrite') == "on" ? $this->sSiteUrl . $a_user['NickName'] : $this->sSiteUrl . "profile.php?ID=" . $s_id;
    return array("id" => (int)$a_user["ID"], "nick" => $s_nick, "sex" => $s_sex, "age" => $s_age, "desc" => $s_desc, "photo" => $s_photo, "profile" => $s_profile);
  }

  /**
   * Counts the age.
   */
  private function getAge($s_dob) {
    $a_dob = explode('-', $s_dob);
    $i_dob_year = $a_dob[0];
    $i_dob_month = $a_dob[1];
    $i_dob_day = $a_dob[2];
    $i_age = date('Y') - $i_dob_year;
    if ($i_dob_month > date('m')) {
      $i_age--;
    }
    elseif ($i_dob_month == date('m') && $i_dob_day > date('d')) {
      $i_age--;
    }
    return $i_age;
  }

  /**
   * Searches for user.
   */
  protected function searchUser($s_value, $s_field = "ID") {
    if ($s_field == "ID") {
      $s_field = "ID";
    }
    else {
      $s_field = "NickName";
    }
    $s_id = $this->oDb->getValue("SELECT id FROM Profiles WHERE " . $s_field . " = '" . $s_value . "' LIMIT 1");
    return $s_id;
  }

  /**
   * Gets current language.
   */
  protected function getCurrentLang($s_user_id = "") {
    return getCurrentLangName();
  }

  /**
   * Gets membership id by user id.
   */
  protected function getMembershipId($s_user_id) {
    $a_membership = getMemberMembershipInfo_current($s_user_id);
    return $a_membership["ID"];
  }

  /**
   * Gets memberships levels.
   */
  protected function getMemberships() {
    $a_memberships = array();
    $r_result = $this->oDb->getResult("SELECT * FROM `sys_acl_levels`");
    while (($a_membership = $this->oDb->fetch($r_result)) !== NULL) {
      $a_memberships[$a_membership["ID"]] = $a_membership["Name"];
    }
    return $a_memberships;
  }
}
