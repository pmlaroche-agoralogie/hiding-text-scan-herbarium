<?php
class Tools
{

    /**
     * @brief Generates a Universally Unique IDentifier, version 4.
     *
     * This function generates a truly random UUID. The built in CakePHP String::uuid() function
     * is not cryptographically secure. You should uses this function instead.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.4
     * @see http://en.wikipedia.org/wiki/UUID
     * @return string A UUID, made up of 32 hex digits and 4 hyphens.
     */
    public static function uuidSecure() {

        $pr_bits = null;
        $fp = @fopen('/dev/urandom','rb');
        if ($fp !== false) {
            $pr_bits .= @fread($fp, 16);
            @fclose($fp);
        } else {
            // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
            $pr_bits = "";
            for($cnt=0; $cnt < 16; $cnt++){
                $pr_bits .= chr(mt_rand(0, 255));
            }
        }

        $time_low = bin2hex(substr($pr_bits,0, 4));
        $time_mid = bin2hex(substr($pr_bits,4, 2));
        $time_hi_and_version = bin2hex(substr($pr_bits,6, 2));
        $clock_seq_hi_and_reserved = bin2hex(substr($pr_bits,8, 2));
        $node = bin2hex(substr($pr_bits,10, 6));

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec($time_hi_and_version);
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        return sprintf('%08s-%04s-%04x-%04x-%012s',
            $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
    }

    public static function stopError($code,$message,$description,$log=true)
    {
       // echo _LOG_DIR_;
       echo "ERROR ".$code." : ".$description;
        if ($log)
            self::logError($description);
       die();

    }

    public static function logError($description)
    {


       // echo _LOG_DIR_;
        if (!is_dir(_LOG_DIR_))
            if (!mkdir(_LOG_DIR_))
                die('Echec lors de la création des répertoires...');

        $fh = fopen(_LOG_DIR_.date("Ymd")."_api_error_log", 'a') or die("can't open file");

        if(isset($_SERVER['HTTP_APIKEY']) && $_SERVER['HTTP_APIKEY']!="")
            $apiKey = $_SERVER['HTTP_APIKEY'];

            $line = "[".date(DATE_RFC2822)."]\t".$_SERVER['REMOTE_ADDR']."\t".Dispatcher::getInstance()->getRequestUri()."\t".$description;

        fwrite($fh, $line."\n");

        fclose($fh);
    }
    
    public static function fromPythonArrayToPhpArray($data)
    {
        // Sample python list
        //$data = '[["1","2","3","4"],["11","12","13","14"],["21","22","23","24"]]';
        
        // Removing the outer list brackets
        $data =  substr($data,1,-1);
        
        $myArr = array();
        // Will get a 3 dimensional array, one dimension for each list
        $myArr =explode('],', $data);
        
        // Removing last list bracket for the last dimension
        if(count($myArr)>1)
            $myArr[count($myArr)-1] = substr($myArr[count($myArr)-1],0,-1);
            
        // Removing first last bracket for each dimenion and breaking it down further
        foreach ($myArr as $key => $value) {
            $value = substr($value,1);
            $myArr[$key] = array();
            $myArr[$key] = explode(',',$value);
        }
        
        return $myArr;
            
    }
    
    public static function fromTFArrayToPhpArray($data)
    {
        
        // Removing the outer list brackets
        $data = trim($data);
        $data =  str_replace("\r","",str_replace("\n","",substr($data,1,-1)));
        $data = preg_replace('/\s+/', ' ',$data);
        
        $myArr = array();
        // Will get a 3 dimensional array, one dimension for each list
        $myArr =explode('] ', $data);
        
        // Removing last list bracket for the last dimension
        if(count($myArr)>1)
            $myArr[count($myArr)-1] = substr($myArr[count($myArr)-1],0,-1);
            
            // Removing first last bracket for each dimenion and breaking it down further
            foreach ($myArr as $key => $value) {
                $value = substr($value,1);
                $myArr[$key] = array();
                $myArr[$key] = explode(' ',$value);
            }
            
            return $myArr;
            
    }
    
    public static function LoadJpeg($imgname){
        /* Tente d'ouvrir l'image */
        $im = imagecreatefromjpeg($imgname);
        
        /* Traitement en cas d'échec */
        if(!$im){
           // echo "<br>ouverture de l'image : 0";
            /* Création d'une image vide */
            $im  = imagecreatetruecolor(150, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc  = imagecolorallocate($im, 0, 0, 0);
            
            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
            
            /* On y affiche un message d'erreur */
            imagestring($im, 1, 5, 5, 'Erreur de chargement ' . $imgname, $tc);
        }else{
           // echo "<br>ouverture de l'image : 1";
        }
        
        return $im;
    }
}