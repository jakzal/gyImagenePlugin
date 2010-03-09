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

  public function testImageIsNotModifiedIfNoParameterIsPassed()
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

  public function testAspectRatioIsPreservedWithWidthAndScaleParameters()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:40)(s:1).png', 200)->
      imageHasWidth(40)->
      imageHasHeight(20)
    ;
  }

  public function testAspectRatioIsNotPreservedWithWidthAndNoScaleParameter()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:40)(s:0).png', 200)->
      imageHasWidth(40)->
      imageHasHeight(80)
    ;
  }

  public function testAspectRatioIsPreservedWithHeightAndScaleParameters()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(h:20)(s:1).png', 200)->
      imageHasWidth(40)->
      imageHasHeight(20)
    ;
  }

  public function testAspectRatioIsNotPreservedWithHeightAndNoScaleParameter()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(h:20)(s:0).png', 200)->
      imageHasWidth(160)->
      imageHasHeight(20)
    ;
  }

  public function testAspectRatioIsPreservedWithWidthHeightAndScaleParameter()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:40)(h:40)(s:1).png', 200)->
      imageHasWidth(40)->
      imageHasHeight(20)
    ;
  }

  public function testAspectRatioIsNotPreservedWithWidthHeightAndNoScaleParameter()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:40)(h:25)(s:0).png', 200)->
      imageHasWidth(40)->
      imageHasHeight(25)
    ;
  }

  public function testImageExceedsItsWidthWithoutInflateParameter()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:200).png', 200)->
      imageHasWidth(200);
    ;
  }
 
  public function testImageExceedsItsHeightWithoutInflateParameter()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(h:100).png', 200)->
      imageHasHeight(100);
    ;
  }

  public function testImageDoesNotExceedItsWidthWithInflateParameterOff()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:200)(i:0).png', 200)->
      imageHasWidth(160);
    ;
  }
 
  public function testImageDoesNotExceedItsHeightWithInflateParameterOff()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(h:100)(i:0).png', 200)->
      imageHasHeight(80);
    ;
  }

  public function testImageExceedsItsWidthWithInflateParameterOn()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(w:200)(i:1).png', 200)->
      imageHasWidth(200);
    ;
  }
 
  public function testImageExceedsItsHeightWithInflateParameterOn()
  {
    $this->
      getAndCheck('gyImagene', 'show', '/imagene/logo-goyello-160x80(h:100)(i:1).png', 200)->
      imageHasHeight(100);
    ;
  }

  public function testWidthIsIncludedInCachedFileName()
  {
    $this->get('/imagene/logo-goyello-160x80(w:120).png');

    $this->test()->ok(file_exists($this->getCachePath() . 'logo-goyello-160x80_11_120_.png'));
  }

  public function testHeightIsIncludedInCachedFileName()
  {
    $this->get('/imagene/logo-goyello-160x80(h:60).png');

    $this->test()->ok(file_exists($this->getCachePath() . 'logo-goyello-160x80_11__60.png'));
  }

  public function testScaleIsIncludedInCachedFileNameWhenItIsOn()
  {
    $this->get('/imagene/logo-goyello-160x80(s:1).png');

    $this->test()->ok(file_exists($this->getCachePath() . 'logo-goyello-160x80_11__.png'));
  }

  public function testScaleIsIncludedInCachedFileNameWhenItIsOff()
  {
    $this->get('/imagene/logo-goyello-160x80(s:0).png');

    $this->test()->ok(file_exists($this->getCachePath() . 'logo-goyello-160x80_01__.png'));
  }

  public function testInflateIsIncludedInCachedFileNameWhenItIsOn()
  {
    $this->get('/imagene/logo-goyello-160x80(i:1).png');

    $this->test()->ok(file_exists($this->getCachePath() . 'logo-goyello-160x80_11__.png'));
  }

  public function testInflateIsIncludedInCachedFileNameWhenItIsOff()
  {
    $this->get('/imagene/logo-goyello-160x80(i:0).png');

    $this->test()->ok(file_exists($this->getCachePath() . 'logo-goyello-160x80_10__.png'));
  }

  public function testAllParametersAreIncludedInCachedFileName()
  {
    $this->get('/imagene/logo-goyello-160x80(w:120)(h:40)(s:0)(i:0).png');

    $this->test()->ok(file_exists($this->getCachePath() . 'logo-goyello-160x80_00_120_40.png'));
  }

  public function testThumbnailModificationTimeIsTheSameAsOriginalFile()
  {
    $filesDirectory   = sfConfig::get('app_gy_imagene_plugin_files_dir', '') . DIRECTORY_SEPARATOR;
    $filePath         = $this->getCachePath() . 'logo-goyello-160x80_11_110_.png';
    $originalFilePath = $filesDirectory . 'logo-goyello-160x80.png';

    $this->get('/imagene/logo-goyello-160x80(w:110).png');

    $this->test()->cmp_ok(filemtime($filePath), '===', filemtime($originalFilePath), 'Thumbnail modification time is set the same as modification time of original file');
  }

  protected function getCachePath()
  {
    return sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'gyThumbnailCache' . DIRECTORY_SEPARATOR;
  }
}

$test = new gyImageneActionsTest(new sfBrowser());
$test->run();

