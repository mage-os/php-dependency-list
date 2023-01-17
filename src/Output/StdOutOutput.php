<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList\Output;

use MageOs\PhpDependencyList\Reference;

class StdOutOutput
{
    public function print($filePath, $references = [], $includeSourceFile = false, $includeModuleName = false)
    {
        fwrite(STDOUT, $filePath.PHP_EOL);
        /** @var Reference $reference */
        foreach($references as $reference){
            fwrite(STDOUT, '  ');
            if($reference->hasClass()){
                fwrite(STDOUT, $reference->getClass());
            }else{
                fwrite(STDOUT, 'N/A');
            }
            if($includeSourceFile || $includeModuleName){
                fwrite(STDOUT, ' (');
                if($includeSourceFile){
                    fwrite(STDOUT, 'source: ');
                    if($reference->hasSourceFile()){
                        fwrite(STDOUT, $reference->getSourceFile());
                    }else{
                        fwrite(STDOUT, 'N/A');
                    }
                }
                if($includeModuleName){
                    if($includeSourceFile){
                        fwrite(STDOUT, ', ');
                    }
                    fwrite(STDOUT, 'module: ');
                    if($reference->hasModuleName()){
                        fwrite(STDOUT, $reference->getModuleName());
                    }else{
                        fwrite(STDOUT, 'N/A');
                    }
                }
                fwrite(STDOUT, ')');
            }
            fwrite(STDOUT, PHP_EOL);
        }
        fwrite(STDOUT, PHP_EOL);
    }
}