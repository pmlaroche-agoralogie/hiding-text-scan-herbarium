<?php
class CleanDB {
    /**
     * @var Analysis
     */
    
    
    public function __construct() {
        
        
    }
    
    public function doAction()
    {
        switch($_GET['action']){
            case 'getResultsCleanDB':
                $this->getResultsCleanDB();
                break;
                
        }
        
    }
    
    protected function getResultsCleanDB()
    {
        global $content;
        
        $sql = "SELECT m.*,p.* FROM " . DB_PREFIXE . "method AS m LEFT JOIN " . DB_PREFIXE . "process AS p ON m.id_method = p.id_method";
        Db::getInstance()->query($sql);
        $aResultsProcess = Db::getInstance()->getAll();
        
        $doublon = '';
        foreach ($aResultsProcess as $aResultProcess)
        {
           /* $tableResults.= '<div class="table-cell">'.$aResultProcess["method"]."_".$aResultProcess["version"].'</div>';
            
            $sql = "SELECT id_results FROM " . DB_PREFIXE . "results WHERE id_process=".$aResultProcess['id_process'];
            Db::getInstance()->query($sql);
            $resumeProcessTotal .= $aResultProcess["method"]."_".$aResultProcess["version"].' : '.Db::getInstance()->numRows()."<br>";*/
            
            $sql = "SELECT id_images, id_results, count(id_results) as nb FROM " . DB_PREFIXE . "results WHERE id_process=".$aResultProcess['id_process']." GROUP BY id_images HAVING nb > 1 ";
            Db::getInstance()->query($sql);
            $aResults = Db::getInstance()->getAll();
            $doublon .= $aResultProcess["method"]."_".$aResultProcess["version"]." : ".sizeof($aResults)."<br>\n";
            
            foreach($aResults as $aResult)
            {
                $sql = "DELETE FROM " . DB_PREFIXE . "results_details WHERE id_results = ".$aResult['id_results'];
                Db::getInstance()->query($sql);
                $sql = "DELETE FROM " . DB_PREFIXE . "results WHERE id_results = ".$aResult['id_results'];
                Db::getInstance()->query($sql);
            }
        }
        
        $content .= $doublon;
    }
}