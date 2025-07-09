<?php

namespace App\Http\Controllers;

use App\Models\YtWidget;
use Illuminate\Http\Request;

class YtWidgetController extends Controller
{
    /* ----------- 1. edit form ----------- */
    public function edit()
    {
        $widget = YtWidget::first() ?? new YtWidget;   // singleton row
        return view('yt.yt-widget-edit', compact('widget'));
    }

    /* ----------- 2. save form ----------- */
public function update(Request $request)
{

    $widget = YtWidget::first() ?? new YtWidget;

    $widget->type     = $request->type;
    $widget->live_url = $request->live_url;

$raw = $request->input('playlist_urls', '');

// turn an array into one big string
if (is_array($raw)) {
    $raw = implode("\n", $raw);
}

$urls = array_filter(array_map('trim', preg_split('/\R/', $raw)));
$widget->playlist_urls = array_values($urls);



    $widget->save();

    return back()->with('status', 'Widget salvato!');
}

}
