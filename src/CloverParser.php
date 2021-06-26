<?php

namespace WillPower232\CloverParserLaravel;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use RuntimeException;
use WillPower232\CloverParser\CloverParser as BaseCloverParser;

class CloverParser extends BaseCloverParser
{
    private string $projectPath;
    private string $branchPath;

    /**
     * @param File|UploadedFile|string $pathToFile
     */
    public function addFile($pathToFile): BaseCloverParser
    {
        if ($pathToFile instanceof File || $pathToFile instanceof UploadedFile) {
            $pathToFile = $pathToFile->getPathName();
        }

        return parent::addFile($pathToFile);
    }

    private function checkPathsSet(): bool
    {
        return isset($this->projectPath) && isset($this->branchPath);
    }

    public function getSVG(): string
    {
        $percentage = (int) ceil($this->getPercentage());

        if ($percentage > 100) {
            $percentage = 100;
        }

        return View::make('clover-parser::coverage-svg', [
            'percentage' => $percentage,
        ])->render();
    }

    public function setPath(string $projectPath, string $branchPath): self
    {
        $this->projectPath = $projectPath;
        $this->branchPath = $branchPath;

        return $this;
    }

    /**
     * @param File|UploadedFile|string $contents
     */
    public function store(string $name, $contents): string
    {
        if (!$this->checkPathsSet()) {
            throw new RuntimeException('Required path not set');
        }

        $path = $this->projectPath . '/' . $name;

        if ($contents instanceof File || $contents instanceof UploadedFile) {
            Storage::disk(config('clover-parser.disk'))
                ->putFileAs($this->projectPath, $contents, $name, ['mimetype' => 'image/svg+xml']);
        } else {
            Storage::disk(config('clover-parser.disk'))
                ->put($path, $contents, ['mimetype' => 'image/svg+xml']);
        }

        return Storage::disk(config('clover-parser.disk'))
            ->url($path);
    }

    public function storeImage(): string
    {
        if (!$this->checkPathsSet()) {
            throw new RuntimeException('Required path not set');
        }

        $image = $this->getSVG();

        return $this->store($this->branchPath . '.svg', $image);
    }
}
