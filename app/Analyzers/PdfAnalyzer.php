<?php

namespace App\Analyzers;

use Smalot\PdfParser\Parser;

/**
 * Extract information from a PDF file.
 */
class PdfAnalyzer extends Analyzer
{
    /**
     * PDF parser.
     *
     * @var \Smalot\PdfParser\Parser
     */
    protected $parser;

    /**
     * Analyzes document.
     */
    public function analyze()
    {
        if (empty($this->body)) {
            return;
        }

        $this->extractDetails();
        $this->storeDetailsOnDisk();
        $this->applyDetailsToDocument();
    }

    /**
     * Store some details in database. This method uses an array to map document
     * properties to metadata properties.
     *
     * @param mixed $mappings
     */
    protected function applyDetailsToDocument($mappings = [])
    {
        $mappings = [
            'title' => 'Title',
        ];

        parent::applyDetailsToDocument($mappings);
    }

    /**
     * Return an instance of PDF parser.
     *
     * @return \Smalot\PdfParser\Parser
     */
    private function getParser()
    {
        if (!$this->parser) {
            $this->parser = new Parser();
        }

        return $this->parser;
    }

    /**
     * Parse PDF content.
     *
     * @param mixed $content
     */
    private function parseContent($content)
    {
        $parser = $this->getParser();

        return $parser->parseContent($content);
    }

    /**
     * Return an array of meta data included in PDF.
     */
    private function extractDetails()
    {
        $data          = $this->parseContent($this->body);
        $this->details = $data->getDetails();

        return $this->details;
    }
}
