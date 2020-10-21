<?php

namespace App\Http\Controllers;

use App\Models\HistoryEntry;
use Illuminate\Http\Request;

class HistoryEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $entries = $request->user()->historyEntries()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->simplePaginate(50);

        return $entries;
    }
}
