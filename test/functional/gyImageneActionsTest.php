<?php
/*
 * (c) 2007-2010 Jakub Zalas
 * (c) 2007-2010 GOYELLO IT Services 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include(dirname(__FILE__) . '/../../../../test/bootstrap/functional.php');

/**
 * @package     gyImagenePlugin
 * @subpackage  test.functional
 * @author      Jakub Zalas <jakub@zalas.pl>
 */
class gyImageneActionsTest extends gyTestFunctionalImagene
{
  public function testFileExtensionIsRequired()
  {
    $this->info('File extension is required')->get('/imagene/default')->with('response')->isStatusCode(404);
  }

  public function testFileNameIsRequired()
  {
    $this->info('File name is required')->get('/imagene')->with('response')->isStatusCode(404);
  }

  public function testModule()
  {
    $this->
      get('/imagene/default.png')->
      isModuleAndAction('gyImagene', 'show', 200)
    ;
  }
}

$test = new gyImageneActionsTest(new sfBrowser());
$test->run();

