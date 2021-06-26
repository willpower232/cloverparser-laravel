<?php

namespace WillPower232\CloverParserLaravel\Tests;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use WillPower232\CloverParserLaravel\CloverParser;

class CloverParserTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    private function makeTempFileSafely(): array
    {
        $file = tmpfile();

        if ($file === false) {
            throw new \Exception('Unable to create temporary file');
        }

        $path = stream_get_meta_data($file)['uri'];

        return [
            $file,
            $path,
        ];
    }

    public function testCreatesSVG(): void
    {
        $parser = new CloverParser();

        [$file, $path] = $this->makeTempFileSafely();

        $clover = <<<ENDCLOVER
        <?xml version="1.0" encoding="UTF-8"?>
        <coverage generated="1618905787">
            <project timestamp="1618905787">
                <metrics files="29" loc="1846" ncloc="1233" classes="20" methods="58"
                coveredmethods="50" conditionals="0" coveredconditionals="0" statements="414"
                coveredstatements="316" elements="472" coveredelements="366"/>
            </project>
        </coverage>
        ENDCLOVER;

        fwrite($file, $clover);

        $parser->addFile($path)
            ->calculateTotals();

        $svg = $parser->getSVG();

        $this->assertStringContainsString('>78%<', $svg);
    }

    public function testCreatesSVGNotOver100(): void
    {
        $parser = new CloverParser();

        [$file, $path] = $this->makeTempFileSafely();

        // this clover file implies 200% coverage
        $clover = <<<ENDCLOVER
        <?xml version="1.0" encoding="UTF-8"?>
        <coverage generated="1618905787">
            <project timestamp="1618905787">
                <metrics files="0" loc="0" ncloc="0" classes="0" methods="0"
                coveredmethods="0" conditionals="0" coveredconditionals="0" statements="0"
                coveredstatements="0" elements="450" coveredelements="900"/>
            </project>
        </coverage>
        ENDCLOVER;

        fwrite($file, $clover);

        $parser->addFile($path)
            ->calculateTotals();

        $svg = $parser->getSVG();

        $this->assertStringContainsString('>100%<', $svg);
    }

    public function testCloverByUpload(): void
    {
        $parser = new CloverParser();

        [$file, $path] = $this->makeTempFileSafely();

        $clover = <<<ENDCLOVER
        <?xml version="1.0" encoding="UTF-8"?>
        <coverage generated="1618905787">
            <project timestamp="1618905787">
                <metrics files="29" loc="1846" ncloc="1233" classes="20" methods="58"
                coveredmethods="50" conditionals="0" coveredconditionals="0" statements="414"
                coveredstatements="316" elements="472" coveredelements="366"/>
            </project>
        </coverage>
        ENDCLOVER;

        fwrite($file, $clover);

        $parser->addFile(new File($path))
            ->calculateTotals();

        $svg = $parser->getSVG();

        $this->assertStringContainsString('>78%<', $svg);
    }

    public function testPathsRequiredToStoreFiles(): void
    {
        $parser = new CloverParser();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Required path not set');

        $parser->store('test.txt', 'howdy');
    }

    public function testPathsRequiredToStoreSVGs(): void
    {
        $parser = new CloverParser();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Required path not set');

        $parser->storeImage();
    }

    public function testStoresFileToCustomDisk(): void
    {
        Storage::fake('random');
        config(['clover-parser.disk' => 'random']);

        $parser = new CloverParser();

        $parser->setPath('hello/there', 'main');

        Storage::disk('random')->assertMissing('hello/there/test.txt');

        $parser->store('test.txt', 'howdy');

        Storage::disk('random')->assertExists('hello/there/test.txt');
    }

    public function testStoresLaravelFileToCustomDisk(): void
    {
        Storage::fake('random');
        config(['clover-parser.disk' => 'random']);

        $parser = new CloverParser();

        $parser->setPath('hello/there', 'main');

        Storage::disk('random')->assertMissing('hello/there/test.txt');

        [$file, $path] = $this->makeTempFileSafely();

        fwrite($file, 'howdy');

        $parser->store('test.txt', new File($path));

        Storage::disk('random')->assertExists('hello/there/test.txt');
    }

    public function testStoresUploadedFileToCustomDisk(): void
    {
        Storage::fake('random');
        config(['clover-parser.disk' => 'random']);

        $parser = new CloverParser();

        $parser->setPath('hello/there', 'main');

        Storage::disk('random')->assertMissing('hello/there/test.txt');

        $file = UploadedFile::fake()->create('howdy.txt', 4);

        $parser->store('test.txt', $file);

        Storage::disk('random')->assertExists('hello/there/test.txt');
    }

    public function testStoresImageToLocalDisk(): void
    {
        Storage::fake('local');

        $parser = new CloverParser();

        $parser->setPath('hello/there', 'main');

        Storage::disk('local')->assertMissing('hello/there/main.svg');

        $parser->storeImage();

        Storage::disk('local')->assertExists('hello/there/main.svg');
    }
}
