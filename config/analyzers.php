<?php

/**
 * This array matches documents MIME type with a class that can analyze it.
 */
return [
    'application/pdf' => \App\Analyzers\PdfAnalyzer::class,
    'image/bmp'       => \App\Analyzers\ExifAnalyzer::class,
    'image/gif'       => \App\Analyzers\ExifAnalyzer::class,
    'image/ico'       => \App\Analyzers\ExifAnalyzer::class,
    'image/iff'       => \App\Analyzers\ExifAnalyzer::class,
    'image/jb2'       => \App\Analyzers\ExifAnalyzer::class,
    'image/jp2'       => \App\Analyzers\ExifAnalyzer::class,
    'image/jpc'       => \App\Analyzers\ExifAnalyzer::class,
    'image/jpeg'      => \App\Analyzers\ExifAnalyzer::class,
    'image/jpx'       => \App\Analyzers\ExifAnalyzer::class,
    'image/png'       => \App\Analyzers\ExifAnalyzer::class,
    'image/psd'       => \App\Analyzers\ExifAnalyzer::class,
    'image/swc'       => \App\Analyzers\ExifAnalyzer::class,
    'image/swf'       => \App\Analyzers\ExifAnalyzer::class,
    'image/tiff'      => \App\Analyzers\ExifAnalyzer::class,
    'image/wbmp'      => \App\Analyzers\ExifAnalyzer::class,
    'image/webp'      => \App\Analyzers\ExifAnalyzer::class,
    'image/xbm'       => \App\Analyzers\ExifAnalyzer::class,
    'text/html'       => \App\Analyzers\HtmlAnalyzer::class,
];
