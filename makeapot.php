<?php
class MakeAPot {
  private $list_array = array();
  private $domain_string = "";
  private $folder_string = "";

  function __construct($folder, $domain) {
    // Set variables.
    $this->folder_string = $folder;
    $this->domain_string = $domain;
    // Scan & loop the extension folder.
    $this->startMakeAPot();
  }
  // Set items to translation list.
  function setArray($array) {
    $this->list_array = $array;
  }
  // Return unique translation list.
  function getArray() {
    $unique = array_unique($this->list_array);
    return $unique;
  }
  // Read folder recursive.
  private function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);
    foreach($files as $key => $value){
      $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
      if(!is_dir($path)) {
        $results[] = $path;
      } else if($value != "." && $value != "..") {
        $this->getDirContents($path, $results);
        $results[] = $path;
      }
    }
    return $results;
  }
  // Scan & loop the extension folder.
  function startMakeAPot() {
    $folder = $this->getDirContents($this->folder_string);
    foreach($folder as $key => $file) {
      if(is_file($file)) {
        // File content.
        $string = file_get_contents($file);
        // Check file extension.
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Get translations between tags.
        $this->getTextBetweenTags($string,$extension);
      }
    }
    // Write the .pot file.
    $this->writeThePot($this->list_array, $this->domain_string . '.pot');
  }
  // Get translations between tags.
  function getTextBetweenTags($string, $file_type) {
    // Get text(s).
    $array = $this->getArray();
    $pattern = "";
    // Set pattern for 'php' files.
    if($file_type == 'php') {
      // Create pattern.
      $part1 = "( ts\('(.*?)',)";
      $part2 = '( ts\("(.*?)",)';
      $pattern = '/' . $part1 . '|' . $part2 . '/';
    }
    // Set pattern for 'template' files.
    if($file_type == 'tpl') {
      // Create pattern.
      // $part1 = "({ts.*}(.*?){\/ts})";
      $part1 = "({ts [a-zA-Z0-9=$\. ]{0,}domain='".$this->domain_string."'}(.*?){\/ts})";
      $part2 = "({ts [a-zA-Z0-9=$\. ]{0,}domain='".$this->domain_string."'}(.*?){\/ts})";
      $pattern = '/' . $part1 . '|' . $part2 . '/';
    }
    // Set pattern for 'javascript' files.
    /*
    if ($file_type == 'js') {
      // Create pattern.
      $part1 = "(ts\('(.*?)')";
      $part2 = '(ts\("(.*?)")';
      $pattern = '/' . $part1 . '|'. $part2 . '/';
    }*/
    // Execute pattern.
    if($pattern != "") {
      // Match pattern.
      preg_match_all($pattern, $string, $match);
      // results part1
      foreach($match[2] as $value) {
        // Skip empty tags.
        if($value != '') { $array[] = $value; }
      }
      // results part2
      foreach($match[4] as $value) {
        // Skip empty tags.
        if($value != '') { $array[] = $value; }
      }
    }
    // Return text(s).
    $this->setArray($array);
  }
  // Write the .pot file.
  function writeThePot($array, $filename) {
    $fh = fopen($filename, 'w');
    fwrite($fh, "#\n");
    fwrite($fh, "msgid \"\"\n");
    fwrite($fh, "msgstr \"\"\n");
    foreach ($array as $key => $value) {
      $value = $value;
      fwrite($fh, "\n");
      fwrite($fh, "msgid \"$value\"\n");
      fwrite($fh, "msgstr \"\"\n");
    }
    fclose($fh);
  }
}
$makeAPot = new MakeAPot(dirname(__FILE__).DIRECTORY_SEPARATOR, "be.ctrl.discounter");
print "<pre>" . print_r($makeAPot->getArray(),true) . "</pre>";
?>