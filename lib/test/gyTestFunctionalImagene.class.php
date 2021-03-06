<?php
/*
 * (c) 2007-2010 Jakub Zalas
 * (c) 2007-2010 GOYELLO IT Services 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package     gyImagenePlugin
 * @subpackage  lib.test
 * @author      Jakub Zalas <jakub@zalas.pl>
 */
class gyTestFunctionalImagene extends sfTestFunctional
{
  /**
   * @param sfBrowserBase $browser 
   * @param lime_test $lime 
   * @param array $testers 
   * @return null
   */
  public function __construct(sfBrowserBase $browser, lime_test $lime = null, $testers = array())
  {
    parent::__construct($browser, $lime, $testers);

    $this->configure();
  }

  /**
   * @return null
   */
  public function configure()
  {
    $filesDir = sfConfig::get('sf_plugins_dir') 
      . DIRECTORY_SEPARATOR . 'gyImagenePlugin' 
      . DIRECTORY_SEPARATOR . 'test' 
      . DIRECTORY_SEPARATOR . 'images';

    sfConfig::set('app_gy_imagene_plugin_files_dir', $filesDir);
  }

  /**
   * @param string $type 
   * @return gyTestFunctionalImagene
   */
  public function isContentType($type)
  {
    $this->with('response')->isHeader('Content-Type', $type);

    return $this;
  }

  /**
   * @param integer $width 
   * @return gyTestFunctionalImagene
   */
  public function imageHasWidth($width)
  {
    $filePath = $this->saveResponseContent();

    list($imageWidth, $imageHeight) = getimagesize($filePath);

    $this->test()->cmp_ok($imageWidth, '===', $width, sprintf('Image width is "%s"', $width));

    return $this;
  }

  /**
   * @param integer $height
   * @return gyTestFunctionalImagene
   */
  public function imageHasHeight($height)
  {
    $filePath = $this->saveResponseContent();

    list($imageWidth, $imageHeight) = getimagesize($filePath);

    $this->test()->cmp_ok($imageHeight, '===', $height, sprintf('Image height is "%s"', $height));

    return $this;
  }

  /**
   * @return string
   */
  protected function saveResponseContent()
  {
    $content  = $this->getResponse()->getContent();
    $filePath = tempnam(sys_get_temp_dir(), 'imagene');
    file_put_contents($filePath, $content);

    return $filePath;
  }

  /**
   * Runs all test methods
   * 
   * @return null
   */
  public function run()
  {
    foreach ($this->getTestMethods() as $method)
    {
      $test = $method->getName();

      $this->info(sfInflector::humanize(sfInflector::underscore(substr($test, 4))));
      $this->setUp();
      $this->$test();
      $this->tearDown();
    }
  }

  /**
   * Finds all test methods using reflection
   * 
   * Every public method without arguments which 
   * name starts with 'test' is treated as a test method.
   * 
   * @return array
   */
  protected function getTestMethods()
  {
    $reflection  = new ReflectionClass($this);
    $methods     = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $testMethods = array();

    foreach ($methods as $method)
    {
      if (0 === strpos($method->getName(), 'test') && 0 === $method->getNumberOfParameters() && strlen($method->getName()) > 4)
      {
        $testMethods[] = $method;
      }
    }

    return $testMethods;
  }

  /**
   * @return null
   */
  protected function setUp()
  {
  }

  /**
   * @return null
   */
  protected function tearDown()
  {
  }
}

