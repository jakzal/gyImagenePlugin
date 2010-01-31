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
   * @param string $module 
   * @param string $action 
   * @param integer $statusCode 
   * @return gyTestFunctionalImagene
   */
  public function isModuleAndAction($module, $action, $statusCode = 200)
  {
    $this->with('request')->begin()->
      isParameter('module', $module)->
      isParameter('action', $action)->
    end()->

    with('response')->isStatusCode($statusCode);

    return $this;
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
      if (0 === strpos($method->getName(), 'test') && 0 === $method->getNumberOfParameters())
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

