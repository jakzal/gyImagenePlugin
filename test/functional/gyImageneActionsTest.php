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
    $this->get('/imagene/logo-goyello')->with('response')->isStatusCode(404);
  }

  public function testFileNameIsRequired()
  {
    $this->get('/imagene')->with('response')->isStatusCode(404);
  }

  public function testRequestingPngFileReturnsPngFile()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello.png', 200)->
      isContentType('image/png')
    ;
  }

  public function testRequestingJpgFileReturnsJpgFile()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello.jpg', 200)->
      isContentType('image/jpg')
    ;
  }

  public function testImageHasToExist()
  {
    $this->getAndCheck('gyImagene', 'show', '/imagene/non-existing.png', 404);
  }
}

$test = new gyImageneActionsTest(new sfBrowser());
$test->run();

