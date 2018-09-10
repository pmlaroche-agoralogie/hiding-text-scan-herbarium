<?php
class Images {
    /**
     * @var Images
     */

    
    public function __construct() {
        
        
    }
    

    
    public function doAction()
    {
        switch($_GET['action']){
            case 'setImages':
                $this->setImages();
                break;
        }
        
    }
    
    protected function setImages()
    {
        global $content;

        $files = scandir(_IMAGES_ORIGIN_DIR_);
        $total = 0;
        foreach ($files as $file)
        {
            if (($file != '.' && $file != '..') && ($file != '.DS_Store') && ($file[0] != '.')) 
            {
                $aFile = explode('.',$file);
                $secureFile = Db::getInstance()->quote($file);
                $secureIdFile = Db::getInstance()->quote($aFile[0]);
                $sql = "INSERT IGNORE INTO " . DB_PREFIXE . "images (id_filename,filename) VALUES (".$secureIdFile.",".$secureFile.")";
                Db::getInstance()->query($sql);
                if (Db::getInstance()->numRows())
                    $total++;
            }
        }
        $content =  $total." nouvelles images ajoutées à la base";
    }
}
