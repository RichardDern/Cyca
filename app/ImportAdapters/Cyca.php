<?php

namespace App\ImportAdapters;

use App\Contracts\ImportAdapter;
use Illuminate\Http\Request;

class Cyca implements ImportAdapter
{
    /**
     * Transforms data from specified request into an importable array. Data
     * collected from the request could be an uploaded file, credentials for
     * remote connection, anything the adapter could support.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function importFromRequest(Request $request): array
    {
        return json_decode(file_get_contents($request->file('file')), true);
    }
}
