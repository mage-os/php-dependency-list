<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use MageOs\PhpDependencyList\Reference;

class SourceResolver
{
    protected $classToFilePathMap = [];

    protected $filePathToModuleMap = [];

    protected $loader;

    protected $reflectedExtensions = [];

    public function __construct()
    {
        if(is_file('./vendor/autoload.php')){
            $this->loader = require './vendor/autoload.php';
        }else{
            fwrite(STDERR, './vendor/autoload.php is not found, unable to resolve classes to file. (please run composer install and run again)'.PHP_EOL);
        }

        foreach(get_loaded_extensions() as $extension){
            $this->reflectedExtensions[$extension] = new \ReflectionExtension($extension);
        }
    }
    /**
     * @param Reference $reference
     */
    public function resolve(&$reference)
    {
        if($this->loader){
            if($reference->hasClass()){
                $reference->setSourceFile($this->getSourceFile($reference->getClass()));
            }
        }
        $reference->setModuleName($this->getModuleName($reference));
    }

    protected function getSourceFile($class)
    {
        $class = ltrim($class, '\\');
        if(!array_key_exists($class, $this->classToFilePathMap)){
            $path = $this->loader->findFile($class);
            if($path){
                $path = str_replace(getcwd().'/', '', realpath($path));
            }
            $this->classToFilePathMap[$class] = $path;
        }
        return $this->classToFilePathMap[$class];
    }

    /**
     * @var Reference $reference
     */
    protected function getModuleName($reference)
    {
        if($reference->getSourceFile() === false){
            if($reference->hasClass()){
                if(!array_key_exists($reference->getClass(), $this->filePathToModuleMap)){
                    $module = false;
                    $class = ltrim($reference->getClass(), '\\');
                    foreach($this->reflectedExtensions as $reflectedExtension){
                        if(in_array($class, $reflectedExtension->getClassNames())){
                            $module = 'ext-'.$reflectedExtension->getName();
                            break;
                        }
                    }
                    $this->filePathToModuleMap[$reference->getClass()] = $module;
                }
                return $this->filePathToModuleMap[$reference->getClass()];
            }
        }else{
            if(!array_key_exists($reference->getSourceFile(), $this->filePathToModuleMap)){
                $composerJson = $this->findComposerJson($reference->getSourceFile());
                if($composerJson){
                    $composerJson = json_decode(file_get_contents($composerJson), true);
                    $this->filePathToModuleMap[$reference->getSourceFile()] = $composerJson['name']; 
                }else{
                    $this->filePathToModuleMap[$reference->getSourceFile()] = false; 
                }
            }
            return $this->filePathToModuleMap[$reference->getSourceFile()];
        }
        return null;
    }

    protected function findComposerJson($path){
        if(!$path){
            return false;
        }
        $dir = dirname($path);    
        if(is_file($dir.'/composer.json')){
            return $dir.'/composer.json';
        }
        return $this->findComposerJson($dir);
    }
}