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
    return sfView::NONE;
  }
}

