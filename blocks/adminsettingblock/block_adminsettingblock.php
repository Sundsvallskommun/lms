
<?php

/**
 * För utvecklingssamtal mellan chef och medarbetare
 *
 * @package    block_utvsamtal
 * @author     Charlotte Englander
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class block_adminsettingblock extends block_base
{

  function init()
  {
    $this->title = get_string('pluginname', 'block_adminsettingblock');
  }

  function has_config()
  {
    return true;
  }

  public function instance_allow_multiple()
  {
    return false;
  }

  function get_content()
  {
    $header = get_string('pluginname', 'block_adminsettingblock');
    $body = '';
    $footer = '';
    
    $configHiddenCourses = 'show';

    if (!empty($this->config->hiddencourses)) {
      $configHiddenCourses = $this->config->hiddencourses;
    }

    if($configHiddenCourses == 'hide'){
      $body = get_string('hiddencourseshidden', 'block_adminsettingblock');
    }
    else if($configHiddenCourses == 'show'){
      $body = get_string('hiddencoursesshown', 'block_adminsettingblock');
    }

    $this->content = new stdClass;
    $this->title = $header;
    $this->content->text = $body;
    $this->content->footer = $footer;



   

    return $this->content;
  }
}
