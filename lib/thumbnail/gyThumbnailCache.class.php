<?php
/*
 * (c) 2007 Jonathan R. Todd <jtodd@adventexdesign.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * This is taken from Harry Fueck's Thumbnail class and 
 * converted for PHP5 strict compliance for use with symfony.
 * 
 * ..and was modified to preserve transparency in gif/png images  
 *
 * @package 		gyImagenePlugin
 * @version    	SVN: $Id$
 * @author 			Jonathan R. Todd <jtodd@adventexdesign.com>
 */
class gyThumbnailCache
{
  /**
	 * Path to file or url of file to create thumbnail from
	 * 
	 * @var string
	 * @access private
	 */
	private $file_master;
	
	/**
	 * Path of thumbnail to be created relative to the cache dir
	 * 
	 * @var string
	 * @access private
	 */
	private $file_thumb;
	
	/**
	 * Absolute path to the cache
	 * 
	 * @var string
	 * @access private
	 */
	private $cache_path;
	
	/**
	 * Path to cache relative to the web root
	 * 
	 * @var string
	 * @access private
	 */
	private $cache_url_path;
	
	/**
	 * Thumbnail width
	 * 
	 * @var int
	 * @access private
	 */
	private $width;
	
	/**
	 * Thumbnail height
	 * 
	 * @var int
	 * @access private
	 */
	private $height;
	
	/**
   * Whether to scale image to fit thumbnail (true) or
   * strech to fit (false)
   * @access private
   * @var boolean
   */
  private $scale;

  /**
   * Whether to inflate images smaller than the thumbnail
   *
   * @var boolean
   */
  private $inflate;
  
  /**
	 * Thumbnail quality
	 * 
	 * @var int
	 * @access private
	 */
	private $quality=90;
	
	/**
	 * Mime type for the thumbnail
	 * 
	 * @var string
	 */
	private $type = null;
	
	/**
	 * Image magick adapter options
	 * @var array
	 */
	private $options = array();
	
  function __construct($file_master = null, $width = null, $height = null,
                       $scale = true, $inflate = true, $type = null,
                       $file_thumb = null, $mtime = null, $create_thumb = true, 
                       $options = array())
  {
    if(!$file_master)
      throw new sfException('Please instantiate gyThumbnailCache with atleast a master file');
    else
      $this->file_master = $file_master;
    
    if($file_thumb)
      $this->file_thumb = $file_thumb;
    else
      $this->file_thumb = $file_master;
      
    if($width)
      $this->width = (int)$width;
    if($height)
      $this->height = (int)$height;
    if($type)
      $this->type = (string)$type;
      
    $this->scale = $scale;
    $this->inflate = $inflate;
        
    $this->cache_path = sfConfig::get('sf_upload_dir').'/gyThumbnailCache';
    $this->cache_url_path = '/uploads/gyThumbnailCache';
    
    $this->mtime = $mtime ? $mtime : null;
    
    $this->options = (is_array($options)?$options:array());
    if (!isset($this->options['convert']))
    {
      $this->options['convert'] = sfConfig::get('app_thumbnail_cache_image_magick_convert');
    }
    if (!isset($this->options['identify']))
    {
      $this->options['convert'] = sfConfig::get('app_thumbnail_cache_image_magick_identify');
    }

    if($create_thumb)
    {
      if(!$this->cacheIsValid())
      {
        //echo "<br>Making thumb<BR>";
        $this->createThumbnail();
      }
    }
  }
  
  /**
   * Instantiates and returns sfThumbnailCache object
   */
  public static function getInstance($file_master = null, $width = null, $height = null,
                                     $scale = true, $inflate = true, $type = null,
                                     $file_thumb = null,$mtime = null, $create_thumb = true, 
                                     $options = array())
  {
    static $gyThumbnailCache;
    
    if( !isset($gyThumbnailCache) )
    {
      // Get instance
		  $className = 'gyThumbnailCache';
		  $gyThumbnailCache = new $className($file_master, $width, $height, $scale, $inflate, $type, $file_thumb, $mtime, $create_thumb, $options);	
    }

    return $gyThumbnailCache;
  }
  
  /**
   * Instantiates and returns a new gyThumbnailCache object
   */
  public static function getNewInstance($file_master = null, $width = null, $height = null,
                                     $scale = true, $inflate = true, $type = null,
                                     $file_thumb = null,$mtime = null, $create_thumb = true, 
                                     $options = array())
  {
    // Get instance
	  $className = 'gyThumbnailCache';
	  $gyThumbnailCache = new $className($file_master, $width, $height, $scale, $inflate, $type, $file_thumb, $mtime, $create_thumb, $options);	
    return $gyThumbnailCache;
  }
  
  /**
   * Instantiates and returns a new gyThumbnailCache object without trying to create thumbnail
   */
  public static function getNewInstanceNoMaster($file_thumb = null,$width = null, $height = null,
                                     $scale = true, $inflate = true, $type = null, $mtime = null, 
                                     $create_thumb = true, $options = array())
  {
    // Get instance
	  $className = 'gyThumbnailCache';
	  $gyThumbnailCache = new $className('none', $width, $height, $scale, $inflate, $type, $file_thumb, $mtime, $create_thumb, $options);	
    return $gyThumbnailCache;
  }
  
  /**
   * Does cached thumbnail exist?
   */
  public function exists()
  {
    return (@GetImageSize($this->getPath())) ? true : false;
  }

  /**
   * Delete thumbnail
   */
  public function delete()
  {
    if(!$this->exists())
      throw new sfException('Can\'t delete photo which doesn\'t exist');
      
    if(!unlink($this->getPath()))
      throw new sfException('Coudn\'t delete photo: '.$this->getPath());
  }

  /**
   * Get thumbnail URL
   */
  public function getURL()
  {
    return $this->cache_url_path.$this->getDir().'/'.$this->getFilename();
  }
  
  /**
   * Get absolute path to thumbnail 
   */
  public function getPath()
  {
    return $this->cache_path.$this->getDir().'/'.$this->getFilename();
  }
  
  /**
   * Get absolute path to thumbnail dir
   */
  public function getDir()
  {
    return dirname($this->file_thumb);
  }
  
  /**
   * Get thumbnail width
   */
  public function getWidth()
  {
     return $this->width;
   }
   
  /**
   * Get thumbnail height
   */
  public function getHeight()
  {
      return $this->height;
    }
  
  /**
   * Is the cached file still valid based on mtime and cache time limit
   * 
   * If mtime is set and is older or equal to cached file then cache is valid
   * If mtime is not set and cache life is less than cache time limit then cache is valid
   */
  public function cacheIsValid()
  {

    if(!$this->exists())
      return false;
    // echo "Mtime: ".date('Y-m-d h:i:s T',$this->mtime).' '.$this->mtime.'<br>'.
    //          "Cache mtime: ".date('Y-m-d h:i:s T',$this->getCacheMTime()).' '.$this->getCacheMTime().'<br>';
    // Use mtime if it's set
    if($this->mtime)
    {
      // File requested is newer than cache
      if($this->mtime > $this->getCacheMTime())
        return false;

      // File requested is older or same as cache
      return true ; 
    }

    // Use cache life time
    $cache_life = time() - $this->getCacheMTime();
    $valid_cache_life = sfConfig::get('app_thumbnail_cache_cache_file_life');
    return ($cache_life > $valid_cache_life) ? false : true;

  }
  
  /**
   * Get modification time of cached file
   */
  public function getCacheMTime()
  {
    return filemtime($this->getPath());
  }
  
  /**
   * Get name of thumbnail file
   */
  private function getFilename()
  {
    return $this->stripExt(basename($this->file_thumb)).
           '_'.$this->width.
           '_'.$this->height.
           '.'.$this->getExt($this->file_thumb);
  }
  
  /**
   * Create the thumbnail and save the file
   */
  private function createThumbnail()
  {
    // Create thumb dir if it doesn't exist
    $this->createDir($this->cache_path.$this->getFilePathThumb());
    
    // We need to create a thumbnail
    // $thumb = new gyThumbnail($this->width, $this->height, $this->scale, $this->inflate, $this->quality);
    $thumb = new gyThumbnail($this->width, $this->height, $this->scale, $this->inflate, $this->quality, 'gyImageMagickAdapter', $this->options);
    $thumb->loadFile($this->file_master);
    $thumb->save($this->getPath(), $this->type);
    $thumb->freeAll();
    
    // Set the access and modification to match S3 time
    if($this->mtime)
    {
      if(!touch($this->getPath(),$this->mtime))
        throw new Exception("Could not set last modification time");
    }
    
    return $this->getPath();
  }
  
  /**
   * Create a directory if it doesn't already exist
   */
  private function createDir($dir)
  {
    // Create thumb dir if it doesn't exist
    if(!is_dir($dir))
    {
      if(!file_exists($dir))
      {
        if(!mkdir($dir,0777,true))
          throw new sfException('Could not create dir: '.$dir);
      }
      else
        throw new sfException('gyThumbnailCache Tried to create dir \''.$dir.'\' but there was a file with the same name');
    }
  }
  
  private function getFilenameMaster()
  {
    return basename($this->file_master);
  }
  
  private function getFilenameThumb()
  {
    return basename($this->file_thumb);
  }
  
  private function getFilePathMaster()
  {
    return dirname($this->file_master);
  }
  
  private function getFilePathThumb()
  {
    return dirname($this->file_thumb);
  }

  /**
   * Get extension from a file name
   * 
   */
  private function getExt($f) 
  {
    $tmp = strrpos($f, '.'); // finds the last occurence of .
    if ($tmp=='0') 
      return '';
    return substr($f, $tmp+1);
  }
  
  /**
   * Remove extension from a file name
   * 
   */
  private function stripExt($f)
  {
    return substr($f, 0, strrpos($f, '.'));
  }
  
  /**
   * This function takes a s3fs bucket and key and returns thumbnail path.
   * If thumb doesn't already exist master is pulled from s3.
   * 
   * s3fs id's are of the form: 'mtime_MT_/bucket/key' like:
   * "1193674271_MT_/adventex/c21hs/employee_cma_photos/3603.jpg"
   * 
   */
  public static function gy_thumbnail_s3_thumbnail($bucket,$key,$file_thumb,$w,$h,$mtime)
  {
    $thumb = gyThumbnailCache::getNewInstanceNoMaster($file_thumb,$w,$h,true,true,null,$mtime);
    // Valid thumb exists
    if($thumb->cacheIsValid())
    {
      return $thumb->getPath();
    }
    // Valid thumb doesn't exist
    else
    {
      // Try to get master file   
      $file = gyAmazonS3File::getNewInstance($key,$bucket,$mtime);      

      // Master exists, create thumbnail
      if($file->fileExists())
      {
        $file_master = $file->getPath();
        $master_mtime = $file->fileGetMTime();
        //echo "Mtime: $master_mtime"."<br><br>".print_r($file->s3GetFileMetaData(),1);
        // Create thumb from master
        $thumb = gyThumbnailCache::getNewInstance($file_master,$w,$h,true,true,null,
          $file_thumb,$master_mtime);

        return $thumb->getPath();
      }

      // Master doesn't exists
      return false;
    } 
  }
  
  /**
   * This function takes a file and returns a path to it's thumb
   * 
   */
  public static function gy_thumbnail_cache_path($photo,$w = null,$h = null,$options = null)
  {
    $w = $w ? $w : 0;
    $h = $h ? $h : 0;

    $thumb = gyThumbnailCache::getNewInstance($photo,$w,$h);

    return $thumb->getPath();
  }

  
}
