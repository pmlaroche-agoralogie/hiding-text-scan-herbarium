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
            $id_method = (int) explode('-',$_GET['method']);
            $id_process = (int) explode('-',$_GET['process']);
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
    }
}