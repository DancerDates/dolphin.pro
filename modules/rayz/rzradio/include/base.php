<?php

/**
 * @file
 * Module base class.
 */

class RzBase {
  const TRUE_VAL = "true";
  const FALSE_VAL = "false";
  const SUCCESS_VAL = "success";
  const FAILED_VAL = "failed";
  /**
   * Module system name.
   */
  protected $sModule = "";
  /**
   * Xml templates to be used for xml-responces.
   */
  protected $aXmlTemplates = array(
    "item" => array(
      2 => '<item key="#1#"><![CDATA[#2#]]></item>',
    ),
    "result" => array(
      1 => '<result value="#1#" />',
      2 => '<result value="#1#" status="#2#" />',
    ),
    "current" => array(
      2 => '<current name="#1#" url="#2#" />',
    ),
    "file" => array(
      2 => '<file name="#1#"><![CDATA[#2#]]></file>',
    ),
  );
  /**
   * Module home url.
   */
  protected $sUrl;
  /**
   * Module home path.
   */
  protected $sPath;
  /**
   * Module files path.
   */
  protected $sFilesPath;
  /**
   * Database queries handler object.
   */
  protected $oDb;
  /**
   * Xml documents handler object.
   */
  protected $oXml;
  /**
   * Request variable - id.
   */
  protected $sId;

  /**
   * Class constructor.
   */
  public function __construct($s_path, $s_url) {
    $this->sPath = $s_path;
    $this->sFilesPath = $this->sPath . "files/";
    $this->sUrl = $s_url;
    $this->sId = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
    $this->oXml = new RzXml();
  }

  /**
   * Config generator.
   */
  public function actionConfig() {
    $s_file_name = $this->sPath . "data/config.xml";
    $r_handle = fopen($s_file_name, "rt");
    $s_contents = fread($r_handle, filesize($s_file_name));
    fclose($r_handle);

    if (is_dir($this->sFilesPath)) {
      $s_contents = str_replace("#filesUrl#", $this->sUrl . "files/", $s_contents);
    }
    return $s_contents;
  }

  /**
   * Set config.
   */
  public function actionSetSetting() {
    $s_key = $this->getRequestVar("key");
    $s_value = $this->getRequestVar("value");
    $s_password = $this->getRequestVar("password");
    if ($this->loginAdmin($this->sId, $s_password)) {
      $a_result = $this->setSettingValue($s_key, $s_value);
      return $this->parseXml($this->aXmlTemplates['result'], $a_result['value'], $a_result['status']);
    }
    else {
      return $this->parseXml($this->aXmlTemplates['result'], "msgUserAuthenticationFailure", self::FAILED_VAL);
    }
  }

  /**
   * Get languages.
   */
  public function actionGetLanguages() {
    return $this->printFiles($this->sId);
  }

  /**
   * Get setting value.
   */
  protected function getSettingValue($s_setting_key, $s_file_path = "", $b_full_return = FALSE) {
    if (empty($s_file_path)) {
      $s_file_path = $this->sPath . "data/config.xml";
    }
    if (!file_exists($s_file_path)) {
      if ($b_full_return) {
        return array('value' => "Cannot open file", 'status' => self::FAILED_VAL);
      }
      else {
        return "";
      }
    }
    $s_config_contents = $this->makeGroup("", "items");
    if (($r_handle = @fopen($s_file_path, "rt")) !== FALSE && filesize($s_file_path) > 0) {
      $s_config_contents = fread($r_handle, filesize($s_file_path));
      fclose($r_handle);
    }
    $s_value = $this->oXml->getValue($s_config_contents, "item", $s_setting_key);
    if ($b_full_return) {
      return array('value' => $s_value, 'status' => self::SUCCESS_VAL);
    }
    else {
      return $s_value;
    }
  }

  /**
   * Set setting value.
   */
  protected function setSettingValue($s_setting_key, $s_setting_value, $s_file_path = "") {
    if (empty($s_file_path)) {
      $s_file_path = $this->sPath . "data/config.xml";
    }
    if (!file_exists($s_file_path)) {
      return $this->parseXml($this->aXmlTemplates['result'], "Cannot open file " . $s_file_path, self::FAILED_VAL);
    }
    $s_config_contents = "";
    if (($r_handle = @fopen($s_file_path, "rt")) !== FALSE && filesize($s_file_path) > 0) {
      $s_config_contents = fread($r_handle, filesize($s_file_path));
      fclose($r_handle);
    }
    if (is_array($s_setting_key) && is_array($s_setting_value)) {
      for ($i = 0; $i < count($s_setting_key); $i++) {
        $s_config_contents = $this->oXml->setValue($s_config_contents, "item", $s_setting_key[$i], $s_setting_value[$i]);
      }
    }
    else {
      $s_config_contents = $this->oXml->setValue($s_config_contents, "item", $s_setting_key, $s_setting_value);
    }

    $b_result = TRUE;
    if (($r_handle = @fopen($s_file_path, "wt")) !== FALSE) {
      $b_result = (fwrite($r_handle, $s_config_contents) !== FALSE);
      fclose($r_handle);
    }
    $b_result = $b_result && $r_handle;
    $s_value = $b_result ? "" : "Cannot write to file " . $s_file_path;
    return array('value' => $s_value, 'status' => $b_result ? self::SUCCESS_VAL : self::FAILED_VAL);
  }

  /**
   * Parse XML pattern.
   */
  protected function parseXml($a_xml_templates) {
    $i_num_args = func_num_args();
    $s_content = $a_xml_templates[$i_num_args - 1];
    for ($i = 1; $i < $i_num_args; $i++) {
      $s_value = func_get_arg($i);
      $s_content = str_replace("#" . $i . "#", $s_value, $s_content);
    }
    return $s_content;
  }

  /**
   * Make XML group tag.
   */
  public function makeGroup($s_xml_content, $s_xml_group = "rz") {
    return "<" . $s_xml_group . ">" . $s_xml_content . "</" . $s_xml_group . ">";
  }

  /**
   * Get extra files.
   */
  protected function getExtraFiles($s_user_id = "") {
    $s_files = $this->sPath . "langs/";
    $a_files = array();
    $s_extension = "xml";
    if ($r_dir_handle = opendir($s_files)) {
      while (FALSE !== ($s_file = readdir($r_dir_handle))) {
        if (is_file($s_files . $s_file) && $s_file != "." && $s_file != ".." && $s_extension == substr($s_file, strpos($s_file, ".") + 1)) {
          $a_files[] = substr($s_file, 0, strpos($s_file, "."));
        }
      }
    }
    closedir($r_dir_handle);
    $s_default_file = $this->getCurrentLang($s_user_id);
    $s_current_file = (in_array($s_default_file, $a_files)) ? $s_default_file : $a_files[0];
    return array(
      'files' => $a_files,
      'current' => $s_current_file,
      'extension' => $s_extension,
    );
  }

  /**
   * Prints files.
   */
  protected function printFiles($s_user_id = "") {
    $a_result = $this->getExtraFiles($s_user_id);
    $s_current = $a_result['current'];
    $s_current_file = $s_current . "." . $a_result['extension'];
    $s_contents = "";
    for ($i = 0; $i < count($a_result['files']); $i++) {
      $s_file = $a_result['files'][$i];
      $s_contents .= $this->parseXml($this->aXmlTemplates['file'], $s_file, $s_file);
    }
    $s_contents = $this->makeGroup($s_contents, "files");
    $s_contents .= $this->parseXml($this->aXmlTemplates['current'], $s_current, $this->sUrl . "langs/" . $s_current_file);
    return $s_contents;
  }

  /**
   * Gets current language.
   */
  protected function getCurrentLang($s_user_id = "") {
    return "en";
  }

  /**
   * Gets request variable.
   */
  public function getRequestVar($s_key, $s_type = "string") {
	switch ($s_type) {
      case "int":
        return isset($_REQUEST[$s_key]) ? (int) $_REQUEST[$s_key] : 0;

      case "boolean":
        return isset($_REQUEST[$s_key]) ? $_REQUEST[$s_key] == self::TRUE_VAL : FALSE;

      case "strbool":
        return isset($_REQUEST[$s_key]) && $_REQUEST[$s_key] == self::TRUE_VAL ? self::TRUE_VAL : self::FALSE_VAL;

      case "db":
        return isset($_REQUEST[$s_key]) ? mysql_real_escape_string($_REQUEST[$s_key]) : "";

      case "string":
      default:
        return isset($_REQUEST[$s_key]) ? $_REQUEST[$s_key] : "";
    }
  }

  /**
   * Checks user login and password.
   */
  protected function loginUser($s_name, $s_password, $b_login = FALSE) {
    return TRUE;
  }

  /**
   * Checks admin login and password.
   */
  protected function loginAdmin($s_id, $s_password) {
    return TRUE;
  }

  /**
   * Gets user info.
   */
  protected function getUserInfo($s_id, $b_nick = FALSE) {
    return array(
      "id" => 0,
      "nick" => "no name",
      "sex" => "M",
      "age" => 25,
      "desc" => "no desc",
      "photo" => "",
      "profile" => "",
    );
  }

  /**
   * Searches for user.
   */
  protected function searchUser($s_value, $s_field = "ID") {
    return $s_value;
  }

  /**
   * Gets membership id by user id.
   */
  protected function getMembershipId($s_user_id) {
    return 0;
  }

  /**
   * Gets memberships levels.
   */
  protected function getMemberships() {
    return array();
  }
}
