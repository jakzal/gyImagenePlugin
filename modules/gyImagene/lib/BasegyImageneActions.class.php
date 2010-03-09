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
 * @subpackage  gyImagene
 * @author      Jakub Zalas <jakub@zalas.pl>
 */
class BasegyImageneActions extends sfActions
{
  /**
   * @param sfWebRequest $request
   */
  public function executeShow(sfWebRequest $request)
  {
    try 
    {
      $thumbnail = $this->createThumbnailFromRequest($request);

      $this->setResponseHeadersForThumbnail($thumbnail);

      $this->getResponse()->setContent(file_get_contents($thumbnail->getPath()));
    }
    catch (Exception $exception) 
    {
      $this->getLogger()->err($exception->getMessage());

      $this->forward404();
    }

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request 
   * @return gyThumbnailCache
   */
  protected function createThumbnailFromRequest(sfWebRequest $request)
  {
    $dir = sfConfig::get('app_gy_imagene_plugin_files_dir', sfConfig::get('sf_upload_dir'));
    $fileName = $this->normalizeFileName($request->getParameter('file_name'));
    $filePath = realpath($dir . DIRECTORY_SEPARATOR . $fileName);

    $thumbnail = new gyThumbnailCache(
      $filePath, 
      $request->getParameter('width', null), 
      $request->getParameter('height', null),
      $request->getParameter('scale', true),
      $request->getParameter('inflate', true)
      /*null, null, null, true, array('method' => 'shave_bottom')*/
    );

    return $thumbnail;
  }

  /**
   * @param sfThumbnailCache $thumbnail 
   * @return null
   */
  protected function setResponseHeadersForThumbnail($thumbnail)
  {
    $response = $this->getResponse();

    $response->addCacheControlHttpHeader('max_age=31536000');
    $response->setContentType($this->getMimeTypeByPath($thumbnail->getPath()));
    $response->setHttpHeader('Last-Modified', $response->getDate($thumbnail->getCacheMTime()));
    $response->setHttpHeader('Expires', $response->getDate(strtotime("+ 1 year")));
    $response->setHttpHeader('Pragma', '', false);
  }

  protected function getMimeTypeByPath($path)
  {
    $extension = preg_replace('/^.*\.([^.]+)$/', '$1', $path);

    return sprintf('image/%s', $extension);
  }

  protected function normalizeFileName($fileName)
  {
    return preg_replace('/^([^(]+).*(\.[^()]+)$/', '$1$2', $fileName);
  }
}

