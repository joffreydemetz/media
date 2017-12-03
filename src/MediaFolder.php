<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Media;

use JDZ\Tree\TreeGroup;
use JDZ\Filesystem\Folder;
use JDZ\Filesystem\Path;
use JDZ\Helpers\StringHelper;

/**
 * Media Folder
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class MediaFolder extends TreeGroup 
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
   * Folder path relative to the root folder
   * 
   * @var    string 
   */
  protected $relpath;
  
  /** 
   * Folder name
   * 
   * @var    string 
   */
  protected $folder;
  
  /**
   * Folder unique alias
   * 
   * @var    string 
   */
  protected $alias;
  
  /**
   * {@inheritDoc}
   */
  protected function load()
  {
    $this->fullpath = Path::clean($this->fullpath);
    $this->root     = Path::clean($this->root);
    $this->groups   = [];
    $this->items    = [];
    
    $relpath = trim(str_replace([$this->root, DIRECTORY_SEPARATOR], ['', '/'], $this->fullpath), '/');
    $parts   = explode('/', $relpath);
    
    $this->folder  = array_pop($parts);
    $this->relpath = implode('/', $parts);
    
    $_folders = Folder::folders($this->fullpath);
    
    if ( count($_folders) ){
      foreach($_folders as $_folder){
        $folder = new MediaFolder([
          'fullpath' => $this->root.($this->relpath===''?'':'/'.$this->relpath).($this->folder===''?'':'/'.$this->folder).'/'.$_folder,
          'root'     => $this->root,
          'mimes'    => $this->mimes,
        ]);
        
        if ( $folder->isValid() ){
          $this->groups[] = $folder;
        }
      }
    }
    
    $_files = Folder::files($this->fullpath);
    
    if ( count($_files) ){
      foreach($_files as $_file){
        $file = new MediaFile([
          'fullpath' => $this->root.($this->relpath===''?'':'/'.$this->relpath).($this->folder===''?'':'/'.$this->folder).'/'.$_file,
          'root'     => $this->root,
          'mimes'    => $this->mimes,
        ]);
        
        if ( $file->isValid() ){
          $this->items[] = $file;
        }
      }
    }
    
    if ( empty($this->groups) && empty($this->items) ){
      return;
    }
    
    $this->value = trim(str_replace([$this->root, DIRECTORY_SEPARATOR], ['', '/'], $this->fullpath), '/');
    $this->alias = StringHelper::toSlug($this->value);
    $this->text  = $this->getFoldername();
    $this->valid = true;
  }
  
  /**
   * Get the text label
   * 
   * @return   string 
   */
  protected function getFoldername()
  {
    if ( $this->folder === '' ){
      return 'ROOT';
    }
    return $this->folder;
  }
}