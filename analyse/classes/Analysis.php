<?php
class Analysis {
    /**
     * @var Analysis
     */
    
    
    public function __construct() {
        
        
    }
    
    public function doAction()
    {
        switch($_GET['action']){
            case 'getAnalysisZone':
                $this->getAnalysisZone();
                break;
            case 'getAnalysisZoneDisplay':
                $this->getAnalysisZoneDisplay();
                break;
                
        }
        
    }
    
    protected function getAnalysisZone()
    {
        global $content;
         
        $file = file_get_contents(_TEMPLATES_DIR_.'Analysis/getAnalysisZone.html');

       // $content .= file_get_contents(_TEMPLATES_DIR_.'Analysis/getAnalysisZone.html');
        
        if (isset($_GET['percent']))
        {
            $content .= str_replace('##percent##',$_GET['percent'],str_replace('##surf##',$_GET['surface'],
                            str_replace('##width##',$_GET['width'],str_replace('##height##',$_GET['height'],$file))));
            $content .=  $this->getAnalysisZoneResults();
        }
        else 
            $content .= str_replace('##percent##','10',str_replace('##surf##','20',str_replace('##width##','10',str_replace('##height##','6',$file))));
            
    }
    
    protected function getAnalysisZoneResults()
    {
        $limit = 10;
        
        $sql = "SELECT id_images FROM " . DB_PREFIXE . "images";
        Db::getInstance()->query($sql);
        $totalImages = Db::getInstance()->numRows();
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "images LIMIT ".($_GET['offset']+$limit).",".$limit;
        Db::getInstance()->query($sql);
        $aResultImages = Db::getInstance()->getAll();
        
        
        $sql = "SELECT m.*,p.* FROM " . DB_PREFIXE . "method AS m LEFT JOIN " . DB_PREFIXE . "process AS p ON m.id_method = p.id_method";
        Db::getInstance()->query($sql);
        $aResultsProcess = Db::getInstance()->getAll();
        
        //print_r($aResultsProcess);die();
        
        $tableResults = '<div class="table">';
        
        $tableResults.= '<div class="table-row"><div class="table-cell">Image</div>';
        foreach ($aResultsProcess as $aResultProcess)
        {
            $tableResults.= '<div class="table-cell">'.$aResultProcess["method"]."_".$aResultProcess["version"].'</div>';
        }
        $tableResults.= '</div>'; //row
        foreach ($aResultImages as $aResultImage)
        {
            $tableResults.= '<div class="table-row"><div class="table-cell">'.$aResultImage['filename'].'</div>';
            foreach ($aResultsProcess as $aResultProcess)
            {
                $sql = "SELECT * FROM " . DB_PREFIXE . "results WHERE id_images = ".$aResultImage['id_images']." AND id_process = ".$aResultProcess['id_process'];
                Db::getInstance()->query($sql);
                $aResultResults = Db::getInstance()->getAll();

                if (sizeof($aResultResults) > 0)
                {
                    $tableResults.= '<div class="table-cell">';
                    $sql = "SELECT * FROM " . DB_PREFIXE . "results_details WHERE id_results = ".$aResultResults[0]['id_results'];
                    Db::getInstance()->query($sql);
                    $tableResults.=  Db::getInstance()->numRows();
                    $tableResults.= '<br>';
                    
                    $getAnalysisZoneResultsFunction = "getAnalysisZoneResults_".$aResultProcess["method"]."_".$aResultProcess["version"];
                    $tableResults.= $this->$getAnalysisZoneResultsFunction($aResultResults[0]['id_results']);
                    
                    $tableResults.= '</div>';

                }
                else 
                    $tableResults.= '<div class="table-cell">-</div>';
            }
            $tableResults.= '</div>'; //fin row
        }
        $tableResults .= '</div>'; //fin table
        if ($_GET['offset'] != 0)
        {
            $tableResults .= '<a href="analysis/zone/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                $_GET['width'].'&height='.$_GET['height'].'&offset='.($_GET['offset']-$limit).'">prev</a> ';
        }
        if (($_GET['offset']+$limit)<$totalImages)
        {
            $tableResults .= ' <a href="analysis/zone/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='. 
                $_GET['width'].'&height='.$_GET['height'].'&offset='.($_GET['offset']+$limit).'">next</a>';
        }
        return $tableResults;
        
    }
    
    protected function getAnalysisZoneResults_TensorFlow_model2($id_results)
    {
        $sql = "SELECT * FROM " . DB_PREFIXE . "results_details WHERE id_results = ".$id_results.
                    " AND percentage >".($_GET['percent']/100)." AND ((x_bottom_right-x_top_left)*(y_bottom_right-y_top_left)) <".($_GET['surface']/100);
        Db::getInstance()->query($sql);
        return  Db::getInstance()->numRows();
        
    }
    
    protected function getAnalysisZoneResults_TensorFlow_model2bis($id_results)
    {
        return $this->getAnalysisZoneResults_TensorFlow_model2($id_results);
    }
    
    protected function getAnalysisZoneResults_OCR_hOCR($id_results)
    {
        $sql = "SELECT * FROM " . DB_PREFIXE . "results_details WHERE id_results = ".$id_results.
        " AND (x_bottom_right-x_top_left) <".($_GET['width']/100)." AND (y_bottom_right-y_top_left) <".($_GET['height']/100);
        Db::getInstance()->query($sql);
        return  Db::getInstance()->numRows();
    }
}