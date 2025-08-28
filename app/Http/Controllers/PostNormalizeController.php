<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

class PostNormalizeController extends Controller
{
    public function normalize(Request $request)
    {
        $fromId = (int)$request->input('from', 0);
        $toId   = (int)$request->input('to', 0);
        $chunk  = (int)$request->input('chunk', 100);
        $dry    = (bool)$request->boolean('dry', false);

        $q = DB::table('posts')->select('id','content')->where('id','>', $fromId);
        if ($toId > 0) $q->where('id','<=',$toId);

        $results = [];
        $q->orderBy('id')->chunk($chunk, function ($rows) use (&$results, $dry) {
            foreach ($rows as $row) {
                $normalized = $this->normalizeHtml((string)$row->content);

                if ($dry) {
                    $results[] = [
                        'id'      => $row->id,
                        'preview' => Str::limit(strip_tags($normalized), 120),
                    ];
                } else {
                    DB::table('posts')->where('id',$row->id)->update([
                        'content'    => $normalized,
                        'updated_at' => now(),
                    ]);
                    $results[] = ['id'=>$row->id, 'status'=>'updated'];
                }
            }
        });

        return response()->json([
            'from'    => $fromId,
            'to'      => $toId,
            'dry'     => $dry,
            'results' => $results,
        ]);
    }

    protected function normalizeHtml(string $html): string
    {
        if (trim($html) === '') return $html;

        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0','UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);
        $root  = $dom->getElementById('root');

        // remove dangerous/unused
        foreach (['//noscript','//script','//style'] as $q) {
            foreach ($xpath->query($q) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        // fix lazy images
        foreach ($xpath->query('.//img', $root) as $img) {
            /** @var \DOMElement $img */
            if ($img->hasAttribute('data-lazy-src') && !$img->hasAttribute('src')) {
                $img->setAttribute('src', $img->getAttribute('data-lazy-src'));
            }
            if ($img->hasAttribute('data-lazy-srcset') && !$img->hasAttribute('srcset')) {
                $img->setAttribute('srcset', $img->getAttribute('data-lazy-srcset'));
            }
            $img->removeAttribute('data-lazy-src');
            $img->removeAttribute('data-lazy-srcset');
        }

        // ad after first <h3>
        $h3 = $xpath->query('.//h3', $root)->item(0);
        if ($h3) {
            $slot = $dom->createElement('div');
            $slot->setAttribute('class', 'ad-slot ad-slot--after-h3');
            $slot->setAttribute('data-ad', 'in-article-1');
            $h3->parentNode->insertBefore($slot, $h3->nextSibling);
        }

        // ad after second <p>
        $ps = $xpath->query('.//p', $root);
        if ($ps->length >= 2) {
            $p2 = $ps->item(1);
            $slot2 = $dom->createElement('div');
            $slot2->setAttribute('class', 'ad-slot ad-slot--after-p2');
            $slot2->setAttribute('data-ad', 'in-article-2');
            $p2->parentNode->insertBefore($slot2, $p2->nextSibling);
        }

        // innerHTML of #root
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        libxml_clear_errors();
        return $out;
    }
}
