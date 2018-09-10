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
            case 'getAnalysisPBZone':
                $this->getAnalysisPBZone();
                break;
                
        }
        
    }
    
    protected function getAnalysisZone()
    {
        global $content;
         
        $file = file_get_contents(_TEMPLATES_DIR_.'Analysis/getAnalysisZone.html');
        
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
        
        $resumeProcessTotal = "<br>";
        $tableResults = '<div class="table">';
        
        $tableResults.= '<div class="table-row"><div class="table-cell">Image</div>';
        foreach ($aResultsProcess as $aResultProcess)
        {
            $tableResults.= '<div class="table-cell">'.$aResultProcess["method"]."_".$aResultProcess["version"].'</div>';
            
            $sql = "SELECT id_results FROM " . DB_PREFIXE . "results WHERE id_process=".$aResultProcess['id_process'];
            Db::getInstance()->query($sql);
            $resumeProcessTotal .= $aResultProcess["method"]."_".$aResultProcess["version"].' : '.Db::getInstance()->numRows()."<br>";
        }
        $tableResults.= '</div>'; //row
        foreach ($aResultImages as $aResultImage)
        {
            $tableResults.= '<div class="table-row"><div class="table-cell">
                                <a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                                $_GET['width'].'&height='.$_GET['height'].'&filename='.$aResultImage['filename'].'&big=0" 
                                target="_blank">'.$aResultImage['filename'].'</a></div>';
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
        
        $navLinks = "";
        if ($_GET['offset'] != 0)
        {
            $navLinks .= '<a href="analysis/zone/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                $_GET['width'].'&height='.$_GET['height'].'&offset='.($_GET['offset']-$limit).'" class="navButton prev">prev</a> ';
        }
        if (($_GET['offset']+$limit)<$totalImages)
        {
            $navLinks .= ' <a href="analysis/zone/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='. 
                $_GET['width'].'&height='.$_GET['height'].'&offset='.($_GET['offset']+$limit).'" class="navButton next">next</a>';
        }
        
        $navLinks = '<div class="nav">'.$navLinks.'<div class="clearBoth"></div></div>';
        return $resumeProcessTotal.$navLinks.$tableResults.$navLinks;
        
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
    
    protected function getAnalysisZoneResults_TensorFlow_model1($id_results)
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
    
    protected function getAnalysisZoneDisplay()
    {
        global $content;
        $contentZone = '';
        $selecteur = '';
        $navImages = '';
        
        $color = array('blue','green','red','orange','pink');
        
        if (isset($_GET['filename']))
        {
            $filename = $_GET['filename'];
            $secureFilename = Db::getInstance()->quote($filename);
        }
        else 
        {
            //todo selectionner premiere image
        }
        
        if (isset($_GET['big']) && (int)$_GET['big'])
        {
            $pathImgOrigin = _IMAGES_BIG_ORIGIN_DIR_;
            $pathRelImgOrigin = _REL_IMAGES_BIG_ORIGIN_DIR_;
        }
        else
        {
            $pathImgOrigin = _IMAGES_ORIGIN_DIR_;
            $pathRelImgOrigin = _REL_IMAGES_ORIGIN_DIR_;
        }
        
        $pathImage = $pathImgOrigin.$filename;
        
        if (file_exists($pathImage))
        {
            $infoImg = getimagesize($pathImage);
            
            $selecteur .= '<a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                $_GET['width'].'&height='.$_GET['height'].'&filename='.$_GET['filename'].'&big=0" 
                                style="color:black">All processes</a><br>';
            
            
        
            $contentZone .= '<div style="position:relative">';
            $contentZone .= '<img src="'.$pathRelImgOrigin.$filename.'"/>';
            $contentZone .= '<canvas id="myCanvas" width="'.$infoImg[0].'" height="'.$infoImg[1].'" style="position:absolute;top:0;left:0">
            Your browser does not support the HTML5 canvas tag.</canvas>';
            $contentZone .= '</div>';
            
            $contentZone .= '<script>
window.onload = function() {
var c=document.getElementById("myCanvas");
var ctx=c.getContext("2d");
/*
// Red rectangle
ctx.beginPath();
ctx.lineWidth="6";
ctx.strokeStyle="red";
ctx.rect(5,5,290,140); 
ctx.stroke();

// Green rectangle
ctx.beginPath();
ctx.lineWidth="4";
ctx.strokeStyle="green";
ctx.rect(30,30,50,50);
ctx.stroke();

// Blue rectangle
ctx.beginPath();
ctx.lineWidth="10";
ctx.strokeStyle="blue";
ctx.rect(50,50,150,80);
ctx.stroke();}*/';
            
            $sql = "SELECT * FROM " . DB_PREFIXE . "results AS r
                        LEFT JOIN " . DB_PREFIXE . "images AS i ON i.id_images = r.id_images
                        LEFT JOIN " . DB_PREFIXE . "process AS p ON r.id_process = p.id_process
                        LEFT JOIN   " . DB_PREFIXE . "method AS m ON m.id_method = p.id_method
                        WHERE i.filename = ".$secureFilename;
            Db::getInstance()->query($sql);
            $aResultsProcess = Db::getInstance()->getAll();
        
            $sql = "SELECT filename FROM " . DB_PREFIXE . "images WHERE id_images < ".$aResultsProcess[0]['id_images']." ORDER BY id_images DESC LIMIT 0,1";
            Db::getInstance()->query($sql);
            $aResultsPrevious = Db::getInstance()->getAll();
            if (sizeof($aResultsPrevious)>0)
            {
                $navImages .= '<a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                                $_GET['width'].'&height='.$_GET['height'].'&filename='.$aResultsPrevious[0]['filename'].'&big=0"
                                 class="navButton prev">Prev.</a> ';
            }
            $sql = "SELECT filename FROM " . DB_PREFIXE . "images WHERE id_images > ".$aResultsProcess[0]['id_images']." ORDER BY id_images ASC LIMIT 0,1";
            Db::getInstance()->query($sql);
            $aResultsNext = Db::getInstance()->getAll();
            if (sizeof($aResultsNext)>0)
            {
                $navImages .= '<a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                    $_GET['width'].'&height='.$_GET['height'].'&filename='.$aResultsNext[0]['filename'].'&big=0"
                                 class="navButton next">Next.</a> ';
            }
            
            foreach($aResultsProcess as $key => $aResultProcess)
            {
                $selecteur .= '<a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                    $_GET['width'].'&height='.$_GET['height'].'&filename='.$_GET['filename'].'&big=0&process='.$aResultProcess['id_process'].'"
                                style="color:'.$color[$key].'">'.$aResultProcess["method"].' '.$aResultProcess["version"].'</a>
                    <a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                    $_GET['width'].'&height='.$_GET['height'].'&filename='.$_GET['filename'].'&big=0&process='.$aResultProcess['id_process'].'&white=1"
                                style="color:'.$color[$key].'">(blanc)</a>
                    <a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                    $_GET['width'].'&height='.$_GET['height'].'&filename='.$_GET['filename'].'&big=0&process='.$aResultProcess['id_process'].'&big=1"
                                style="color:'.$color[$key].'">(big)</a>
                    <a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                    $_GET['width'].'&height='.$_GET['height'].'&filename='.$_GET['filename'].'&big=0&process='.$aResultProcess['id_process'].'&white=1&big=1"
                                style="color:'.$color[$key].'">(blanc big)</a>
                    <br>';
                if (!isset($_GET['process']) || $_GET['process']==$aResultProcess['id_process'])
                {
                    $getAnalysisZoneDisplayFunction = "getAnalysisZoneDisplay_".$aResultProcess["method"]."_".$aResultProcess["version"];
                    
                    $contentZone .=  $this->$getAnalysisZoneDisplayFunction($aResultProcess['id_results'],$color[$key],$infoImg);
                }
                
               
            }
            $contentZone .= '
}</script>
            ';
        }
        else 
        {
            $contentZone .= 'Image non trouvée.';
        }
        
        $navImages = '<div class="nav navImages">'.$navImages.'<div class="clearBoth"></div></div>';
        
        $content = $navImages.$selecteur.$contentZone.$navImages;

        
    }
    
    protected function getAnalysisZoneDisplay_TensorFlow_model2($id_results,$color,$infoImg)
    {
        $limitPercent = $_GET['percent']/100;
        $limitSurface = $_GET['surface']/100;
        $i=1;
        $script = '';
        
        $sql = "SELECT * FROM " . DB_PREFIXE . "results_details
                            WHERE id_results = ".$id_results."
                                AND percentage > ".$limitPercent;
        Db::getInstance()->query($sql);
        $aResultsDetails = Db::getInstance()->getAll();
        foreach ($aResultsDetails as $aResultsDetail)
        {
            $boxWidth = ($aResultsDetail['x_bottom_right'] - $aResultsDetail['x_top_left']) ;
            $boxHeight = ($aResultsDetail['y_bottom_right'] - $aResultsDetail['y_top_left']);
            
            $surface = ($aResultsDetail['x_bottom_right'] - $aResultsDetail['x_top_left']) * ($aResultsDetail['y_bottom_right'] - $aResultsDetail['y_top_left']);
            if ($surface < $limitSurface)
            {
                $xgh_pixel = $aResultsDetail['x_top_left'] *  $infoImg[0];
                $yhg_pixel = $aResultsDetail['y_top_left'] *  $infoImg[1];
                $width_pixel = $boxWidth *  $infoImg[0];
                $height_pixel = $boxHeight *  $infoImg[1];
                
                if (isset($_GET['white']) && (int)$_GET['white'])
                {
                    $script .= 'ctx.fillStyle="white";
                                ctx.fillRect('.$xgh_pixel.','.$yhg_pixel.','.$width_pixel.','.$height_pixel.');';
                }
                else
                {
                    $script .= 'ctx.lineWidth="2";
                                ctx.strokeStyle="'.$color.'";
                                ctx.strokeRect('.$xgh_pixel.','.$yhg_pixel.','.$width_pixel.','.$height_pixel.');
                                ctx.textBaseline = "top";
                                ctx.fillStyle = "'.$color.'";
                                ctx.font = "20pt sans-serif";
                                ctx.fillText("'.$i.'", '.($xgh_pixel+5).', '.($yhg_pixel).');';
                }
                $i++;
                
                
            }
        }
        return $script;
    }
    
    protected function getAnalysisZoneDisplay_TensorFlow_model2bis($id_results,$color,$infoImg)
    {
        return $this->getAnalysisZoneDisplay_TensorFlow_model2($id_results,$color,$infoImg);
    }
    
    protected function getAnalysisZoneDisplay_TensorFlow_model1($id_results,$color,$infoImg)
    {
        return $this->getAnalysisZoneDisplay_TensorFlow_model2($id_results,$color,$infoImg);
    }
    
    protected function getAnalysisZoneDisplay_OCR_hOCR($id_results,$color,$infoImg)
    {
        $limitWidth = $_GET['width']/100;
        $limitHeight = $_GET['height']/100;
        $i=1;
        
        $script = '';
        $sql = "SELECT * FROM " . DB_PREFIXE . "results_details
                            WHERE id_results = ".$id_results;
        //AND percentage > ".$limitPercent;
        Db::getInstance()->query($sql);
        $aResultsDetails = Db::getInstance()->getAll();
        foreach ($aResultsDetails as $aResultsDetail)
        {
            
            $boxWidth = ($aResultsDetail['x_bottom_right'] - $aResultsDetail['x_top_left']) ;
            $boxHeight = ($aResultsDetail['y_bottom_right'] - $aResultsDetail['y_top_left']);
            
            if (($boxWidth < $limitWidth) && ($boxHeight < $limitHeight))
            {
                $xgh_pixel = $aResultsDetail['x_top_left'] *  $infoImg[0];
                $yhg_pixel = $aResultsDetail['y_top_left'] *  $infoImg[1];
                $width_pixel = $boxWidth *  $infoImg[0];
                $height_pixel = $boxHeight *  $infoImg[1];
               
                
                if (isset($_GET['white']) && (int)$_GET['white'])
                {
                    $script .= 'ctx.fillStyle="white";
                                ctx.fillRect('.$xgh_pixel.','.$yhg_pixel.','.$width_pixel.','.$height_pixel.');';
                }
                else
                {
                    $script .= 'ctx.lineWidth="2";
                                ctx.strokeStyle="'.$color.'";
                                ctx.strokeRect('.$xgh_pixel.','.$yhg_pixel.','.$width_pixel.','.$height_pixel.');
                                ctx.textBaseline = "top";
                                ctx.fillStyle = "'.$color.'";
                                ctx.font = "20pt sans-serif";
                                ctx.fillText("'.$i.'", '.($xgh_pixel+5).', '.($yhg_pixel).');';
                }
                $i++;
            }
        }
        return $script;
        
    }
    
    protected function getAnalysisPBZone()
    {
        global $content;
        
        $file = file_get_contents(_TEMPLATES_DIR_.'Analysis/getAnalysisPBZone.html');
        
        // $content .= file_get_contents(_TEMPLATES_DIR_.'Analysis/getAnalysisZone.html');
        
        if (isset($_GET['percent']))
        {
            $content .= str_replace('##percent##',$_GET['percent'],str_replace('##surf##',$_GET['surface'],
                str_replace('##width##',$_GET['width'],str_replace('##height##',$_GET['height'],$file))));
            $content .=  $this->getAnalysisPBZoneResults();
        }
        else
            $content .= str_replace('##percent##','10',str_replace('##surf##','20',str_replace('##width##','10',str_replace('##height##','6',$file))));
    }
    
    protected function getAnalysisPBZoneResults()
    {
        
        $aPbZoneImages= array();
        $aNbProcessTotal = array();
        $sql = "SELECT m.*,p.* FROM " . DB_PREFIXE . "method AS m LEFT JOIN " . DB_PREFIXE . "process AS p ON m.id_method = p.id_method";
        Db::getInstance()->query($sql);
        $aResultsProcess = Db::getInstance()->getAll();
        foreach ($aResultsProcess as $aResultProcess)
        {
            $getAnalysisPBZoneResultsFunction = "getAnalysisPBZoneResults_".$aResultProcess["method"]."_".$aResultProcess["version"];
            
            $this->$getAnalysisPBZoneResultsFunction($aResultProcess['id_process'],$aPbZoneImages,$aNbProcess);
            
            $sql = "SELECT id_results FROM " . DB_PREFIXE . "results WHERE id_process=".$aResultProcess['id_process'];
            Db::getInstance()->query($sql);
            $aNbProcessTotal[$aResultProcess['id_process']]=Db::getInstance()->numRows();
            
            
        }
        

        $resume = '<br>';
        $tableResults = '<div class="table">';
        
        $tableResults.= '<div class="table-row"><div class="table-cell">Image</div>';
        foreach ($aResultsProcess as $aResultProcess)
        {
            $tableResults.= '<div class="table-cell">'.$aResultProcess["method"]."_".$aResultProcess["version"].'</div>';
            $resume .= $aResultProcess["method"].' '.$aResultProcess["version"].' : '.$aNbProcess[$aResultProcess['id_process']].'('.$aNbProcessTotal[$aResultProcess['id_process']].')<br>';
        }
        $tableResults.= '</div>'; //row
        foreach ($aPbZoneImages as $filename => $pbZoneImages)
        {
            $tableResults.= '<div class="table-row"><div class="table-cell">
                                <a href="analysis/zone/display/get/?percent='.$_GET['percent'].'&surface='.$_GET['surface'].'&width='.
                                $_GET['width'].'&height='.$_GET['height'].'&filename='.$filename.'&big=0"
                                target="_blank">'.$filename.'</a></div>';
                                foreach ($aResultsProcess as $aResultProcess)
                                {
                                    if (isset($pbZoneImages[$aResultProcess['id_process']]))
                                    {
                                        $tableResults.= '<div class="table-cell">';
                                        $tableResults.= $pbZoneImages[$aResultProcess['id_process']];
                                        $tableResults.= '</div>';
                                    }
                                    else
                                        $tableResults.= '<div class="table-cell">-</div>';
                                   
                                }
                                $tableResults.= '</div>'; //fin row
        }
        $tableResults .= '</div>'; //fin table
        return $resume.$tableResults;
       
    }
    
    protected function getAnalysisPBZoneResults_TensorFlow_model2($id_process,&$aPbZoneImages,&$aNbProcess)
    {
        $minZone = 1;
        $maxZone = 10;
        
        
        $subSql = "SELECT rd.id_results
                        FROM " . DB_PREFIXE . "results_details AS rd
                        LEFT JOIN " . DB_PREFIXE . "results AS r ON r.id_results = rd.id_results
                        WHERE r.id_process = ".$id_process;
        
        $subSql2 = "SELECT rd.id_results, count(*) as nb_zone 
                        FROM results_details AS rd
                        WHERE rd.percentage >".($_GET['percent']/100)."
                            AND ((rd.x_bottom_right-rd.x_top_left)*(rd.y_bottom_right-rd.y_top_left)) <".($_GET['surface']/100)."
                            AND rd.id_results IN (".$subSql.") 
                        GROUP BY rd.id_results";
        
        $sql = "SELECT i.filename, IFNULL(rd.nb_zone,0) as nb_zone
                        FROM " . DB_PREFIXE . "images AS i
                        LEFT JOIN " . DB_PREFIXE . "results AS r ON i.id_images = r.id_images
                        LEFT OUTER JOIN (".$subSql2.") AS rd ON r.id_results = rd.id_results
                        WHERE r.id_results IN (".$subSql.")
                        HAVING nb_zone < ".$minZone." OR nb_zone > ".$maxZone;
        
        Db::getInstance()->query($sql);
        $aResultsPbZone = Db::getInstance()->getAll();
        if (sizeof($aResultsPbZone) > 0)
        {
            $aNbProcess[$id_process] = sizeof($aResultsPbZone);
            foreach($aResultsPbZone as $aResultPbZone)
            {
                
                $aPbZoneImages[$aResultPbZone['filename']][$id_process] = $aResultPbZone['nb_zone'];
            }
        }
    }
    
    protected function getAnalysisPBZoneResults_TensorFlow_model2bis($id_process,&$aPbZoneImages,&$aNbProcess)
    {
        $this->getAnalysisPBZoneResults_TensorFlow_model2($id_process,$aPbZoneImages,$aNbProcess);
    }
    
    protected function getAnalysisPBZoneResults_TensorFlow_model1($id_process,&$aPbZoneImages,&$aNbProcess)
    {
        $this->getAnalysisPBZoneResults_TensorFlow_model2($id_process,$aPbZoneImages,$aNbProcess);
    }
    
    protected function getAnalysisPBZoneResults_OCR_hOCR($id_process,&$aPbZoneImages,&$aNbProcess)
    {
        $aNbProcess[$id_process] = 0;
    }
}