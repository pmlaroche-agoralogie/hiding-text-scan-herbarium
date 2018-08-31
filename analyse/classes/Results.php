<?php
class Results {
    /**
     * @var Results
     */
    
    
    public function __construct() {
        
        
    }
    
    public function doAction()
    {
        switch($_GET['action']){
            case 'setResults':
                $this->setResults();
                break;

        }
        
    }
    
    protected function setResults()
    {
        global $content;
        
        if (isset($_GET['method']))
        {
            $id_method = (int)explode('-',$_GET['method'])[0];
            $id_process = (int) explode('-',$_GET['process'])[0];
            $this->addResults($id_method, $id_process);
        }
        
        $select = "";
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "method";
        Db::getInstance()->query($sql);
        $aResultMethod = Db::getInstance()->getAll();
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "process";
        Db::getInstance()->query($sql);
        $aResultProcess = Db::getInstance()->getAll();
        
        $optionsMethod = '';
        $optionsProcess = '';
        
        foreach($aResultMethod as $resultMethod)
        {
            $optionsMethod .= '<option value="'.$resultMethod['id_method'].'" 
                urlfriendlyname="'.$resultMethod['url_friendly_name'].'">'.$resultMethod['method'].'</option>';
        }
        if ($optionsMethod != '')
        {
            $select .= '<select id="method"><option disabled selected>method</option>'.$optionsMethod.'</select>';
        }
        
        foreach($aResultProcess as $resultProcess)
        {
            $optionsProcess .= '<option value="'.$resultProcess['id_process'].'" parent="'.$resultProcess['id_method'].'" 
                urlfriendlyname="'.$resultProcess['url_friendly_name'].'" style="display:none;">'.$resultProcess['version'].'</option>';
        }
        if ($optionsProcess != '')
        {
            $select .= '<select id="process"><option value="0" parent="all" disabled selected>version</option>'.$optionsProcess.'</select>';
        }
        
        if ($select !=  "")
        {    
            $select .=  file_get_contents(_TEMPLATES_DIR_.'Results/setResults.html');
        }
        
        $content .= $select;
    }
    
    protected function addResults($id_method,$id_process)
    {
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "method WHERE id_method = ".$id_method;
        Db::getInstance()->query($sql);
        $aResultMethod = Db::getInstance()->getAll();
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "process WHERE id_process = ".$id_process." AND id_method = ".$id_method;
        Db::getInstance()->query($sql);
        $aResultProcess = Db::getInstance()->getAll();
        
        if ($aResultProcess < 1)
            Tools::stopError('404','Not Found','Process not found in DB :'.$sql);
        
        print_r($aResultMethod);
        
        $path_results = _IMAGES_DIR_ . $aResultMethod[0]["method"] . "/" . $aResultProcess[0]["version"] . "/";
        
        $addResultsFunction = "addResults_".$aResultMethod[0]["method"]."_".$aResultProcess[0]["version"];
        
        $document = $this->$addResultsFunction($id_method,$id_process,$path_results);
        
            
    }
    
    protected function addResults_TensorFlow_model2($id_method,$id_process,$path_results)
    {
        echo $path_results;
        $files = scandir($path_results);
        
        $nb_img = 0;
        $nb_lines = 0;
        foreach ($files as $file)
        {
            if (($file != '.' && $file != '..') && ($file != '.DS_Store') && ($file[0] != '.'))
            {
                $pattern = '/^(.+)\-list\.txt$/';
                
                if (preg_match($pattern,$file,$match))
                {
                    $secureImage = Db::getInstance()->quote($match[1]);
                    $secureIDProcess = (int)$id_process;
                    $sql = "SELECT * FROM " . DB_PREFIXE . "images AS i
                        LEFT JOIN " . DB_PREFIXE . "results AS r ON r.id_images = i.id_images
                        WHERE i.filename = ".$secureImage." AND r.id_process=".$secureIDProcess;
                    Db::getInstance()->query($sql);
                    if (Db::getInstance()->numRows() == 0)
                    {
                        $contentFile = file_get_contents($path_results.$file);
                        
                        $aResults = Tools::fromTFArrayToPhpArray($contentFile);
                        //print_r($aResults);
                        
                        try 
                        {
                            //Begin transaction
                            Db::getInstance()->beginTransaction();
                            $sql_image = "SELECT id_images FROM " . DB_PREFIXE . "images WHERE filename = ".$secureImage;
                            $sql = "INSERT INTO " . DB_PREFIXE . "results (id_process,id_images) VALUES (".$id_process.",(".$sql_image."))";
                            //echo $sql."\n";
                            Db::getInstance()->query($sql);
                            $id_results = Db::getInstance()->lastInsertId();
                            //$id_results = 1;
                            
                            $nb_img++;
                            
                            foreach ($aResults as $result)
                            {
                                $sql = "INSERT INTO " . DB_PREFIXE . "results_details (id_results,y_top_left,x_top_left,y_bottom_right,x_bottom_right,constante,percentage)
                                            VALUES (".$id_results.", ".(float)$result[0].",".(float)$result[1].",".(float)$result[2].",
                                                    ".(float)$result[3].",".(float)$result[4].",".(float)$result[5].")";
                                Db::getInstance()->query($sql);
                                
                                $nb_lines++;
    
                            }
                            
                            //fin transaction
                            Db::getInstance()->commitTransaction();
                        }
                        catch (PDOException $e)
                        {
                            Db::getInstance()->rollbackTransaction();
                            Tools::stopError('404','Not found','PDO Transaction:'.$e->getMessage()."\t".$sql);
                        }
                        
    
                    }
                }
            }
            
        }
        echo 'nb images : '.$nb_img."<br>\n";
        echo 'nb lines : '.$nb_lines."<br>\n";
        
        
    }
    
    protected function addResults_OCR_hOCR($id_method,$id_process,$path_results)
    {
        echo $path_results;
        $files = scandir($path_results);
        
        $nb_img = 0;
        $nb_lines = 0;
        foreach ($files as $file)
        {
            if (($file != '.' && $file != '..') && ($file != '.DS_Store') && ($file[0] != '.'))
            {
                $pattern = '/^(.+)_\.hocr$/';
                
                if (preg_match($pattern,$file,$match))
                {
                    $secureImage = Db::getInstance()->quote($match[1]);
                    $secureIDProcess = (int)$id_process;
                    $sql = "SELECT * FROM " . DB_PREFIXE . "images AS i
                        LEFT JOIN " . DB_PREFIXE . "results AS r ON r.id_images = i.id_images
                        WHERE i.filename = ".$secureImage." AND r.id_process=".$secureIDProcess;
                    Db::getInstance()->query($sql);
                    if (Db::getInstance()->numRows() == 0)
                    {
                        $doc = new DOMDocument();
                        $doc->loadHTMLFile($path_results.$file);
                        
                        $finder = new DomXPath($doc);
                        $classname="ocr_line";
                        
                        $nodes = $finder->query("//span[contains(@class, '$classname')]");
                        foreach ($nodes as $node)
                        {
                            $attrTitle = $node->getAttribute("title");
                            $pos_bbox = strpos($attrTitle,"bbox");
                            $pos_point = strpos($attrTitle,";");
                            if ($pos_point !== false && $pos_bbox !== false) 
                            {
                                
                                $bbox = substr($attrTitle,$pos_bbox + 5,$pos_point - ($pos_bbox + 5));
                                $tab_bbox = explode(" ", $bbox);                                
                                $aResults[] = $tab_bbox;
                               /* if (count($tab_bbox)==4 && ( $largeur < 350 ) && ( $hauteur < 300 )){
                                    imagefilledrectangle ($im ,$tab_bbox[0],$tab_bbox[1],$tab_bbox[2],$tab_bbox[3],$white);
                                }*/
                            }
                        }
                        print_r($aResults);
                        //manque largeur image pour conversion %
                        
                        //TODO: convertir % et enregistrer, constant et percentage dans table = 1

                    }
                }
            }
        }
        echo 'nb images : '.$nb_img."<br>\n";
        echo 'nb lines : '.$nb_lines."<br>\n";
        
    }
}