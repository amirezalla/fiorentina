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
        $dry    = (bool)$request->input('dry', false);

        config(['purifier.settings.laviola' => [
            'HTML.Doctype' => 'HTML5',
            'HTML.Allowed' => 'h2,h3,h4,h5,h6,p,em,strong,ul,ol,li,blockquote,a[href|title|target|rel],img[alt|src|srcset|sizes|width|height|data-lazy-src|data-lazy-srcset],span[class|style],div[class|id|data-*],figure,figcaption,br,hr',
            'CSS.AllowedProperties' => 'text-decoration,font-weight,font-style',
            'Attr.AllowedFrameTargets' => ['_blank'],
            'AutoFormat.RemoveEmpty' => false,
            'URI.AllowedSchemes' => ['http' => true, 'https' => true, 'data' => true],
        ]]);

        $q = DB::table('posts')->select('id','content')->where('id','>', $fromId);
        if ($toId > 0) {
            $q->where('id','<=',$toId);
        }

        $updated = [];
        $q->orderBy('id')->chunk($chunk, function ($rows) use (&$updated, $dry) {
            foreach ($rows as $row) {
                $normalized = $this->normalizeHtml($row->content);

                $clean = \Mews\Purifier\Facades\Purifier::clean($normalized, 'laviola');

                if ($dry) {
                    $updated[] = [
                        'id'      => $row->id,
                        'preview' => Str::limit(strip_tags($clean), 120)
                    ];
                } else {
                    DB::table('posts')->where('id',$row->id)->update([
                        'content' => $clean,
                        'updated_at' => now(),
                    ]);
                    $updated[] = ['id'=>$row->id, 'status'=>'updated'];
                }
            }
        });

        return response()->json([
            'from' => $fromId,
            'to'   => $toId,
            'dry'  => $dry,
            'results' => $updated,
        ]);
    }

    protected function normalizeHtml(string $html): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0','UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);
        $root  = $dom->getElementById('root');

        foreach (['//noscript','//script','//style'] as $q) {
            foreach ($xpath->query($q) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        foreach ($xpath->query('.//img', $root) as $img) {
            if ($img->hasAttribute('data-lazy-src') && !$img->hasAttribute('src')) {
                $img->setAttribute('src', $img->getAttribute('data-lazy-src'));
            }
            if ($img->hasAttribute('data-lazy-srcset') && !$img->hasAttribute('srcset')) {
                $img->setAttribute('srcset', $img->getAttribute('data-lazy-srcset'));
            }
            $img->removeAttribute('data-lazy-src');
            $img->removeAttribute('data-lazy-srcset');
        }

        // slot ADV dopo primo H3
        $h3 = $xpath->query('.//h3',$root)->item(0);
        if ($h3) {
            $slot = $dom->createElement('div');
            $slot->setAttribute('class','ad-slot ad-slot--after-h3');
            $slot->setAttribute('data-ad','in-article-1');
            $h3->parentNode->insertBefore($slot,$h3->nextSibling);
        }

        // slot ADV dopo secondo P
        $ps = $xpath->query('.//p',$root);
        if ($ps->length >= 2) {
            $p2 = $ps->item(1);
            $slot2 = $dom->createElement('div');
            $slot2->setAttribute('class','ad-slot ad-slot--after-p2');
            $slot2->setAttribute('data-ad','in-article-2');
            $p2->parentNode->insertBefore($slot2,$p2->nextSibling);
        }

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        libxml_clear_errors();
        return $out;
    }
}
