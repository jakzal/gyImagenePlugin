<?php
/*
 * (c) 2007-2010 Jakub Zalas
 * (c) 2007-2010 GOYELLO IT Services 
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include(dirname(__FILE__) . '/../bootstrap/functional.php');

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
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80.png', 200)->
      isContentType('image/png')
    ;
  }

  public function testRequestingJpgFileReturnsJpgFile()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80.jpg', 200)->
      isContentType('image/jpg')
    ;
  }

  public function testImageHasToExist()
  {
    $this->getAndCheck('gyImagene', 'show', '/imagene/non-existing.png', 404);
  }

  public function testParametersAreRemovedFromImageName()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:80)(h:80).png', 200)->
      isContentType('image/png')
    ;
  }

  public function testImageIsNotModifiedIfNoParametersArePassed()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80.png', 200)->
      imageHasWidth(160)->
      imageHasHeight(80)
    ;
  }

  public function testImageIsScaledToGivenWidth()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:40).png', 200)->
      imageHasWidth(40)
    ;
  }

  public function testImageIsScaledToGivenHeight()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(h:40).png', 200)->
      imageHasHeight(40)
    ;
  }

  public function testAspectRatioIsPreservedIfOnlyWidthIsGiven()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:40).png', 200)->
      imageHasWidth(40)->
      imageHasHeight(20)
    ;
  }

  public function testAspectRatioIsPreservedIfOnlyHeightIsGiven()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(h:40).png', 200)->
      imageHasWidth(80)->
      imageHasHeight(40)
    ;
  }
}

$test = new gyImageneActionsTest(new sfBrowser());
$test->run();

