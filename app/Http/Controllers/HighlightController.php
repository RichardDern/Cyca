<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHighlightRequest;
use App\Models\Highlight;
use Illuminate\Http\Request;

class HighlightController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHighlightRequest $request)
    {
        $data = $request->validated();

        $highlight = new Highlight();

        $highlight->user_id    = $request->user()->id;
        $highlight->expression = $data['expression'];
        $highlight->color      = $data['color'];

        $highlight->save();

        return $request->user()->highlights()->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\Models\Highlight $highlight
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreHighlightRequest $request, Highlight $highlight)
    {
        if ($highlight->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validated();

        $highlight->expression = $data['expression'];
        $highlight->color      = $data['color'];

        $highlight->save();

        return $request->user()->highlights()->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Models\Hightlight $hightlight
     *
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

    /**
     * Update my groups positions.
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePositions(Request $request)
    {
        if (!$request->has('positions')) {
            abort(422);
        }

        $positions = $request->input('positions');

        if (!is_array($positions)) {
            abort(422);
        }

        $user = $request->user();

        foreach ($positions as $highlightId => $position) {
            if (!is_numeric($highlightId) || !is_numeric($position)) {
                abort(422);
            }

            $highlight = $user->highlights()->findOrFail($highlightId);

            $highlight->position = $positions[$highlightId];

            $highlight->save();
        }
    }
}
