<?php

namespace Gti\Cache;

class FileTest extends \PHPUnit_Framework_TestCase
{
    protected $file;
    
    public function setUp()
    {
        $this->file = new File();
    }
    
    public function testFileImplementaInterfaceCache()
    {
        $this->assertInstanceOf('\Gti\Cache\CacheInterface', $this->file);
    }
    
    public function testArquivoFileRecebeConstrutor()
    {
        $path = __DIR__ . '/';
        $file = new File($path);        
        $this->assertEquals($path, $file->getCacheDir());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionPathNaoGravavel()
    {
        $path = '/naoexistepath';
        $this->file->setCacheDir($path);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionLifetimeString()
    {
        $this->file->setLifetime('string');
    }
    
    public function testSetLifetime()
    {
        $lifetime = 86400;
        $this->assertEquals($lifetime, $this->file->setLifetime($lifetime)->getLifetime());
    }
    
    public function testSerializeDataTrue()
    {
        $serializeTrue = $this->file->setAutomaticSerialize(true)->getAutomaticSerialize();
        $this->assertTrue($serializeTrue);
    }
    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testExceptionSetSerializeNaoBool()
    {
        $this->file->setAutomaticSerialize('string');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionSaveSemPath()
    {
        $this->file->save('key', 'Dados');
    }
    
    public function testSaveDados()
    {
        $dados = "Meus dados " . __METHOD__;
        //NECESSARIO TER A PERMISSÃO DE ESCRITA NA PASTA DO TESTE NESTE MOMENTO
        $path = __DIR__ . '/pathTest';
        mkdir($path);        
        $key = 'testando';
        $ret = $this->file->setCacheDir($path)->save($key, $dados);        
        $this->assertTrue($ret);
        if (is_file($path . '/' . md5($key . 'Gti'))) {
            unlink($path . '/' . md5($key . 'Gti'));
        }
        rmdir($path);
    }        
   
    public function testRetornarDados()
    {
        $dados = "Meus Dados " . __METHOD__;
        //NECESSARIO TER A PERMISSÃO DE ESCRITA NA PASTA DO TESTE NESTE MOMENTO
        $path = __DIR__ . '/pathTest';
        mkdir($path);
        $key = 'testando';
        $this->file->setCacheDir($path)->save($key, $dados);
        $ret = $this->file->load($key);
        
        $this->assertEquals($dados, $ret);
        
        if (is_file($path . '/' . md5($key . 'Gti'))) {
            unlink($path . '/' . md5($key . 'Gti'));
        }
        rmdir($path);        
    }
    
    public function testClearFalse()
    {
        $this->assertFalse($this->file->clear('keyInexistente'));
    }
    
    public function testClearTrue()
    {
        $dados = "Meus Dados " . __METHOD__;
        //NECESSARIO TER A PERMISSÃO DE ESCRITA NA PASTA DO TESTE NESTE MOMENTO
        $path = __DIR__ . '/pathTest';
        mkdir($path);
        $key = 'testando';
        $this->file->setCacheDir($path)->save($key, $dados);
        $this->assertTrue($this->file->clear($key));
        if (is_file($path . '/' . md5($key . 'Gti'))) {
            unlink($path . '/' . md5($key . 'Gti'));
        }
        rmdir($path);
    }
    
    
}
