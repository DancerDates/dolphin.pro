<?php

/**
 * @file
 * Database handler class.
 */

class RzDbConnect {
  var $sHost;
  var $iPort;
  var $iSocket;
  var $sDb;
  var $sUser;
  var $sPassword;
  var $bConnected;
  var $rLink;
  
  public function __construct($sHost, $iPort, $iSocket, $sDb, $sUser, $sPassword) {
    $this->bPrintLog = true;
    $this->sHost = $sHost;
    $this->iPort = $iPort;
    $this->iSocket = $iSocket;
    $this->sDb = $sDb;
    $this->sUser = $sUser;
    $this->sPassword = $sPassword;
    $this->bConnected = false;
  }

  public function connect() {
    if($this->bConnected)
      return;
    $dbHost = strlen($this->iPort) ? $this->sHost . ":" . $this->iPort : $this->sHost;
    $dbHost .= strlen($this->iSocket) ? ":" . $this->iSocket : "";
    @$this->rLink = mysql_connect($dbHost, $this->sUser, $this->sPassword);
    if($this->rLink)
      $this->bConnected = true;
    else
      $this->bConnected = false;
    @mysql_select_db($this->sDb, $this->rLink);
    mysql_query("SET NAMES 'utf8'", $this->rLink);
    mysql_query("SET @@local.wait_timeout=9000;", $this->rLink);
    mysql_query("SET @@local.interactive_timeout=9000;", $this->rLink);
  }

  public function disconnect() {
    mysql_close($this->rLink);
    $this->bConnected = false;
  }

  public function reconnect() {
    $this->disconnect();
    $this->connect();
  }
	
  /**
   * Gets query result.
   */
  public function getResult($s_query) {
    return mysql_query($s_query);
  }

  /**
   * Gets array.
   */
  public function getArray($s_query) {
    return $this->fetch($this->getResult($s_query));
  }

  /**
   * Gets single value.
   */
  public function getValue($s_query) {
    $aResult = mysql_fetch_array($this->getResult($s_query));
    return $aResult[0];
  }

  /**
   * Fetches query record.
   */
  public function fetch($r_result) {
    $a_res = mysql_fetch_assoc($r_result);
    if (!$a_res) {
      $a_res = NULL;
    }
    return $a_res;
  }
}
