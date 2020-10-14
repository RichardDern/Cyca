<?php

namespace App\Contracts;

use Illuminate\Http\Request;

/**
 * Interface for data importers
 */
interface ImportAdapter {
    /**
     * Transforms data from specified request into an importable array. Data
     * collected from the request could be an uploaded file, credentials for
     * remote connection, anything the adapter could support.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function importFromRequest(Request $request): array;
}
