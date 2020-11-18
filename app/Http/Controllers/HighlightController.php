<?php

namespace App\Http\Controllers;

use App\Models\Highlight;
use App\Http\Requests\StoreHighlightRequest;
use Illuminate\Http\Request;

class HighlightController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHighlightRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHighlightRequest $request)
    {
        $data = $request->validated();

        $highlight = new Highlight();
        $highlight->user_id = $request->user()->id;
        $highlight->expression = $data['expression'];
        $highlight->color = $data['color'];
        $highlight->save();

        return $request->user()->highlights()->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreHighlightRequest  $request
     * @param  \App\Models\Models\Highlight  $highlight
     * @return \Illuminate\Http\Response
     */
    public function update(StoreHighlightRequest $request, Highlight $highlight)
    {
        if ($highlight->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validated();

        $highlight->expression = $data['expression'];
        $highlight->color = $data['color'];
        $highlight->save();

        return $request->user()->highlights()->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\Hightlight  $hightlight
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Highlight $highlight)
    {
        if ($highlight->user_id !== $request->user()->id) {
            abort(404);
        }

        $highlight->delete();
        return $request->user()->highlights()->get();
    }
}
