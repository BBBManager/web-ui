<?php
class IMDT_Util_File{
    /**
     * @param String $path - Caminho completo a ser analisado
     * @return String - String contendo o caminho $path relativo ao APPLICATION_PATH
     */
    public static function relativeToApplicationPath($path){
        $arrPath = explode(DIRECTORY_SEPARATOR, realpath($path));
        $arrAppPath = explode(DIRECTORY_SEPARATOR, realpath(APPLICATION_PATH));
        $relativePath = '';
        $newPath = '';
        $maxI = count($arrAppPath) > count($arrPath) ? count($arrAppPath) : count($arrPath);
        for($i = 0; $i < $maxI; $i++){
            if(isset($arrPath[$i])){
                if(isset($arrAppPath[$i])){
                    if($arrAppPath[$i] != $arrPath[$i]){
                        $relativePath .= DIRECTORY_SEPARATOR.'..';
                        $newPath .= DIRECTORY_SEPARATOR.$arrPath[$i];
                    }
                }else{
                    $newPath .= DIRECTORY_SEPARATOR.$arrPath[$i];
                }
            }else{
                $relativePath .= DIRECTORY_SEPARATOR.'..';
            }
        }
        return $relativePath.$newPath;
    }

    /**
     * @param String $fullPath  - Caminho completo a ser checado a existencia no filesystem
     * @param Boolean $createIfNotExists - Booleano identificando se a estrtura de diretorios deve ser criada, caso nao exista
     * @return Boolean - Booleano identificando a existencia do caminho passado em $fullPath 
     */
    public static function checkPath($fullPath, $createIfNotExists = false) {
        $fullPath = preg_replace('/[\/\\\]/', DIRECTORY_SEPARATOR, $fullPath);		
        $pathExists = file_exists($fullPath);
        
        if(! $pathExists){
            if($createIfNotExists){
                self::_createPath($fullPath);
                $pathExists = file_exists($fullPath);
            }
        }
        
        return $pathExists;
    }

    /**
     * @param String $directoryHierarchy - Cria a estrutura de diretorios contida na String $directoryHierarchy
     */
    static private function _createPath($directoryHierarchy){
        mkdir($directoryHierarchy, 0775, true);
    }
    
    static public function removeDirectory($directoryPath){
        $handle = opendir($directoryPath);
        
        if($handle){
            while (false !== ($file = readdir($handle))){
                if ($file != "." && $file != ".."){  
                    if(is_dir($directoryPath.$file)){
                        if(!@rmdir($directoryPath.$file)){ // Empty directory? Remove it  
                            delete_directory($directoryPath.$file.'/'); // Not empty? Delete the files inside it  
                        }  
                    }else{  
                        @unlink($directoryPath.$file);  
                    }  
                }  
            }
            closedir($handle); 
            @rmdir($directoryPath);  
        }  
    }  
}
?>
