<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Media;

use JDZ\Tree\Tree;
use JDZ\Filesystem\File;
use JDZ\Filesystem\Folder;
use JDZ\Filesystem\Path;

/**
 * Media list Helper
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class MediaTree extends Tree
{
	/**
   * Root path
   * 
	 * @var    string 
	 */
  protected $root;
  
	/**
   * Media path
   * 
	 * @var    string 
	 */
  protected $fullpath;
  
 	/**
   * The list of mimes to display
   * 
	 * @var    array 
	 */
  protected $mimes;
  
	/**
	 * {@inheritDoc}
	 */
  protected function load()
  {
    $this->root = Path::clean($this->root);
    if ( empty($this->root) ){
      throw new \RuntimeException('Need to specify root folder in '.get_class());
    }
    
    $this->mimes = (array)$this->mimes;
    if ( empty($this->mimes) ){
      $this->mimes = [ 
        "image\/.+", 
        "application\/pdf",
      ];
    }
    
    $folder = new MediaFolder([ 
      'root'     => $this->root,
      'fullpath' => $this->fullpath,
      'mimes'    => $this->mimes,
    ]);
    $folder->set('text', 'Root');
    $folder->set('type', 'root');
    
    if ( $folder->isValid() ){
      $this->tree = $folder->toObject();
      
      // $this->select = $folder->toSelect();
    }
  }  
}