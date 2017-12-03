<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Media;

use JDZ\Tree\TreeItem;
use JDZ\Filesystem\File;
use JDZ\Filesystem\Path;
use JDZ\Helpers\StringHelper;

/**
 * Media File
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class MediaFile extends TreeItem
{
  /**
   * Fullpath to the file
   * 
   * @var    string 
   */
  protected $fullpath;
  
  /**
   * Root folder
   * 
   * @var    string 
   */
  protected $root;
  
   /**
   * The list of mimes to display
   * 
   * @var    array 
   */
  protected $mimes;
  
  /**
   * File path relative to the root folder
   * 
   * @var    string 
   */
  protected $relpath;
  
  /**
   * File name
   * 
   * @var    string 
   */
  protected $filename;
  
  /**
   * File name with no extension
   * 
   * @var    string 
   */
  protected $name;
  
  /**
   * File extension
   * 
   * @var    string 
   */
  protected $extension;
  
  /**
   * File type (image, pdf)
   * 
   * @var    string 
   */
  protected $ftype;
  
  /**
   * File size (Ko)
   * 
   * @var    float 
   */
  protected $filesize;
  
  /**
   * File unique alias
   * 
   * @var    string 
   */
  protected $alias;
  
  /**
   * {@inheritDoc}
   */
  public function toObject()
  {
    $data = (array) parent::toObject();
    
    $data['alias'] = $this->alias;
    $data['ftype'] = $this->ftype;
    $data['src']   = $this->src;
    // $data['absolute']   = $this->fullpath,
    // $data['extension']  = $this->extension,
    // $data['filesize']   = $this->filesize,
    
    return (object)$data;
  }
  
  /**
   * {@inheritDoc}
   */
  protected function load()
  {
    $app = Callisto();
    
    $this->fullpath = Path::clean($this->fullpath);
    $this->root     = Path::clean($this->root);
    $this->ftype    = '';
    $this->src      = '';
    
    $relpath = trim(str_replace([$this->root, DIRECTORY_SEPARATOR], ['', '/'], $this->fullpath), '/');
    $parts   = explode('/', $relpath);
    
    $this->filename  = array_pop($parts);
    $this->relpath   = implode('/', $parts);
    $this->name      = File::stripExt($this->filename);
    $this->extension = File::getExt($this->filename);
    
    $mime = mime_content_type($this->fullpath);
    
    if ( !preg_match("/^(".implode(')|(', $this->mimes).")$/", mime_content_type($this->fullpath)) ){
      return false;
    }
    
    $this->filesize = round(filesize($this->fullpath)/1000, 2);
    $this->value    = trim(str_replace([$this->root, DIRECTORY_SEPARATOR], ['', '/'], $this->fullpath), '/');
    $this->alias    = StringHelper::toSlug($this->value);
    
    if ( preg_match("/^image\/.+$/", $mime) ){
      $this->ftype = 'image';
      $this->src   = $app->getFileBaseUrl().'media/'.$this->value;
    }
    elseif ( preg_match("/^application\/pdf$/", $mime) ){
      $this->ftype = 'pdf';
      $this->src   = $app->getFileBaseUrl().'media/'.$this->value;
    }
    
    $this->text  = $this->getFilename();
    $this->valid = true;
  }
  
  /**
   * Get the text label
   * 
   * @return   string
   */
  protected function getFilename()
  {
    return $this->name.' ('.$this->extension.')';
  }
}