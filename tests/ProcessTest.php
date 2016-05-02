<?php
use Fr\Process;

class ProcessTest extends PHPUnit_Framework_TestCase {

  public function testStartAProcess(){
    $tmpFile = tempnam(sys_get_temp_dir(), "FranciumProcess");
    $PR = new Process($this->getPHPExecutable(), array(
      "arguments" => array(
        "-r" => "echo 'hello';echo 'world';"
      ),
      "output" => $tmpFile
    ));
    $PR->start();
    
    $this->assertNotEquals("helloworld", file_get_contents($tmpFile));
    
    /**
     * Let the bg process complete
     */
    sleep(1);
    $this->assertEquals("helloworld", file_get_contents($tmpFile));
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
