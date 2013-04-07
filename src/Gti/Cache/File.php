<?php

namespace Gti\Cache;

class File extends CacheAbstract implements CacheInterface
{
    private $filename;    
    private $cacheDir;
    private $data;

    public function __construct($cacheDir = null) 
    {
        if (!is_null($cacheDir)) {
            $this->setCacheDir($cacheDir);
        }
    }
    
    private function checkDirWrite($dirpath)
    {        
         if (is_null($dirpath)) {
             throw new \InvalidArgumentException("This {$dirpath} is not null");
         }
         if (!is_writable($dirpath)) {
             throw new \InvalidArgumentException("This {$dirpath} is not write");
         }
    }
    
    public function setCacheDir($dir)
    {           
        $this->checkDirWrite($dir);
        $this->cacheDir = rtrim($dir, '/') . '/';            
        return $this;
    }
    
    public function getCacheDir()
    {
        return $this->cacheDir;
    }
    
    public function save($key, $data) 
    {                      
        $this->checkDirWrite($this->cacheDir);
        $this->filename = $this->cacheDir . $this->getFileName($key);
        $dataInternal = serialize(array('expire' => time() + $this->filename, 'data' => $this->createDataSerialize($data)));
        return (bool)file_put_contents($this->filename, $dataInternal, LOCK_EX);        
    }
    
    private function getFileName($key)
    {
        return md5($key . 'Gti');
    }       
    
    public function load($key) 
    {
        if ($this->checkMetadata($key)) {
            return $this->data;
        }
        return null;
    }

    private function checkFileExists($filename, $rename = true)
    {                
        if ($rename) {
            $this->filename = $filename;
        }
        return file_exists($filename) && is_readable($filename);
    }        
    
    private function checkMetadata($key) 
    {
        $filename = $this->cacheDir . $this->getFileName($key);
        if (!$this->checkFileExists($filename)) {
            return false;
        }        
        $metadata = unserialize(file_get_contents($filename));        
        if (isset($metadata['expire']) && $metadata['expire'] >= time()) {
            $this->data = $this->getDataSerialize($metadata['data']);
            return true;
        } else {
            unlink($filename);
        }       
        return false;
    }
    
    public function clear($key)
    {            
        $filename = $this->cacheDir . $this->getFileName($key);
        if ($this->checkFileExists($filename)) {
            unlink($filename);
            return true;
        }
        return false;
    }
        
}
