<?php 

/**
 * @file
 * Module main class.
 */

class RzModule extends RzIntegration {
  /**
   * Module system name.
   */
  protected $sModule = "rzradio";
  /**
   * Elements DB table.
   */
  protected $elementsDbTable;
  /**
   * Favorites DB table.
   */
  protected $favoritesDbTable;
  const SEARCH_URL = "http://dir.xiph.org/search?search=#key#";
  const CATEGORY_URL = "http://dir.xiph.org/by_genre/#key#";
  const M3U_URL = "http://dir.xiph.org/listen/#id#/listen.m3u";

  /**
   * Class constructor.
   */
  public function __construct($s_path, $s_url) {
    parent::__construct($s_path, $s_url);
    $this->elementsDbTable = $this->dbPrefix . $this->sModule . "_elements";
    $this->favoritesDbTable = $this->dbPrefix . $this->sModule . "_favorites";
    $this->aXmlTemplates["result"][4] = '<result value="#1#" status="#2#" param="#3#" autoplay="#4#" />';
    $this->aXmlTemplates["result"][5] = '<result value="#1#" status="#2#" page="#3#" itemsPerPage="#4#" itemsAll="#5#" />';
    $this->aXmlTemplates["element"] = array(
      3 => '<element file="#1#" stream="#2#"><title><![CDATA[#3#]]></title></element>',
      5 => '<element id="#1#" image="#2#" elements="#5#" search="#4#" isCategory="true"><title><![CDATA[#3#]]></title></element>',
      6 => '<element id="#1#" file="#2#" stream="#3#" favorite="#4#" featured="#5#"><title><![CDATA[#6#]]></title></element>',
    );
  }

  /**
   * Config generator.
   */
  public function actionConfig() {
    $s_contents = parent::actionConfig();
    $s_contents = str_replace("#joinUrl#", $this->sLoginUrl, $s_contents);
    $s_contents = str_replace("#popupUrl#", $this->sUrl . "player.php", $s_contents);
    $s_menu_location = empty($_COOKIE["RayzRadioMenu"]) ? "bottom" : $_COOKIE["RayzRadioMenu"];
    $s_contents = str_replace("#menuLocation#", $s_menu_location, $s_contents);
    $s_menu_hide = empty($_COOKIE["RayzRadioMenuHide"]) ? self::FALSE_VAL : $_COOKIE["RayzRadioMenuHide"];
    $s_contents = str_replace("#menuHide#", $s_menu_hide, $s_contents);
    $s_menu_hint = empty($_COOKIE["RayzRadioMenuHint"]) ? self::TRUE_VAL : $_COOKIE["RayzRadioMenuHint"];
    $s_contents = str_replace("#menuHint#", $s_menu_hint, $s_contents);
    $i_volume = empty($_COOKIE["RayzRadioVolume"]) ? 100 : $_COOKIE["RayzRadioVolume"];
    $s_contents = str_replace("#volume#", $i_volume, $s_contents);
    return $s_contents;
  }

  /**
   * Sets config setting.
   */
  public function actionSetConfig() {
    $s_key = $this->getRequestVar("key");
    $s_value = $this->getRequestVar("value");
    if (!empty($s_key) && !empty($s_value)) {
      setcookie("RayzRadio" . $s_key, $s_value, time() + 31536000);
    }
  }

  /**
   * Authorizes user.
   */
  public function actionUserAuthorize() {
    $s_password = $this->getRequestVar("password", "db");
    $s_value = $this->loginAdmin($this->sId, $s_password) ? self::TRUE_VAL : self::FALSE_VAL;
    $s_status = $this->loginUser($this->sId, $s_password) ? self::SUCCESS_VAL : self::FAILED_VAL;
    return $this->parseXml($this->aXmlTemplates['result'], $s_value, $s_status);
  }

  /**
   * Authorizes admin.
   */
  public function actionAdminAuthorize() {
    return $this->actionUserAuthorize();
  }

  /**
   * Creates new category.
   */
  public function actionNewCategory() {
    $s_title = $this->getRequestVar("title", "db");
    $i_category_id = $this->getRequestVar("category", "int");
    $s_id = $this->oDb->getValue("SELECT ID FROM " . $this->elementsDbTable . " WHERE Title='" . $s_title . "' AND Parent= " . $i_category_id . " LIMIT 1");
    if (empty($s_id)) {
      $s_search = $this->getRequestVar("search");
      $this->oDb->getResult("INSERT INTO " . $this->elementsDbTable . "(Title, Stream, Parent, Category) VALUES('" . $s_title . "', '" . $s_search . "', " . $i_category_id . ", 1)");
      $i_cat_id = (int)$this->oDb->getResult("SELECT MAX(ID) FROM " . $this->elementsDbTable);
      $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Ord=(Ord+1) WHERE Parent=" . $i_category_id);
      return $this->parseXml($this->aXmlTemplates["result"], $i_cat_id, self::SUCCESS_VAL);
    }
    else {
      return $this->parseXml($this->aXmlTemplates["result"], "Category with the same title already exists", self::FAILED_VAL);
    }
  }

  /**
   * Edits a category.
   */
  public function actionEditCategory() {
    $s_title = $this->getRequestVar("title", "db");
    $s_search = $this->getRequestVar("search", "db");
    $r_result = $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Title='" . $s_title . "', Stream='" . $s_search . "' WHERE ID='" . $this->sId . "'");
    if ($r_result) {
      return $this->parseXml($this->aXmlTemplates["result"], "", self::SUCCESS_VAL);
    }
    else {
      return $this->parseXml($this->aXmlTemplates["result"], "Error changing category", self::FAILED_VAL);
    }
  }

  /**
   * Uploads category image.
   */
  public function actionUploadCategoryImage() {
    $s_extension = str_replace(".", "", $this->getRequestVar("ext"));
    $s_old_file = $this->sFilesPath . $this->getCatImage($this->sId);
    $s_file_name = $this->sFilesPath . $this->sId . "_cat." . $s_extension;
    if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      @unlink($s_old_file);
      move_uploaded_file($_FILES['Filedata']['tmp_name'], $s_file_name);
      @chmod($s_file_name, 0644);
    }
  }

  /**
   * Checks category image.
   */
  public function actionCheckCategoryImage() {
    $s_extension = str_replace(".", "", $this->getRequestVar("ext"));
    $s_file_name = $this->sFilesPath . $this->sId . "_cat." . $s_extension;
    if (!file_exists($s_file_name) || filesize($s_file_name) == 0) {
      return $this->parseXml($this->aXmlTemplates['result'], "Error uploading file.", self::FAILED_VAL);
    }
    else {
      return $this->parseXml($this->aXmlTemplates['result'], $this->sId . "_cat." . $s_extension, self::SUCCESS_VAL);
    }
  }

  /**
   * Removes category.
   */
  public function actionRemoveCategory() {
    $s_user = $this->getRequestVar("user", "db");
    $s_password = $this->getRequestVar("password");
    if (!$this->loginAdmin($s_user, $s_password)) {
      return $this->parseXml($this->aXmlTemplates['result'], "Access error!", self::FAILED_VAL);
    }
    $iParentId = $this->oDb->getValue("SELECT Parent FROM " . $this->elementsDbTable . " WHERE ID='" . $this->sId . "'");
    $r_res = $this->oDb->getResult("DELETE FROM " . $this->elementsDbTable . " WHERE ID=" . $this->sId);
    @unlink($this->getCatImagePath($this->sId));
    if ($r_res) {
      $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Ord=(Ord-1) WHERE Category=1 AND Parent=" . $iParentId);
      return $this->parseXml($this->aXmlTemplates["result"], $this->sId, self::SUCCESS_VAL);
    }
    else {
      return $this->parseXml($this->aXmlTemplates["result"], "Error removing category", self::FAILED_VAL);
    }
  }

  /**
   * Changes categories order.
   */
  public function actionChangeCategoriesOrder() {
    $s_id1 = $this->getRequestVar("id1", "db");
    $r_result = $this->oDb->getResult("SELECT ID, Ord FROM " . $this->elementsDbTable . " WHERE ID='" . $this->sId . "' OR ID='" . $s_id1 . "'");
    $aCat1 = $this->oDb->fetch($r_result);
    $aCat2 = $this->oDb->fetch($r_result);
    $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Ord='" . $aCat2['Ord'] . "' WHERE ID='" . $aCat1['ID'] . "'");
    $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Ord='" . $aCat1['Ord'] . "' WHERE ID='" . $aCat2['ID'] . "'");
  }

  /**
   * Gets category contents.
   */
  public function actionGetCategoryContents() {
    return $this->getElements("category");
  }

  /**
   * Gets elements.
   */
  public function actionGetElements() {
    $s_mode = $this->getRequestVar("mode");
    return $this->getElements($s_mode);
  }

  /**
   * Gets elements handler.
   */
  protected function getElements($s_mode) {
    $i_page = $this->getRequestVar("page", "int");
    $i_items_per_page = $this->getRequestVar("perPage", "int");
    $i_category_id = $this->getRequestVar("category", "int");
    $s_user = $this->getRequestVar("user");
    $b_paginate = FALSE;
    $s_count = "SELECT COUNT(ID) AS Count ";
    $s_select = "SELECT * ";
    switch ($s_mode) {
      case 'category':
        $s_sql = "";
        if ($i_category_id > 0) {
          $this->fillCategory($i_category_id);
          $s_cat_factor = $s_cat_factor1 = "";
        }
        else {
          $s_cat_factor = "Category=1 AND ";
          $s_cat_factor1 = "cats.Category=1 AND ";
        }
        $s_count = "SELECT COUNT(ID) AS Count FROM " . $this->elementsDbTable . " WHERE " . $s_cat_factor . "Parent=" . $i_category_id;
        $s_select = "SELECT cats.*, COUNT(els.ID) AS Count FROM " . $this->elementsDbTable . " AS cats LEFT JOIN " . $this->elementsDbTable . " AS els ON cats.ID=els.Parent WHERE " . $s_cat_factor1 . "cats.Parent=" . $i_category_id . " GROUP BY cats.ID ORDER BY cats.Category DESC, cats.Ord ASC, cats.ID DESC";
        break;
      case 'favorites':
        $s_count = "SELECT COUNT(El.ID) AS Count ";
        $s_select = "SELECT El.* ";
        $s_sql = "FROM " . $this->elementsDbTable . " AS El INNER JOIN " . $this->favoritesDbTable . " AS Fv WHERE El.Category=0 AND El.ID=Fv.Element AND Fv.User='" . $s_user ."' ORDER BY Fv.ID DESC";
        $s_user = "";
        break;
      case 'featured':
      default:
        $b_paginate = TRUE;
        $s_sql = "FROM " . $this->elementsDbTable . " WHERE Featured=1 ORDER BY ID DESC";
        break;
    }
    $i_count = (int)@$this->oDb->getValue($s_count . $s_sql);
    if (!$b_paginate) {
      $i_items_per_page = $i_count;
    }
    $r_result = @$this->oDb->getResult($s_select . $s_sql . ($b_paginate ? $this->getPagination($i_page, $i_items_per_page) : ""));
    if (!$r_result) {
      return $this->parseXml($this->aXmlTemplates["result"], "msgErrorGetItems", self::FAILED_VAL);
    }
    $s_contents = $this->parseXml($this->aXmlTemplates["result"], "", self::SUCCESS_VAL, $i_page, $i_items_per_page, $i_count);
    $s_elements = "";
    $i_current_time = time();
    while (($a_element = $this->oDb->fetch($r_result)) !== NULL) {
      if ($a_element['Category']) {
        $s_elements .= $this->parseXml($this->aXmlTemplates['element'], $a_element['ID'], $this->getCatImage($a_element['ID']), stripslashes($a_element['Title']), $a_element['Stream'], $a_element['Count']);
      }
      else {
        $s_favorite = (!empty($s_user) && $this->isFavorite($s_user, $a_element['ID'])) ? self::TRUE_VAL : self::FALSE_VAL;
        $s_featured = $a_element['Featured'] > 0 ? self::TRUE_VAL : self::FALSE_VAL;
        $s_elements .= $this->parseXml($this->aXmlTemplates['element'], $a_element['ID'], $a_element['PlaylistUrl'], $a_element['Stream'], $s_favorite, $s_featured, stripslashes($a_element['Title']));
      }
    }
    $s_contents .= $this->makeGroup($s_elements, "elements");
    return $s_contents;
  }

  /**
   * Searches radio stations.
   */
  public function actionSearch() {
    $s_search = $this->getRequestVar("search");
    if (empty($s_search)) {
      return $this->parseXml($this->aXmlTemplates["result"], "msgErrorGetStations", self::FAILED_VAL);
    }
    $a_result = $this->search($s_search, $i_page, $i_items_per_page);
    if ($a_result['status'] != self::SUCCESS_VAL) {
      return $this->parseXml($this->aXmlTemplates["result"], $a_result['value'], $a_result['status']);
    }
    $s_radios = "";
    $a_radios = $a_result['value'];
    for ($i = 0; $i < count($a_radios); $i++) {
      $s_radios .= $this->parseXml($this->aXmlTemplates['element'], $a_radios[$i]['playlist'], "", $a_radios[$i]['title']);
    }
    return $this->parseXml($this->aXmlTemplates["result"], "", self::SUCCESS_VAL, $i_page, $i_items_per_page, max(($i_pagesNum * $i_items_per_page), count($a_radios))) . $this->makeGroup($s_radios, "elements");
  }

  /**
   * Gets stream url.
   */
  public function actionGetStreamUrl() {
    $s_stream = $this->getRequestVar("stream");
    $s_title = addslashes($this->getRequestVar("title"));
    $a_result = $this->getStreamUrl($s_stream, $s_title);
    if ($a_result['status'] == self::SUCCESS_VAL) {
      $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Stream='" . $a_result['value'] . "', PlaylistUrl='" . $a_result['playlist'] . "' WHERE PlaylistUrl='" . $s_stream . "' LIMIT 1");
    }
    elseif (!empty($s_stream) && $this->getSettingValue("removeIdle") == self::TRUE_VAL) {
      $this->oDb->getResult("DELETE Els, Favs FROM " . $this->elementsDbTable . " AS Els LEFT JOIN " . $this->favoritesDbTable . " AS Favs ON Els.ID=Favs.Element WHERE Els.PlaylistUrl = '" . $s_stream . "'");
    }
    return $this->parseXml($this->aXmlTemplates['result'], $a_result['value'], $a_result['status']);
  }

  /**
   * Deletes station.
   */
  public function actionDeleteStation() {
    $s_user = $this->getRequestVar("user");
    $s_password = $this->getRequestVar("password");
    if (!$this->loginAdmin($s_user, $s_password)) {
      return $this->parseXml($this->aXmlTemplates['result'], "Access error!", self::FAILED_VAL);
    }
    $this->oDb->getResult("DELETE Els, Favs FROM " . $this->elementsDbTable . " AS Els LEFT JOIN " . $this->favoritesDbTable . " AS Favs ON Els.ID=Favs.Element WHERE Els.ID = " . $this->sId);
    @unlink($this->sFilesPath . $this->sId . ".jpg");
  }

  /**
   * Updates station.
   */
  public function actionUpdateStation() {
    $s_playlist_url = "";
    $s_title = $this->getRequestVar("title", "db");
    $s_stream = $this->getRequestVar("stream");
    $b_playlist = $this->getRequestVar("playlist", "boolean");
    $i_category = $this->getRequestVar("category", "int");
    if ($b_playlist) {
      $s_playlist_url = $s_stream;
      $a_result = $this->getStreamUrl($s_playlist_url, $s_title);
      $s_stream = $a_result['status'] == self::SUCCESS_VAL ? $a_result['value'] : "";
      if ($a_result['status'] == self::SUCCESS_VAL) {
        $s_playlist_url = $a_result['playlist'];
      }
    }
    $s_category_id = isset($_REQUEST['category']) ? $_REQUEST['category'] : "";
    $s_category = $i_category == 0 ? "" : ", Parent=" . $i_category;
    $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Title='" . $s_title . "', PlaylistUrl='" . $s_playlist_url . "', Stream='" . $s_stream . "'" . $s_category . " WHERE ID=" . $this->sId);
    return $this->parseXml($this->aXmlTemplates['result'], "", self::SUCCESS_VAL, $s_stream);
  }

  /**
   * Renames station.
   */
  public function actionRenameStation() {
    $i_category = $this->getRequestVar("category", "int");
    $s_category = $i_category == 0 ? "" : ", Parent='" . $i_category . "'";
    $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Title='" . $s_title . "', Url='" . $s_url . "'" . $s_category . " WHERE ID=" . $this->sId);
    return $this->parseXml($this->aXmlTemplates['result'], "", self::SUCCESS_VAL);
  }

  /**
   * Renames station.
   */
  public function actionAddStation() {
    $a_result = $this->addStation();
    return $this->parseXml($this->aXmlTemplates['result'], $a_result['value'], $a_result['status'], $a_result['stream']);
  }

  /**
   * Adds favorite.
   */
  public function actionAddFavorite() {
    if (empty($this->sId)) {
      $a_result = $this->addStation();
      if ($a_result['status'] == self::FAILED_VAL) {
        return $this->parseXml($this->aXmlTemplates['result'], $a_result['value'], $a_result['status']);
      }
      else {
        $this->sId = $a_result['value'];
      }
    }
    $s_user = $this->getRequestVar("user", "db");
    if (!$this->isFavorite($s_user, $this->sId)) {
      $this->oDb->getResult("INSERT INTO " . $this->favoritesDbTable . "(User, Element) VALUES('" . $s_user . "', '" . $this->sId . "')");
    }
    return $this->parseXml($this->aXmlTemplates['result'], $this->sId, self::SUCCESS_VAL);
  }

  /**
   * Adds favorite.
   */
  public function actionRemoveFavorite() {
    $s_user = $this->getRequestVar("user");
    $this->oDb->getResult("DELETE FROM " . $this->elementsDbTable . " WHERE User='" . $s_user . "' AND ID='" . $this->sId . "' AND Category=0 LIMIT 1");
    $r_res = $this->oDb->getResult("DELETE FROM " . $this->favoritesDbTable . " WHERE User='" . $s_user . "' AND Element='" . $this->sId . "'");
    if ($r_res) {
      return $this->parseXml($this->aXmlTemplates["result"], "", self::SUCCESS_VAL);
    }
    else {
      return $this->parseXml($this->aXmlTemplates["result"], "Error removing favorite", self::FAILED_VAL);
    }
  }

  /**
   * Adds featured.
   */
  public function actionAddFeatured() {
    if (empty($this->sId)) {
      $a_result = $this->addStation(TRUE);
      if ($a_result['status'] == self::FAILED_VAL) {
        return $this->parseXml($this->aXmlTemplates['result'], $a_result['value'], $a_result['status']);
      }
      else {
        $this->sId = $a_result['value'];
      }
    }
    else {
      $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Featured=1 WHERE ID=" . $this->sId);
    }
    return $this->parseXml($this->aXmlTemplates['result'], $this->sId, self::SUCCESS_VAL);
  }

  /**
   * Removes featured.
   */
  public function actionRemoveFeatured() {
    $s_user = $this->getRequestVar("user", "db");
    $i_start = $this->getRequestVar("start", "int");
    $r_res = $this->oDb->getResult("DELETE Els, Favs FROM " . $this->elementsDbTable . " AS Els LEFT JOIN " . $this->favoritesDbTable . " AS Favs ON Els.ID=Favs.Element WHERE Els.User='" . $s_user . "' AND Els.ID='" . $this->sId . "' AND Els.Category=0 AND Els.Featured=1");
    if (!$r_res) {
      $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Featured=0 WHERE ID=" . $this->sId);
    }
    $s_contents = $this->parseXml($this->aXmlTemplates["result"], "", self::SUCCESS_VAL);
    if ($i_start > 0) {
      $a_element = $this->oDb->getArray("SELECT * FROM " . $this->elementsDbTable . " WHERE Featured=1 ORDER BY ID DESC LIMIT " . ($i_start-1) . ",1");
      if (is_array($a_element) && count($a_element) > 0) {
        $s_favorite = (!empty($s_user) && $this->isFavorite($s_user, $a_element['ID'])) ? self::TRUE_VAL : self::FALSE_VAL;
        $s_contents .= $this->parseXml($this->aXmlTemplates["element"], $a_element['ID'], $a_element['PlaylistUrl'], $a_element['Stream'],  $s_favorite, stripslashes($a_element['Title']));
      }
    }
    return $s_contents;
  }

  /**
   * Removes station.
   */
  public function actionRemove() {
    $i_start = $this->getRequestVar("start", "int");
    $s_user = $this->getRequestVar("user", "db");
    $s_password = $this->getRequestVar("password", "db");
    if (!$this->loginAdmin($s_user, $s_password)) {
      return $this->parseXml($this->aXmlTemplates['result'], "Access error!", self::FAILED_VAL);
    }
    $r_res = $this->oDb->getResult("DELETE Els, Favs FROM " . $this->elementsDbTable . " AS Els LEFT JOIN " . $this->favoritesDbTable . " AS Favs ON Els.ID=Favs.Element WHERE Els.ID=" . $this->sId);
    if ($r_res) {
      $s_contents = $this->parseXml($this->aXmlTemplates["result"], "", self::SUCCESS_VAL);
      if ($i_start > 0) {
        $a_element = $this->oDb->getArray("SELECT * FROM " . $this->elementsDbTable . " WHERE Category=0 ORDER BY ID DESC LIMIT " . ($i_start-1) . ",1");
        if (is_array($a_element) && count($a_element) > 0) {
          $s_favorite = (!empty($s_user) && $this->isFavorite($s_user, $a_element['ID'])) ? self::TRUE_VAL : self::FALSE_VAL;
          $s_contents .= $this->parseXml($this->aXmlTemplates["element"], $a_element['ID'], $a_element['PlaylistUrl'], $a_element['Stream'],  $s_favorite, stripslashes($a_element['Title']));
        }
      }
    }
    else {
      $s_contents = $this->parseXml($this->aXmlTemplates["result"], "Error removing radio", self::FAILED_VAL);
    }
    return $s_contents;
  }

  /**
   * Gets category image.
   */
  protected function getCatImage($s_category_id) {
    if (file_exists($this->sFilesPath . $s_category_id . "_cat.jpg")) {
      return $s_category_id . "_cat.jpg";
    }
    if (file_exists($this->sFilesPath . $s_category_id . "_cat.png")) {
      return $s_category_id . "_cat.png";
    }
    if (file_exists($this->sFilesPath . $s_category_id . "_cat.gif")) {
      return $s_category_id . "_cat.gif";
    }
    return "cat.png";
  }

  /**
   * Gets category image path.
   */
  protected function getCatImagePath($s_category_id) {
    $s_cat = $this->getCatImage($s_category_id);
    if ($s_cat == "cat.png") {
      return "";
    }
    else {
      return $this->sFilesPath . $s_cat;
    }
  }

  /**
   * Gets pagination.
   */
  protected function getPagination($i_page = 0, $i_items_per_page = 25)	{
    return " LIMIT " . $i_page * $i_items_per_page . ", " . $i_items_per_page;
  }

  /**
   * Checks if the given element id is favorited by given user.
   */
  protected function isFavorite($s_user, $s_element_id) {
    $s_favoriteId = $this->oDb->getValue("SELECT ID FROM " . $this->favoritesDbTable . " WHERE User='" . $s_user . "' AND Element='" . $s_element_id . "' LIMIT 1");
    return !empty($s_favoriteId);
  }

  /**
   * Adds station.
   */
  protected function addStation($b_featured = FALSE) {
    $s_user = $this->getRequestVar("user", "db");
    $s_title = $this->getRequestVar("title", "db");
    $s_playlist_url = $this->getRequestVar("stream", "db");
    $i_category = $this->getRequestVar("category", "int");
    $a_result = $this->oDb->getArray("SELECT ID, Stream FROM " . $this->elementsDbTable . " WHERE PlaylistUrl='" . $s_playlist_url . "' LIMIT 1");
    $this->sId = $a_result['ID'];
    if (empty($this->sId)) {
      $a_result = $this->getStreamUrl($s_playlist_url, $s_title);
      if ($a_result['status'] == self::FAILED_VAL) {
        return $a_result;
      }
      $s_stream = $a_result['value'];
      $r_result = $this->oDb->getResult("INSERT INTO " . $this->elementsDbTable . "(User, Title, PlaylistUrl, Stream, Featured, Parent) VALUES('" . $s_user . "', '" . $s_title . "', '" . $s_playlist_url . "', '" . $s_stream . "', " . ($b_featured ? 1 : 0) . ", " . $i_category . ")");
      if ($r_result) {
        return array('value' => (int)$this->oDb->getResult("SELECT MAX(ID) FROM " . $this->elementsDbTable), 'status' => self::SUCCESS_VAL, 'stream' => $s_stream);
      }
      else {
        return array('value' => "msgAddingError", 'status' => self::FAILED_VAL, 'stream' => "");
      }
    }
    else {
      if ($b_featured) {
        $this->oDb->getResult("UPDATE " . $this->elementsDbTable . " SET Featured=1 WHERE ID=" . $this->sId);
      }
      return array('value' => $this->sId, 'status' => self::SUCCESS_VAL, 'stream' => $a_result['Stream']);
    }
  }

  /**
   * Gets stream url.
   */
  protected function getStreamUrl($s_playlist_url, $s_title) {
    $s_streamUrl = $this->retrieveStreamUrl($s_playlist_url);
    if (!$this->isUrl($s_streamUrl)) {
      $a_result = $this->search($s_title);
      if ($a_result['status'] == self::SUCCESS_VAL && is_array($a_result['value']) && count($a_result['value'])>0 && $a_result['value'][0]['title'] == $s_title) {
        $s_playlist_url = $a_result['value'][0]['playlist'];
        $s_streamUrl = $this->retrieveStreamUrl($s_playlist_url);
      }
    }
    if (!$this->isUrl($s_streamUrl)) {
      return array('value' => "msgBadStreamUrl", 'status' => self::FAILED_VAL);
    }
    return array('value' => $s_streamUrl, 'playlist' => $s_playlist_url, 'status' => self::SUCCESS_VAL);
  }

  /**
   * Retrieves stream url.
   */
  protected function retrieveStreamUrl($s_playlist_url) {
    $s_streamUrl = "";
    $s_data = $this->readUrl($s_playlist_url, FALSE);
    $a_file_lines = explode("\n", $s_data);
    for ($i = 0; $i < count($a_file_lines); $i++) {
      $a_parts = explode("=", $a_file_lines[$i]);
      if ($a_parts[0] == "File1") {
        $s_streamUrl = trim($a_parts[1]);
      }
    }
    if (empty($s_streamUrl)) {
      $s_streamUrl = trim($a_file_lines[0]);
    }
//$s_streamUrl .= substr($s_streamUrl, -1) == "/" ? ";" : "/;";
    return $s_streamUrl;
  }

  /**
   * Retrieves stream url.
   */
  protected function readUrl($s_url, $b_header = TRUE) {
    $s_data = "";
    if (function_exists("curl_init")) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $s_url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
      if ($b_header) {
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
      }
      $s_data = curl_exec($curl);
      curl_close($curl);
    }
    else {
      $s_data = file_get_contents($s_url);
    }
    return $s_data;
  }

  /**
   * Retrieves stream url.
   */
  protected function fillCategory($i_category) {
    $s_search = $this->oDb->getValue("SELECT Stream FROM " . $this->elementsDbTable . " WHERE ID=" . $i_category . " AND Category=1");
    if (empty($s_search)) {
      return;
    }
    $i_min_items = (int)$this->getSettingValue("minCatItems");
    $i_num_rows = (int)$this->oDb->getValue("SELECT COUNT(ID) FROM " . $this->elementsDbTable . " WHERE Parent=" . $i_category);
    if ($i_num_rows >= $i_min_items) {
      return;
    }
    $r_result = $this->oDb->getResult("SELECT * FROM " . $this->elementsDbTable . " WHERE Parent=" . $i_category . " ORDER BY Category DESC, ID DESC");
    $a_search_url = explode("#key#", self::CATEGORY_URL);
    $s_search_url = implode(urlencode($s_search), $a_search_url);
    $a_found = $this->retrieveRadios($s_search_url);
    if (count($a_found) == 0) {
      return;
    }
    $a_foundShort = array();
    for ($i = 0; $i < count($a_found); $i++) {
      $a_foundShort[] = $a_found[$i]['playlist'];
    }
    $a_existing = array();
    while (($arr = $this->oDb->fetch($r_result)) !== NULL) {
      if ($arr['Category'] == 0) {
        $a_existing[] = $arr['PlaylistUrl'];
      }
    }
    $a_diff = array_diff($a_foundShort, $a_existing);
    for ($i = 0; $i < count($a_diff); $i++) {
      for ($j = 0; $i < count($a_found); $j++) {
        if ($a_diff[$i] == $a_found[$j]['playlist']) {
          $this->oDb->getResult("INSERT INTO " . $this->elementsDbTable . "(Title, PlaylistUrl, Parent) VALUES('" . addslashes($a_found[$j]['title']) . "', '" . $a_found[$j]['playlist'] . "', " . $i_category . ")");
          break;
        }
      }
    }
  }

  /**
   * Searches for stations.
   */
  protected function search($s_search, $i_page = 0, $i_items_per_page = 25) {
    $a_search = explode("#key#", self::SEARCH_URL);
    $s_search_url = implode(urlencode($s_search), $a_search);
    return array('value' => $this->retrieveRadios($s_search_url), 'status' => self::SUCCESS_VAL);
  }

  /**
   * Retrieves radios.
   */
  protected function retrieveRadios($s_url) {
    $s_data = $this->readUrl($s_url);
    if (empty($s_data)) {
      return array('value' => "Can't connect to Icecast directory. The reason is server firewall or the site is down.", 'status' => self::FAILED_VAL);
    }
    $a_data = explode('<tr class="row', $s_data);
    array_shift($a_data);
    $a_radios = array();
    for ($i = 0; $i < count($a_data); $i++) {
      if (strpos($a_data[$i], 'title="More MP3 streams"') !== FALSE) {
        $s_id = $this->getStringPart($a_data[$i], 'href="/listen/', '/listen.m3u"');
        $a = $this->getTagAttributes($a_data[$i], 'a');
        $s_url = $a['href'];
        $s_title = trim(strip_tags($this->getTagContents($a_data[$i], 'a')));
        $a_radios[] = array('title' => $s_title, 'playlist' => str_replace("#id#", $s_id, self::M3U_URL));
      }
    }
    return $a_radios;
  }

  /**
   * Gets XML tag contents.
   */
  protected function getTagContents($s_data, $s_tag) {
    $a_data = explode("<" . $s_tag, $s_data, 2);
    if (strpos($a_data[1], ">") > 0) {
      $a_data = explode(">", $a_data[1], 2);
      $s_data = $a_data[1];
    }
    else {
      $s_data = substr($a_data[1], 1);
    }
    $a_data = explode("</" . $s_tag . ">", $s_data, 2);
    $s_data = $a_data[0];
    $i_cdata_index = strpos($s_data, "<![CDATA[");
    if (is_numeric($i_cdata_index) && $i_cdata_index == 0) {
      return $this->getStringPart($s_data, "<![CDATA[", "]]>");
    }
    return $s_data;
  }

  /**
   * Gets XML tag attributes.
   */
  protected function getTagAttributes($s_data, $s_tag) {
    $a_data = explode("<" . $s_tag, $s_data, 2);
    $i_tag_index1 = strpos($a_data[1], "/>");
    $i_tag_index = strpos($a_data[1], ">");
    if (!is_integer($i_tag_index1) || $i_tag_index1 > $i_tag_index) {
      $a_data = explode(">", $a_data[1], 2);
    }
    else {
      $a_data = explode("/>", $a_data[1], 2);
    }			
    $s_attributes = str_replace("'", '"', trim($a_data[0]));
    $a_attributes = array();
    $s_pattern = '(([^=])+="([^"])+")';
    preg_match_all($s_pattern, $s_attributes, $a_matches);
    $a_matches = $a_matches[0];
    for ($i = 0; $i < count($a_matches); $i++) {
      $a_data = explode('="', $a_matches[$i]);
      $a_attributes[trim($a_data[0])] = substr($a_data[1], 0, strlen($a_data[1])-1);
    }
    return $a_attributes;
  }

  /**
   * Gets HTML text part.
   */
  protected function getStringPart($s_data, $s_left, $s_right) {
    $a_parts = explode($s_left, $s_data, 2);
    $a_parts = explode($s_right, $a_parts[1], 2);
    return count($a_parts) == 2 ? $a_parts[0] : "";
  }

  /**
   * Defines if given text is url.
   */
  protected function isUrl($s_url) {
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $s_url);
  }

  /**
   * Decodes ASCII.
   */
  protected function decodeAscii($s_text) {
    $s_text = preg_replace_callback("/%([^%]{2})/", array($this, 'decodeAsciiCallback'), $s_text);
    return $s_text;
  }

  /**
   * ASCII decoding handler.
   */
  protected function decodeAsciiCallback($a_params) {
    return chr(hexdec($a_params[1]));
  }
}
