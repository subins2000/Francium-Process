<?php
namespace Francium;

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
    $this->options = $options;
    self::setOS();
  }
  
  public function start(){
    if(self::$os === "linux" || self::$os === "mac"){
      $this->startOnNix();
    }
  }
  
  public function startOnWindows(){
    
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
        $arguments = " " . escapeshellarg($value);
      }else{
        $arguments = " $option " . escapeshellarg($value);
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
    
    $cmd = $this->cmd . $arguments . $output . " &";
    var_dump($cmd);
    exec($cmd);
  }
  
}
