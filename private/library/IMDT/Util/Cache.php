<?php
class IMDT_Util_Cache{
    
    public static function getFromCache($cacheDataInstance){
        $frontend= array(
            'lifetime'                  => $cacheDataInstance->getCacheLifeTime(),
            'automatic_serialization'   => true
        );
        
        IMDT_Util_File::checkPath($cacheDataInstance->getCacheDir(), true);

        $backend= array(
            'cache_dir' => $cacheDataInstance->getCacheDir()
        );

        $cache = Zend_Cache::factory(
            'Core',
            'File',
            $frontend,
            $backend
        );
        
        $cacheData = $cache->load($cacheDataInstance->getCacheStorageKey());
        
        if($cacheData == null){
            $cacheData = $cacheDataInstance->generateData();
            $cache->save($cacheData, $cacheDataInstance->getCacheStorageKey());
        }
        
        return $cacheData;
    }
}