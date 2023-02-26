<?php

declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use JsonSerializable;

class Reference implements JsonSerializable
{
    protected $class;

    protected $moduleName;

    protected $sourceFile;

    public function __construct($class = null, $moduleName = null, $sourceFile = null)
    {
        $this->class = $class;
        $this->moduleName = $moduleName;
        $this->sourceFile = $sourceFile;
    }

    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function hasClass()
    {
        return $this->class != null;
    }

    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function hasModuleName()
    {
        return $this->moduleName != null;
    }

    public function setSourceFile($sourceFile)
    {
        $this->sourceFile = $sourceFile;
        return $this;
    }

    public function getSourceFile()
    {
        return $this->sourceFile;
    }

    public function hasSourceFile()
    {
        return $this->sourceFile != null;
    }

    public function toArray()
    {
        return [
            'class' => $this->hasClass() ? $this->getClass() : null,
            'module' => $this->hasModuleName() ? $this->getModuleName() : null,
            'source' => $this->hasSourceFile() ? $this->getSourceFile() : null,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
