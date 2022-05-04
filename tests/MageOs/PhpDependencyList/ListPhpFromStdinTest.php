<?php declare(strict_types=1);

namespace MageOs\PhpDependencyList;

use PHPUnit\Framework\TestCase;

class ListPhpFromStdinTest extends TestCase
{
    public function testEmptyStream(): void
    {
        $stream = fopen('php://temp', 'r');
        $sut = new ListPhpFromStdin($stream);

        $this->assertSame([], iterator_to_array($sut->list()));
        
    }
    
    public function testSingleFile(): void
    {
        $phpCode = <<<EOT
<?php class Foo {
    public function bar(Qux \$qux) { }
}
EOT;
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $phpCode);
        fseek($stream, 0, SEEK_SET);

        
        $sut = new ListPhpFromStdin($stream);
        $this->assertSame([$phpCode], iterator_to_array($sut->list()));
    }
    
    public function testTwoFiles(): void
    {
        $phpCode1 = <<<EOT
<?php class Foo {
    public function bar(Qux \$qux) {}
}
EOT;
        $phpCode2 = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
}
EOT;
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $phpCode1 . "\0" . $phpCode2);
        fseek($stream, 0, SEEK_SET);
        
        $sut = new ListPhpFromStdin($stream);
        
        $this->assertSame([$phpCode1, $phpCode2], iterator_to_array($sut->list()));
    }
    
    public function testEmptyThenNonEmptyFile(): void
    {
        $phpCode = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
}
EOT;
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, "\0" . $phpCode);
        fseek($stream, 0, SEEK_SET);
        
        $sut = new ListPhpFromStdin($stream);
        
        $this->assertSame([$phpCode], iterator_to_array($sut->list()));
    }
    
    public function testNonEmptyThenEmptyFile(): void
    {
        $phpCode = <<<EOT
<?php class Qux {
    public function moo(Buz \$buz) {}
}
EOT;
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $phpCode . "\0");
        fseek($stream, 0, SEEK_SET);
        
        $sut = new ListPhpFromStdin($stream);
        
        $this->assertSame([$phpCode], iterator_to_array($sut->list()));
    }

    public function testThreeEmptyFiles(): void
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, "\0\0");
        fseek($stream, 0, SEEK_SET);

        $sut = new ListPhpFromStdin($stream);

        $this->assertSame([], iterator_to_array($sut->list()));
    }
}