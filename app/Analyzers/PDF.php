<?php

namespace App\Analyzers;

use \Smalot\PdfParser\Parser;
use Storage;

/**
 * Extract information from a PDF file
 */
class PDF extends Analyzer
{
    public function analyze()
    {
        $storageRoot      = $this->document->getStoragePath();
        $metaFilename     = $storageRoot . '/meta.json';
        $parser           = new Parser();
        $pdf              = $parser->parseContent($this->body);
        $details          = $pdf->getDetails();

        Storage::put($metaFilename, json_encode($details));

        if (!empty($details['Title'])) {
            $this->document->title = $details['Title'];
        }
    }
}
