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
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "method";
        Db::getInstance()->query($sql);
        $aResultMethod = Db::getInstance()->getAll();
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "process";
        Db::getInstance()->query($sql);
        $aResultProcess = Db::getInstance()->getAll();
        
        $optionsMethod = '';
        $optionsProcess = '';
        
        //https://nosmoking.developpez.com/tutoriels/javascript/listes-liees-entre-elles/
        
        foreach($aResultMethod as $resultMethod)
        {
            $optionsMethod .= '<option value="'.$resultMethod['id_method'].'">'.$resultMethod['method'].'</option>';
        }
        if ($optionsMethod != '')
        {
            $content .= '<select id="method">'.$optionsMethod.'</select>';
        }
        
        foreach($aResultProcess as $resultProcess)
        {
            $optionsProcess .= '<option value="'.$resultProcess['id_process'].'">'.$resultProcess['version'].'</option>';
        }
        if ($optionsProcess != '')
        {
            $content .= '<select id="process">'.$optionsProcess.'</select>';
        }
        
        $content .="menu";
    }
}