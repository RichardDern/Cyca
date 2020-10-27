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
        $entries = $request->user()->userHistoryEntries()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->simplePaginate(25);

        return $entries;
    }
}
