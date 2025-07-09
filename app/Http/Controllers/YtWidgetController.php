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

    // ① grab raw textarea string
    $raw = $request->input('playlist_urls', '');

    // ② split on CR/LF, trim, drop empties
    $urls = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw)));

    // ③ store the clean array (still full URLs, no ID stripping here)
    $widget->playlist_urls = array_values($urls);

    $widget->save();

    return back()->with('status', 'Widget salvato!');
}

}
