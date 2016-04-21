<?php
namespace Fr;

class Process{

  public static $os = null;
  public $cmd = null, $options = array(
    "output" => null,
    "arguments" => array()
  );
  
  private static function setOS(){
    if(self::$os === null){
      /**
       * Get the OS
       */
      $os = strtolower(substr(php_uname('s'), 0, 3));
      if ($os == 'lin') {
        self::$os = "linux";
      }else if ($os == 'win') {
        self::$os = "windows";
      }else if ($os == 'mac') {
        self::$os = "mac";
      }
    }
  }
  
  /**
   * 
   */
  public function __construct($cmd, $options){
    $this->cmd = $cmd;
    $this->options = array_replace_recursive($this->options, $options);
    self::setOS();
  }
  
  public function start(){
    if(self::$os === "linux" || self::$os === "mac"){
      return $this->startOnNix();
    }else if(self::$os === "windows"){
      return $this->startOnWindows();
    }
  }
  
  public function startOnWindows(){
    /**
     * Make Arguments
     */
    $arguments = "";
    foreach($this->options["arguments"] as $option => $value){
      if(is_numeric($option)){
        $arguments .= " " . escapeshellarg($value);
      }else{
        $arguments .= " $option " . escapeshellarg($value);
      }
    }
    
    /**
     * Where to output
     */
    if($this->options["output"] === null){
      $outputFile = "/dev/null";
    }else{
      $outputFile = $this->options["output"];
    }
    $output = " > " . escapeshellarg($outputFile);
    
    $cmd = escapeshellarg($this->cmd) . $arguments . $output;
    
    $bgCmd = escapeshellarg(self::getPHPExecutable()) . " " . escapeshellarg(self::getBGPath()) . " " . escapeshellarg(base64_encode($cmd)) . " > /dev/null &";
    
    $WshShell = new COM("WScript.Shell");
    $oExec = $WshShell->Run($bgCmd, 0, false);
    
    return $cmd;
  }
  
  /**
   * *nix systems :
   *    Linux - Ubuntu, Debian...
   *    Unix - Mac
   */
  public function startOnNix(){
    /**
     * Make Arguments
     */
    $arguments = "";
    foreach($this->options["arguments"] as $option => $value){
      if(is_numeric($option)){
        $arguments .= " " . escapeshellarg($value);
      }else{
        $arguments .= " $option " . escapeshellarg($value);
      }
    }
    
    /**
     * Where to output
     */
    if($this->options["output"] === null){
      $outputFile = "/dev/null";
    }else{
      $outputFile = $this->options["output"];
    }
    $output = " > " . escapeshellarg($outputFile);
    
    $cmd = escapeshellarg($this->cmd) . $arguments . $output;
    
    $bgCmd = escapeshellarg(self::getPHPExecutable()) . " " . escapeshellarg(self::getBGPath()) . " " . escapeshellarg(base64_encode($cmd)) . " > /dev/null &";
    exec($bgCmd);
    
    return $cmd;
  }
  
  private function getBGPath(){
    return __DIR__ . "/ProcessBG.php";
  }
  
  private function getPHPExecutable() {
    if(defined("PHP_BINARY") && PHP_BINARY != ""){
      return PHP_BINARY;
    }else{
      $paths = explode(PATH_SEPARATOR, getenv('PATH'));
      foreach ($paths as $path) {
        // we need this for XAMPP (Windows)
        if (strstr($path, 'php.exe') && isset($_SERVER["WINDIR"]) && file_exists($path) && is_file($path)) {
          return $path;
        }else {
          $php_executable = $path . DIRECTORY_SEPARATOR . "php" . (isset($_SERVER["WINDIR"]) ? ".exe" : "");
          if (file_exists($php_executable) && is_file($php_executable)) {
            return $php_executable;
          }
        }
      }
    }
    return FALSE; // not found
  }
  
}
