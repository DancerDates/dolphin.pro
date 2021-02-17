<?php

/**
 * @file
 * Xml documents handler class.
 */

class RzXml {
  /**
   * Creates parser.
   */
  protected function createParser() {
    return xml_parser_create("UTF-8");
  }

  /**
   * Get the value of specified attribute for specified tag.
   */
  public function getAttribute($s_xml_content, $s_xml_tag, $s_xml_attribute) {
    $r_parser = $this->createParser();
    xml_parse_into_struct($r_parser, $s_xml_content, $a_values, $a_indexes);
    xml_parser_free($r_parser);
    $a_field_index = $a_indexes[strtoupper($s_xml_tag)][0];
    return $a_values[$a_field_index]['attributes'][strtoupper($s_xml_attribute)];
  }

  /**
   * Get an array of attributes for specified tag or an array of tags.
   */
  public function getAttributes($s_xml_content, $s_xml_tag_name, $s_xml_tag_index = -1) {
    $r_parser = $this->createParser();
    xml_parse_into_struct($r_parser, $s_xml_content, $a_values, $a_indexes);
    xml_parser_free($r_parser);
    // Gets two-dimensional array of attributes.
    if ($s_xml_tag_index == -1) {
      $a_result = array();
      $a_tag_indexes = $a_indexes[strtoupper($s_xml_tag_name)];
      if (count($a_tag_indexes) <= 0) {
        return NULL;
      }
      foreach ($a_tag_indexes as $i_tag_index) {
        $a_result[] = $a_values[$i_tag_index]['attributes'];
      }
      return $a_result;
    }
    else {
      $i_tag_index = $a_indexes[strtoupper($s_xml_tag_name)][$s_xml_tag_index];
      return $a_values[$i_tag_index]['attributes'];
    }
  }

  /**
   * Get an array of tags or one tag if its index is specified.
   */
  public function getTags($s_xml_content, $s_xml_tag_name, $i_xml_tag_index = -1) {
    $r_parser = $this->createParser();
    xml_parse_into_struct($r_parser, $s_xml_content, $a_values, $a_indexes);
    xml_parser_free($r_parser);
    // Get an array of tags.
    if ($i_xml_tag_index == -1) {
      $a_result = array();
      $a_tag_indexes = $a_indexes[strtoupper($s_xml_tag_name)];
      if (count($a_tag_indexes) <= 0) {
        return NULL;
      }
      foreach ($a_tag_indexes as $i_tag_index) {
        $a_result[] = $a_values[$i_tag_index];
      }
      return $a_result;
    }
    else {
      $i_tag_index = $a_indexes[strtoupper($s_xml_tag_name)][$i_xml_tag_index];
      return $a_values[$i_tag_index];
    }
  }

  /**
   * Gets the values of the given tag.
   */
  public function getValues($s_xml_content, $s_xml_tag_name) {
    $r_parser = $this->createParser();
    xml_parse_into_struct($r_parser, $s_xml_content, $a_values, $a_indexes);
    xml_parser_free($r_parser);
    $a_tag_indexes = $a_indexes[strtoupper($s_xml_tag_name)];
    $a_tag_indexes = isset($a_tag_indexes) ? $a_tag_indexes : array();
    $a_return_values = array();
    foreach ($a_tag_indexes as $i_tag_index) {
      $a_return_values[$a_values[$i_tag_index]['attributes']['KEY']] = isset($a_values[$i_tag_index]['value']) ? $a_values[$i_tag_index]['value'] : NULL;
    }
    return $a_return_values;
  }

  /**
   * Gets the value of the given tag.
   */
  public function getValue($s_xml_content, $s_xml_tag_name, $s_name) {
    $a_values = $this->getValues($s_xml_content, $s_xml_tag_name);
    return isset($a_values[$s_name]) ? $a_values[$s_name] : "";
  }

  /**
   * Sets the values of tag where attribute "key" equals to specified.
   */
  public function setValues($s_xml_content, $s_xml_tag_name, $a_key_values) {
    $r_parser = $this->createParser();
    xml_parse_into_struct($r_parser, $s_xml_content, $a_values, $a_indexes);
    xml_parser_free($r_parser);
    $a_tag_indexes = $a_indexes[strtoupper($s_xml_tag_name)];
    if (count($a_tag_indexes) == 0) {
      return $this->getContent();
    }
    foreach ($a_tag_indexes as $i_tag_index) {
      foreach ($a_key_values as $s_key => $s_value) {
        if ($a_values[$i_tag_index]['attributes']['KEY'] == $s_key) {
          $a_values[$i_tag_index]['value'] = $s_value;
          break;
        }
      }
    }
    return $this->getContent($a_values);
  }

  /**
   * Sets the value of tag where attribute "key" equals to specified.
   */
  public function setValue($s_xml_content, $s_xml_tag_name, $s_name, $s_value) {
    $a_key_values = array($s_name => $s_value);
    return $this->setValues($s_xml_content, $s_xml_tag_name, $a_key_values);
  }

  /**
   * Adds given values to XML content.
   */
  public function addValues($s_xml_content, $s_xml_tag_name, $a_key_values) {
    $r_parser = $this->createParser();
    xml_parse_into_struct($r_parser, $s_xml_content, $a_values, $a_indexes);
    xml_parser_free($r_parser);
    $a_tag_indexes = $a_indexes[strtoupper($s_xml_tag_name)];
    $i_last_tag_index = $a_tag_indexes[count($a_tag_indexes) - 1];
    $i_adds_count = count($a_key_values);
    $i_level = $a_values[$i_last_tag_index]["level"];

    for ($i = count($a_values) - 1; $i > $i_last_tag_index; $i--) {
      $a_values[$i + $i_adds_count] = $a_values[$i];
    }

    $i = $i_last_tag_index;
    foreach ($a_key_values as $s_key => $s_value) {
      $i++;
      $a_values[$i] = array(
        "tag" => $s_xml_tag_name,
        "type" => "complete",
        "level" => $i_level,
        "attributes" => array("KEY" => $s_key),
        "value" => $s_value,
      );
    }
    return $this->getContent($a_values);
  }

  /**
   * Get content in XML format from given values array.
   */
  public function getContent($a_values = array()) {
    $s_content = "";
    foreach ($a_values as $a_value) {
      $s_tag_name = strtolower($a_value['tag']);
      switch ($a_value['type']) {
        case "open":
          $s_content .= "<" . $s_tag_name . ">";
          break;

        case "complete":
          $s_content .= "<" . $s_tag_name;
          if (isset($a_value['attributes'])) {
            foreach ($a_value['attributes'] as $s_attr_key => $s_attr_value) {
              $s_content .= " " . strtolower($s_attr_key) . "=\"" . $s_attr_value . "\"";
            }
          }
          $s_content .= isset($a_value['value']) ? "><![CDATA[" . $a_value['value'] . "]]></" . $s_tag_name . ">" : " />";
          break;

        case "close":
          $s_content .= "</" . $s_tag_name . ">";
          break;
      }
    }
    return $s_content;
  }
}
