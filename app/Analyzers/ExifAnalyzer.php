<?php

namespace App\Analyzers;

/**
 * Extract information from a supported image file.
 */
class ExifAnalyzer extends Analyzer
{
    /**
     * Analyzes document.
     */
    public function analyze()
    {
        if (empty($this->body)) {
            return;
        }

        $bodyPath      = storage_path('app/'.$this->document->getStoragePath().'/body');
        $this->details = exif_read_data($bodyPath, null, true, true);

        if (empty($this->details)) {
            return;
        }

        $this->document->description = (string) view('partials.image')->with([
            'exif' => $this->details,
            'url'  => $this->document->url,
        ]);

        $this->storeDetailsOnDisk();
        $this->applyDetailsToDocument();
    }
}
