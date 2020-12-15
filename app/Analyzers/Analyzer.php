<?php

namespace App\Analyzers;

use App\Models\Document;

abstract class Analyzer
{
    /**
     * Document being analyzed.
     *
     * @var \App\Models\Document
     */
    protected $document;

    /**
     * File content.
     *
     * @var string
     */
    protected $body;

    /**
     * Document details (meta data).
     *
     * @var mixed
     */
    protected $details;

    /**
     * Associate Cyca's document being analized.
     *
     * @return self
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Document's body as fetched by Cyca.
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Store details on disk.
     */
    protected function storeDetailsOnDisk()
    {
        if (empty($this->details)) {
            return;
        }

        $storageRoot  = $this->document->getStoragePath();
        $metaFilename = sprintf('%s/meta.json', $storageRoot);

        Storage::put($metaFilename, json_encode($this->details));
    }

    /**
     * Store some details in database. This method uses an array to map document
     * properties to metadata properties.
     *
     * @param array $mappings
     */
    protected function applyDetailsToDocument($mappings)
    {
        foreach ($mappings as $documentKey => $detailsKey) {
            if (!empty($this->details[$detailsKey])) {
                $this->document->{$documentKey} = $this->details[$detailsKey];
            }
        }
    }
}
