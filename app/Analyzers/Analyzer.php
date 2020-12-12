<?php

namespace App\Analyzers;

use App\Models\Document;

abstract class Analyzer
{
    /**
     * Document being analyzed
     *
     * @var \App\Models\Document
     */
    protected $document = null;

    /**
     * File content
     * @var string
     */
    protected $body = null;
    
    /**
     * Associate Cyca's document being analized
     *
     * @param \App\Models\Document $document
     * @return self
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Document's body as fetched by Cyca
     *
     * @param string $body
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }
}
