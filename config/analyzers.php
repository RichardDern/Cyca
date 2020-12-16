<?php

/**
 * This array matches documents MIME type with a class that can analyze it.
 */
return [
    'application/pdf' => \App\Analyzers\PdfAnalyzer::class,
    'text/html'       => \App\Analyzers\HtmlAnalyzer::class,
];
