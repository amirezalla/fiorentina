<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage; // Correct import for Storage
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;


// v3 drivers (exist only on Intervention Image v3)
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Botble\Media\RvMedia;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Botble\Member\Models\Member;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Category;
use Botble\Slug\Models\Slug;
use FriendsOfBotble\Comment\Models\Comment;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use Botble\Base\Models\MetaBox;
use Botble\Setting\Models\Setting;
use App\Jobs\ImportWpPostsJob;
use App\Jobs\ImportWpMetaJob;
use App\Jobs\ImportWpSlugsJob;




class WpImportController extends BaseController
{

    public function __construct(protected RvMedia $rvMedia)
    {
    }
    public function users()
    {
        try {
            // Fetch all users from the frntn_users table on the mysql2 connection
            $users = DB::connection('mysql2')
                ->table('frntn_users')
                ->get();
    
            if (!$users) {
                return response()->json(['message' => 'Users not found in frntn_users table.'], 404);
            }
    
            foreach ($users as $user) {
                // Check if a user with the same email already exists in the `members` table
                $existingMember = Member::where('email', $user->user_email)->first();
                if ($existingMember) {
                    continue; // Skip this user if email already exists
                }
    
                // Determine the confirmation timestamp
                $confirmed_at = !$user->user_activation_key ? null : '2024-09-24 13:42:15';
    
                // Import the user data into the Member model, including user_login
                $member = new Member();
                $member->setRawAttributes([
                    'id'           => $user->ID,
                    'user_login'   => $user->user_login,       // Importing the user_login
                    'first_name'   => $user->user_nicename,      // Assuming user_nicename is the first name
                    'last_name'    => '',                      // No last name field in the source table
                    'email'        => $user->user_email,
                    'password'     => $user->user_pass,          // Use raw WordPress-hashed password
                    'avatar_id'    => null,                    // Set null or default value
                    'confirmed_at' => $confirmed_at,
                    'dob'          => null,                    // Set null or default value
                    'phone'        => null,                    // Set null or default value
                    'description'  => null,                    // Set null or default value
                    'gender'       => null,                    // Set null or default value
                ]);
    
                $member->save();
            }
    
            return response()->json([
                'message' => 'Users imported successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing users.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    

public function importPostsWithoutMeta()
    {
        // Dispatch an async job so the HTTP request is short
        ImportWpPostsJob::dispatch();

        return response()->json([
            'message' => 'Import scheduled: posts (without meta). Check logs / horizon for progress.'
        ], 202);
    }

public function importMetaForPosts(Request $request)
{
    DB::connection()->disableQueryLog();
    DB::connection('mysql2')->disableQueryLog();

    // Defaults to entire posts table if not provided
    $minId = (int) Post::min('id') ?: 1;
    $maxId = (int) Post::max('id') ?: $minId;

    $startId = (int) $request->query('start_id', $minId);
    $endId   = (int) $request->query('end_id', $maxId);

    if ($startId > $endId) {
        [$startId, $endId] = [$endId, $startId];
    }

    $batch  = max(10, (int) $request->query('batch', 20)); // posts per HTTP request
    $images = (int) $request->query('images', 1);           // 1 = import featured images; 0 = skip
    $debug  = (int) $request->query('debug', 1);

    // Descending walk: start cursor just AFTER the endId, then fetch id < cursor
    $cursor = $endId + 1;

    return redirect()->route('wp.meta.step', compact('startId', 'endId', 'cursor', 'batch', 'images', 'debug'));
}

public function metaStep(Request $request)
{
    DB::connection()->disableQueryLog();
    DB::connection('mysql2')->disableQueryLog();
    @ini_set('max_execution_time', '0');
    @ini_set('memory_limit', '512M');

    $startId = (int) $request->query('startId');
    $endId   = (int) $request->query('endId');
    $cursor  = (int) $request->query('cursor', $endId + 1); // walk downward
    $batch   = max(10, (int) $request->query('batch', 20));
    $images  = (int) $request->query('images', 1);
    $debug   = (int) $request->query('debug', 0);

    // Fetch next slice DESC where id < cursor
    $posts = Post::query()
        ->whereBetween('id', [$startId, $endId])
        ->where('id', '<', $cursor)
        ->orderByDesc('id')
        ->limit($batch)
        ->get(['id', 'image']);

    if ($posts->isEmpty()) {
        return $this->progressView([
            'done'        => true,
            'startId'     => $startId,
            'endId'       => $endId,
            'cursor'      => $cursor,
            'batch'       => $batch,
            'images'      => $images,
            'debug'       => $debug,
            'processed'   => 0,
            'errors'      => [],
            'nextUrl'     => null,
        ]);
    }

    $errors = [];
    $processed = 0;

    // In DESC mode, the next cursor should become the SMALLEST id we processed this page
    $lastId = $cursor;

    foreach ($posts as $p) {
        $lastId = (int) $p->id; // this keeps decreasing in DESC order

        try {
            // Pull only needed WP meta for THIS post
            $meta = DB::connection('mysql2')
                ->table('frntn_postmeta')
                ->where('post_id', $p->id)
                ->whereIn('meta_key', ['_thumbnail_id', '_yoast_wpseo_primary_category'])
                ->pluck('meta_value', 'meta_key');

            $primaryCategoryId = isset($meta['_yoast_wpseo_primary_category'])
                ? (int) $meta['_yoast_wpseo_primary_category'] : null;

            Post::where('id', $p->id)->update([
                'format_type' => 'post',
                'category_id' => $primaryCategoryId ?: null,
                'updated_at'  => now(),
            ]);

            // Import featured image if requested and missing
            if ($images === 1) {
                $thumbId = isset($meta['_thumbnail_id']) ? (int) $meta['_thumbnail_id'] : null;
                if ($thumbId && empty($p->image)) {
                    $res = $this->importFeaturedImage($p->id, $thumbId, (bool) $debug);
                    if (($res['ok'] ?? false) === false) {
                        $reason = $res['reason'] ?? 'unknown';
                        $errors[] = "Post {$p->id}: failed to import image (attachment {$thumbId}) — {$reason}";
                    }
                }
            }

            unset($meta);
            $processed++;
            gc_collect_cycles();

        } catch (\Throwable $e) {
            $msg = "Post {$p->id} error: " . $e->getMessage();
            $errors[] = $msg;
            Log::error('[WP Meta Import] ' . $msg, ['trace' => substr($e->getTraceAsString(), 0, 2000)]);
        }
    }

    // Are there more posts with id >= startId and < lastId ?
    $hasMore = Post::query()
        ->whereBetween('id', [$startId, $endId])
        ->where('id', '<', $lastId)
        ->exists();

    $nextUrl = $hasMore
        ? route('wp.meta.step', [
            'startId' => $startId,
            'endId'   => $endId,
            // move the cursor downward (next page must fetch id < this)
            'cursor'  => $lastId,
            'batch'   => $batch,
            'images'  => $images,
            'debug'   => $debug,
        ])
        : null;

    return $this->progressView([
        'done'        => !$hasMore,
        'startId'     => $startId,
        'endId'       => $endId,
        'cursor'      => $lastId, // smallest processed id in this page
        'batch'       => $batch,
        'images'      => $images,
        'debug'       => $debug,
        'processed'   => $processed,
        'errors'      => $errors,
        'nextUrl'     => $nextUrl,
    ]);
}
protected function importFeaturedImage(int $postId, int $attachmentId, bool $debug = false): array
{
    $ctx = ['postId'=>$postId, 'attachmentId'=>$attachmentId];

    try {
        // 0) Already set?
        $has = \Botble\Blog\Models\Post::query()->where('id', $postId)->value('image');
        if (!empty($has)) {
            return ['ok'=>true, 'reason'=>'image already set'];
        }

        // 1) Attachment row & GUID
        $att = DB::connection('mysql2')
            ->table('frntn_posts')
            ->where('ID', $attachmentId)
            ->first(['ID','post_title','post_excerpt','guid']);

        if (!$att)  { $this->maybeDd($debug, 'Attachment not found', $ctx); return ['ok'=>false,'reason'=>'attachment not found']; }
        if (empty($att->guid)) { $this->maybeDd($debug, 'Attachment GUID empty', $ctx+['att'=>$att]); return ['ok'=>false,'reason'=>'attachment guid empty']; }

        // 2) Normalize URL and download (retry with encoded basename if needed)
        $origUrl = $this->normalizeGuidUrl((string)$att->guid);
        $dl = $this->curlDownload($origUrl);
        if ($dl['ok'] === false) {
            $encodedUrl = $this->encodeBasename($origUrl);
            if ($encodedUrl !== $origUrl) {
                $dl = $this->curlDownload($encodedUrl);
                $origUrl = $encodedUrl;
            }
        }
        if ($dl['ok'] === false) {
            $this->maybeDd($debug, 'Download failed', $ctx + ['url'=>$origUrl, 'why'=>$dl['reason']]);
            return ['ok'=>false, 'reason'=>$dl['reason']];
        }

        // 3) Build ALT (caption > alt > image_meta.caption)
        $meta = DB::connection('mysql2')
            ->table('frntn_postmeta')
            ->where('post_id', $attachmentId)
            ->pluck('meta_value', 'meta_key');

        $wpAlt     = $meta['_wp_attachment_image_alt'] ?? '';
        $wpMetaSer = $meta['_wp_attachment_metadata'] ?? null;
        $imgMetaCaption = '';
        if ($wpMetaSer) {
            try {
                $arr = @unserialize($wpMetaSer);
                if (is_array($arr) && isset($arr['image_meta']['caption'])) {
                    $imgMetaCaption = (string) $arr['image_meta']['caption'];
                }
            } catch (\Throwable $e) {}
        }
        $alt = trim($att->post_excerpt) ?: trim($wpAlt) ?: trim($imgMetaCaption) ?: '';

        // 4) Prepare destination path on S3 (preserve original filename)
        $basename = basename(parse_url($origUrl, PHP_URL_PATH) ?? '');
// Build a safe, short filename
$safeName = $this->buildSafeFilename($origUrl, $attachmentId, 80);

// folders by year/month (like WP)
$ym   = date('Y/m');
$path = "posts/{$ym}/{$safeName}";

$disk = Storage::disk('wasabi');

// Ensure unique name if file already exists (append -{id} before the extension)
if ($disk->exists($path)) {
    $dot  = strrpos($safeName, '.');
    $base = $dot !== false ? substr($safeName, 0, $dot) : $safeName;
    $ext  = $dot !== false ? substr($safeName, $dot) : '';
    $path = "posts/{$ym}/{$base}-{$attachmentId}{$ext}";
}

        // 5) Stream upload to S3
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $dl['body']);
        rewind($stream);

        $opts = ['visibility' => 'public'];
        if (!empty($dl['content_type'])) {
            $opts['ContentType'] = $dl['content_type'];
        }

        $ok = $disk->writeStream($path, $stream, $opts);
        fclose($stream);

        if ($ok === false) {
            $this->maybeDd($debug, 'S3 writeStream failed', $ctx + ['path'=>$path]);
            return ['ok'=>false, 'reason'=>'s3 write failed'];
        }

        // 6) Get public URL and save to post
        $url = $disk->url($path);

        if (!$url) {
            $this->maybeDd($debug, 'S3 url() returned empty', $ctx + ['path'=>$path]);
            return ['ok'=>false, 'reason'=>'s3 url empty'];
        }
        $this->makeThumbnails($path);
        \Botble\Blog\Models\Post::where('id', $postId)->update([
            'image'      => $url,
            'updated_at' => now(),
        ]);

        // Optional: if you also want to store ALT somewhere (Botble usually keeps it in media library),
        // you can add a MetaBox or a custom table here.

        return ['ok'=>true, 'reason'=>'uploaded to s3'];

    } catch (\Throwable $e) {
        Log::error('[WP Image Import] hard failure', $ctx + ['err'=>$e->getMessage()]);
        $this->maybeDd($debug, 'Exception thrown', $ctx + ['exception'=>$e->getMessage()]);
        return ['ok'=>false, 'reason'=>'exception: '.$e->getMessage()];
    }
}
protected function makeImageManager(): ImageManager
{
    // Intervention Image v3: construct with a Driver instance
    if (class_exists(GdDriver::class)) {
        if (extension_loaded('imagick') && class_exists(ImagickDriver::class)) {
            return new ImageManager(new ImagickDriver());
        }
        return new ImageManager(new GdDriver());
    }

    // Intervention Image v2 fallback: old array syntax
    $driver = extension_loaded('imagick') ? 'imagick' : 'gd';
    return new ImageManager(['driver' => $driver]);
}

/**
 * Make WP-like hard-cropped thumbnails next to the original on the 'laviolas3' disk.
 *
 * @param string $path  e.g. "posts/2025/08/1-7-1.jpg"
 * @param array  $sizes e.g. [[150,150],[540,360],[565,375]]
 */
protected function makeThumbnails(string $path, array $sizes = [[150,150],[540,360],[565,375]]): void
{
    $disk = Storage::disk('wasabi');
    if (!$disk->exists($path)) return;

    $manager = $this->makeImageManager();

    // Read original (stream for low memory)
    $stream = $disk->readStream($path);
    if ($stream === false) return;

    // v3 uses ->read(); v2 uses ->make()
    if (method_exists($manager, 'read')) {
        $image = $manager->read($stream); // v3
    } else {
        $image = $manager->make(stream_get_contents($stream)); // v2
    }
    if (is_resource($stream)) @fclose($stream);

    $info     = pathinfo($path);
    $dir      = $info['dirname']  ?? '';
    $filename = $info['filename'] ?? 'image';
    $ext      = strtolower($info['extension'] ?? 'jpg');

    foreach ($sizes as [$w, $h]) {
        // Clone
        if (method_exists($image, 'clone')) {
            $im = $image->clone(); // v3
        } else {
            $im = clone $image;    // v2
        }

        // Hard crop to exact box
        if (method_exists($im, 'cover')) {
            $im = $im->cover($w, $h);   // v3
        } else {
            $im->fit($w, $h);           // v2
        }

        // Encode
        if (method_exists($im, 'toJpeg')) {
            // v3 encoders
            $binary = match ($ext) {
                'png'  => (string) $im->toPng()->toString(),
                'webp' => (string) $im->toWebp(90)->toString(),
                default => (string) $im->toJpeg(90)->toString(),
            };
        } else {
            // v2 encoders
            $format = in_array($ext, ['png','webp','jpg','jpeg']) ? $ext : 'jpg';
            $binary = (string) $im->encode($format, 90);
            if ($format === 'jpg') $ext = 'jpg';
        }

        $newPath = ($dir && $dir !== '.') ? "{$dir}/{$filename}-{$w}x{$h}.{$ext}"
                                          : "{$filename}-{$w}x{$h}.{$ext}";

        $disk->put($newPath, $binary, ['visibility' => 'public']);
    }
}

/** Normalize legacy GUIDs: prefer https; strip non-signed query strings */
protected function normalizeGuidUrl(string $url): string
{
    if (str_starts_with($url, 'http://')) {
        $url = preg_replace('#^http://#i', 'https://', $url);
    }
    $parts = parse_url($url);
    if (!empty($parts['query']) && !str_contains($parts['query'], 'X-Amz-')) {
        $url = preg_replace('/\?.*$/', '', $url);
    }
    return $url;
}

/** URL-encode only the filename to fix spaces/special chars */
protected function encodeBasename(string $url): string
{
    $p = parse_url($url);
    if (empty($p['path'])) return $url;
    $segs = explode('/', $p['path']);
    $last = array_pop($segs);
    $p['path'] = implode('/', $segs) . '/' . rawurlencode($last);

    $rebuilt = ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '');
    if (!empty($p['port'])) $rebuilt .= ':' . $p['port'];
    $rebuilt .= $p['path'];
    if (!empty($p['query'])) $rebuilt .= '?' . $p['query'];
    if (!empty($p['fragment'])) $rebuilt .= '#' . $p['fragment'];
    return $rebuilt;
}

/** Robust downloader with cURL; returns body and content_type or reason */
protected function curlDownload(string $url): array
{
    if (!function_exists('curl_init')) {
        $bin = @file_get_contents($url);
        if ($bin === false) return ['ok'=>false, 'reason'=>'download failed'];
        return ['ok'=>true, 'body'=>$bin, 'content_type'=>null];
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; WP-Importer/1.0; +https://laviola.it)',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_HTTPHEADER     => ['Accept: image/*,*/*;q=0.8'],
        CURLOPT_HEADER         => true,
    ]);

    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok'=>false, 'reason'=>'download failed: '.$err];
    }

    $headerSize   = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $statusCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType  = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $body         = substr($resp, $headerSize);

    curl_close($ch);

    if (!in_array($statusCode, [200, 301, 302], true)) {
        return ['ok'=>false, 'reason'=>"http status not ok: HTTP {$statusCode}"];
    }
    if ($body === '' || strlen($body) < 8) {
        return ['ok'=>false, 'reason'=>'empty body'];
    }

    return ['ok'=>true, 'body'=>$body, 'content_type'=>$contentType];
}

/** When debug=1: dd() immediately; else just log */
protected function maybeDd(bool $debug, string $msg, array $context = []): void
{
    if ($debug) {
        dd(['where'=>$msg] + $context);
    } else {
        \Log::warning('[WP Image Import DEBUG] '.$msg, $context);
    }
}


protected function buildSafeFilename(string $url, int $attachmentId, int $maxBaseLen = 80): string
{
    $basename = basename(parse_url($url, PHP_URL_PATH) ?? '');

    // Split name/ext
    $dot = strrpos($basename, '.');
    $name = $dot !== false ? substr($basename, 0, $dot) : $basename;
    $ext  = $dot !== false ? substr($basename, $dot) : '';

    // 1) Try to transliterate to ASCII (iconv may be unavailable on some builds)
    $nameAscii = function_exists('iconv')
        ? @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name)
        : $name;

    // Fallback sanitize
    $nameAscii = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $nameAscii);
    $nameAscii = trim($nameAscii, '_');

    if ($nameAscii === '') {
        $nameAscii = "image_{$attachmentId}";
    }

    // 2) Enforce max length on the basename (without extension)
    // leave room for "-{id}" that we'll append when needed
    $reserveForId = 1 + strlen((string) $attachmentId); // dash + digits
    $limit = max(10, $maxBaseLen - $reserveForId);

    if (strlen($nameAscii) > $limit) {
        $nameAscii = substr($nameAscii, 0, $limit);
    }

    // 3) Final filename (we’ll still add -id if a collision happens)
    return $nameAscii . $ext;
}


    /**
     * Minimal HTML view with progress + auto-redirect.
     * Shows errors plainly so you can see exactly what's wrong.
     */
    protected function progressView(array $data)
    {
        $auto = $data['nextUrl'] ? '<meta http-equiv="refresh" content="1;url=' . e($data['nextUrl']) . '">' : '';
        $errorsHtml = '';
        if (!empty($data['errors'])) {
            $errorsHtml = '<h3 style="color:#c00;margin-top:16px">Errors this batch ('
                . count($data['errors']) . '):</h3><ul style="color:#900">';
            foreach ($data['errors'] as $e) {
                $errorsHtml .= '<li>' . e($e) . '</li>';
            }
            $errorsHtml .= '</ul>';
        }

        $html = <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>WP Meta Import</title>
<style>
body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; padding: 18px; }
.card { border:1px solid #ddd; border-radius:10px; padding:16px; max-width: 900px; }
.btn { display:inline-block; background:#4b2d7f; color:#fff; padding:10px 14px; border-radius:8px; text-decoration:none; }
.btn:visited { color:#fff; }
.small { color:#666; font-size: 12px; }
</style>
{$auto}
</head>
<body>
<div class="card">
  <h2>WP Meta Import</h2>
  <p><strong>Range:</strong> {$data['startId']} → {$data['endId']}<br>
     <strong>Last processed ID:</strong> {$data['cursor']}<br>
     <strong>Processed this request:</strong> {$data['processed']}<br>
     <strong>Batch size:</strong> {$data['batch']}<br>
     <strong>Images:</strong> {$data['images']} (1=yes, 0=no)
    — <strong>Debug:</strong> {$data['debug']}</p>
</p>
  {$errorsHtml}
  <p>
HTML;

        if ($data['nextUrl']) {
            $html .= '<a class="btn" href="' . e($data['nextUrl']) . '">Continue</a> ';
            $html .= '<span class="small">Auto-redirecting in ~1s…</span>';
        } else {
            $html .= '<span class="btn" style="background:#2c974b">Done</span>';
        }

        $html .= <<<HTML
  </p>
</div>
</body>
</html>
HTML;

        return response($html);
    }









public function importSlugs(Request $request)
{
    DB::connection()->disableQueryLog();

    // Default full range
    $minId = (int) Post::min('id') ?: 1;
    $maxId = (int) Post::max('id') ?: $minId;

    $startId = (int) $request->query('start_id', $minId);
    $endId   = (int) $request->query('end_id', $maxId);

    if ($startId > $endId) {
        [$startId, $endId] = [$endId, $startId];
    }

    $batch   = max(10, (int) $request->query('batch', 100)); // 100 as requested
    $reverse = (int) $request->query('reverse', 0);          // 1 = from end to start

    // Cursor is "last processed id"
    $cursor = $reverse ? ($endId + 1) : ($startId - 1);

    return redirect()->route('wp.slugs.step', compact('startId','endId','cursor','batch','reverse'));
}

public function slugsStep(Request $request)
{
    DB::connection()->disableQueryLog();
    @ini_set('max_execution_time', '0');
    @ini_set('memory_limit', '512M');

    $startId = (int) $request->query('startId');
    $endId   = (int) $request->query('endId');
    $cursor  = $request->query('cursor');               // int-ish but we keep as given
    $batch   = max(10, (int) $request->query('batch', 100));
    $reverse = (int) $request->query('reverse', 0);

    // Figure direction + comparison operator
    if ($reverse) {
        $op       = '<';
        $orderCol = 'id';
        $orderDir = 'desc';
        $cursor   = $cursor !== null ? (int) $cursor : ($endId + 1);
    } else {
        $op       = '>';
        $orderCol = 'id';
        $orderDir = 'asc';
        $cursor   = $cursor !== null ? (int) $cursor : ($startId - 1);
    }

    // Pull next slice
    $q = Post::query()
        ->whereBetween('id', [$startId, $endId])
        ->where('id', $op, $cursor)
        ->orderBy($orderCol, $orderDir)
        ->limit($batch)
        ->get(['id','plain_slug','created_at','updated_at']);

    if ($q->isEmpty()) {
        return $this->progressView([
            'done'      => true,
            'startId'   => $startId,
            'endId'     => $endId,
            'cursor'    => $cursor,
            'batch'     => $batch,
            'images'    => null,
            'debug'     => 0,
            'processed' => 0,
            'errors'    => [],
            'nextUrl'   => null,
            'title'     => 'WP Slug Import',
        ]);
    }

    // Determine VARCHAR limit for slugs.key once
    static $slugKeyMax = null;
    if ($slugKeyMax === null) {
        try {
            $row = DB::selectOne(
                "SELECT CHARACTER_MAXIMUM_LENGTH AS len
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?",
                [DB::getDatabaseName(), DB::getTablePrefix() . 'slugs', 'key']
            );
            $slugKeyMax = $row && $row->len ? (int) $row->len : 120;
        } catch (\Throwable $e) {
            $slugKeyMax = 120;
        }
    }

    $mbLimit = function (string $value, int $limit): string {
        if ($limit <= 0) return '';
        return mb_strlen($value) <= $limit ? $value : mb_substr($value, 0, $limit);
    };

    $rows = [];
    $lastId = null;
    foreach ($q as $p) {
        $lastId = (int) $p->id;
        // Prefer existing plain_slug, fallback to slugified id/title if you want (id is safest)
        $base = trim((string) $p->plain_slug);
        if ($base === '') $base = (string) $p->id;

        $key = $mbLimit($base, $slugKeyMax);
        if ($key === '') $key = (string) $p->id;

        $rows[] = [
            'key'            => $key,
            'reference_id'   => $p->id,
            'reference_type' => Post::class,
            'created_at'     => $p->created_at ?? now(),
            'updated_at'     => now(),
        ];
    }

    if ($rows) {
        // Upsert by (reference_id, reference_type) to keep one slug per post
        Slug::query()->upsert(
            $rows,
            ['reference_id', 'reference_type'],
            ['key','updated_at']
        );
    }

    // Do we have more?
    $hasMore = Post::query()
        ->whereBetween('id', [$startId, $endId])
        ->where('id', $op, $lastId)
        ->exists();

    $nextUrl = $hasMore
        ? route('wp.slugs.step', [
            'startId' => $startId,
            'endId'   => $endId,
            'cursor'  => $lastId,
            'batch'   => $batch,
            'reverse' => $reverse,
        ])
        : null;

    return $this->progressView([
        'title'     => 'WP Slug Import',
        'done'      => !$hasMore,
        'startId'   => $startId,
        'endId'     => $endId,
        'cursor'    => $lastId,
        'batch'     => $batch,
        'images'    => null,
        'debug'     => 0,
        'processed' => count($rows),
        'errors'    => [],
        'nextUrl'   => $nextUrl,
    ]);
}



public function deleteTodayImportedPosts()
{
    try {
        // Get today's date
        $today = Carbon::today();

        // Fetch post IDs created today
        $postIds = DB::table('posts')
            ->whereDate('created_at','>', '2025-01-03 00:00:00')
            ->pluck('id');

        if ($postIds->isEmpty()) {
            return response()->json(['message' => 'No posts found for today.'], 404);
        }

        // Delete associated slugs
        DB::table('slugs')
            ->whereIn('reference_id', $postIds)
            ->where('reference_type', 'Botble\Blog\Models\Post')
            ->delete();

        // Delete the posts
        DB::table('posts')
            ->whereIn('id', $postIds)
            ->delete();

        return response()->json([
            'message' => 'Posts and slugs created today have been deleted successfully.',
            'deleted_post_ids' => $postIds,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error deleting posts and slugs.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function importCategories(){

    try{

        $posts = Post::all();
        $categoryIds = $posts->pluck('category_id')->unique()->toArray();
        $categories = DB::connection('mysql2')
            ->table('frntn_terms')
            ->whereIn('term_id', $categoryIds)
            ->get();

        foreach ($categories as $category) {
            $existingCategory = Category::where('name', $category->name)->first();
            if (!$existingCategory) {
                $newCategory = new Category();
                $newCategory->fill([
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'description' => null,
                    'parent_id' => 0,
                    'icon' => null,
                    'is_featured' => 0,
                    'order' => $category->term_order ?? 0,
                    'is_default' => 0,
                    'status' => 'published',
                    'author_id' => 1,
                    'author_type' => 'Botble\ACL\Models\User',
                ]);
                $newCategory->save();

                $slug = new Slug();
                $slug->fill([
                    'key' => Str::slug($category->name), // converts "Blog dei Tifosi" to "blog-dei-tifosi"
                    'reference_id' => $category->term_id,
                    'reference_type' => 'Botble\Blog\Models\Category',
                ]);
                $slug->save();
            }
        }

        $posts = Post::where('category_id', '>', 0)->get();
        foreach ($posts as $post) {
            DB::table('post_categories')->insert([
                'post_id' => $post->id,
                'category_id' => $post->category_id,
            ]);
        }

    }
    catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing categories.',
            'error' => $e->getMessage(),
        ], 500);
    }


}


private function category($primaryCategoryId,$post_id){
    try{


        $term = DB::connection('mysql2')
            ->table('frntn_terms')
            ->where('term_id',$primaryCategoryId)
            ->first();
        $slug= new Slug();
        $existingCategory = Category::where('name', $term->name)->first();
        if (!$existingCategory) {
            // Create and save the category
            $category = new Category();
            $category->fill([
                'id'=>$term_id,
                'name' => $term->name,
                'description' => null, // Set to null or map from another source if available
                'parent_id' => 0, // Default to no parent (adjust logic for hierarchical categories)
                'icon' => null, // Default to null or provide logic for icons
                'is_featured' => 0, // Default to not featured
                'order' => $term->term_order ?? 0, // Use `term_order` or default to 0
                'is_default' => 0, // Default to not default
                'status' => 'published', // Default to published
                'author_id' => 1, // Set default author or map as needed
                'author_type' => 'Botble\ACL\Models\User', // Default author type
            ]);
            $category->save();
            $slug->fill([
                'key'=>'category/news/'.$term->name,
                'reference_id'=>$primaryCategoryId,
                'reference_type'=>'Botble\Blog\Models\Category'
            ]);
            $slug->save();
        }
        DB::table('post_categories')->insert([
            'post_id' => $post_id,
            'category_id' => $primaryCategoryId,
        ]);


    }catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing category.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
   private function ensureBackupAndGetPublicUrl($post): string
{
$path       = ltrim($post->image, '/');
$sourceDisk = Storage::disk('wasabi');
$backupDisk = Storage::disk('wasabi_backup');

if (! $backupDisk->exists($path)) {

    // 1. Create a signed URL that lives 15 min
    $tempUrl = $sourceDisk->url($path);

    // 2. Open a read-only HTTP stream
    $stream  = fopen($tempUrl, 'rb');
    if ($stream === false) {
        throw new RuntimeException("Unable to download $tempUrl");
    }

    // 3. Stream-upload to the public bucket
    $backupDisk->writeStream($path, $stream, ['visibility' => 'public']);
}

$publicUrl = $backupDisk->url($path);
return $publicUrl;
}

    public function generateSEO(Request $request)
    {
        try {
            if($request->has('post_id')){
                $postId = $request->input('post_id');

            }
            // Retrieve the post
            $post = DB::table('posts')->where('id', $postId)->first();
    
            if (!$post) {
                return response()->json(['message' => 'Post not found.'], 404);
            }
// ---------------------------------------------------
// 1)  Pull the two meta-boxes (if they exist)
// ---------------------------------------------------
$seoMetaJson      = DB::table('meta_boxes')
    ->where('reference_id',   $postId)
    ->where('reference_type', 'Botble\Blog\Models\Post')
    ->where('meta_key',       'seo_meta')
    ->value('meta_value');        // → null if row missing

$keywordsMetaJson = DB::table('meta_boxes')
    ->where('reference_id',   $postId)
    ->where('reference_type', 'Botble\Blog\Models\Post')
    ->where('meta_key',       'vig_seo_keywords')
    ->value('meta_value');        // → null if row missing

$seo_image=$this->ensureBackupAndGetPublicUrl($post);

// ---------------------------------------------------
// 2)  Decode & inspect
//     a) SEO meta is “custom” if ANY block has
//        seo_title OR seo_description filled in.
//     b) Keywords are “present” if there is at least
//        one non-empty keyword.
// ---------------------------------------------------
$hasCustomSeoMeta = false;
if ($seoMetaJson) {
    $seoBlocks = json_decode($seoMetaJson, true) ?? [];
    $hasCustomSeoMeta = collect($seoBlocks)->contains(function ($b) {
        return !empty($b['seo_title']) || !empty($b['seo_description']);
    });
}

$hasKeywords = false;
if ($keywordsMetaJson) {
    $kwBlocks = json_decode($keywordsMetaJson, true) ?? [];
    $hasKeywords = collect($kwBlocks)
        ->pluck('keywords')->flatten()
        ->filter(fn ($k) => trim($k) !== '')
        ->isNotEmpty();
}

// ---------------------------------------------------
// 3)  Stop only if *real* SEO is already there
// ---------------------------------------------------
if ($hasCustomSeoMeta || $hasKeywords) {
    return response()->json(
        ['message' => 'SEO metadata already exists for this post.'],
        200
    );
}

            // Generate SEO content using ChatGPT API

            $apiKey = Setting::where('key', 'GPT_API')->value('value');

            $apiUrl = 'https://api.openai.com/v1/chat/completions';
    
// Complete the prompt with clear markers for the expected structure.
$prompt = "Generate SEO metadata for the following post:
    Title: {$post->name}
    Content: {$post->content}
    
    Please output the result as a JSON object with two keys:
    - \"keywords\": an array of keywords (strings)
    - \"meta_description\": a string with the meta description in Italian

    Do not include any additional text.

    Example output:
    {
        \"keywords\": [\"Felipe Melo\", \"Fiorentina\", \"Juventus\", \"calcio\", \"Firenze\", \"centrocampista\", \"viola\", \"Inter\", \"Chiellini\", \"pensionamento\", \"carriera\", \"derby\", \"allenatore\"],
        \"meta_description\": \"Leggi l'intervista di Felipe Melo, ex centrocampista viola, sulla sua carriera nel calcio, dall'amore per Firenze al passaggio alla Juventus e l'esperienza all'Inter. Scopri i retroscena e i progetti futuri dell'ex giocatore.\"
    }
    ";
    
    $maxRetries = 5;  // Maximum number of retries
    $retryCount = 0;
    $validJson  = false;
    $seoContent = null;
    $jsonData   = null;

    do {
        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Content-Type'  => 'application/json',
        ])->post($apiUrl, [
            'model'       => 'gpt-4o-mini',
            'messages'    => [
                ['role' => 'system', 'content' => 'You are an SEO assistant. Your responses must strictly follow the provided JSON format.'],
                ['role' => 'user',   'content' => $prompt],
            ],
            'max_tokens'  => 1024,
            'temperature' => 0.0,
        ]);

        $data = $response->json();

        // Check if the expected content is returned
        if (!isset($data['choices'][0]['message']['content'])) {
            return response()->json(['message' => 'Failed to generate SEO content.'], 500);
        }

        $seoContent = $data['choices'][0]['message']['content'];
        $jsonData   = json_decode($seoContent, true);

        // If json_decode returns valid data, break out of the loop
        if (json_last_error() === JSON_ERROR_NONE) {
            $validJson = true;
            break;
        }

        $retryCount++;
        // Optionally, add a small delay before retrying
        sleep(1);
    } while ($retryCount < $maxRetries);

    if (!$validJson) {
        return response()->json(['message' => 'Invalid JSON returned after multiple attempts.'], 500);
    }

    // Continue processing with $jsonData
    // For example:
    $keywords       = $jsonData['keywords'] ?? [];
    $metaDescription = $jsonData['meta_description'] ?? '';

    DB::table('meta_boxes')
    ->where('reference_id',   $postId)
    ->where('reference_type', 'Botble\Blog\Models\Post')
    ->whereIn('meta_key', ['seo_meta', 'vig_seo_keywords'])
    ->delete();

                // Save SEO data to the `meta_boxes` table
                DB::table('meta_boxes')->insert([
                    [
                        'meta_key' => 'vig_seo_keywords',
                        'meta_value'    => json_encode([['keywords' => $keywords]]), // if you need keywords in a similar structure
                        'reference_id' => $postId,
                        'reference_type' => 'Botble\Blog\Models\Post',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'meta_key' => 'seo_meta',
                        'meta_value' => json_encode([['index' => 'index','seo_title'=>$post->name , 'seo_description' => $metaDescription,"seo_image" => $seo_image]]),
                        'reference_id' => $postId,
                        'reference_type' => 'Botble\Blog\Models\Post',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
    
            return redirect()->back()->with([
                'message' => 'SEO metadata generated and saved successfully. for : ' . $post->name,
                'seo_content' => $seoContent,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error generating SEO metadata.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




public static function importComment($postId)
{
    try {
        // Fetch all comments related to the specific post ID
        $comments = DB::connection('mysql2')
            ->table('frntn_comments')
            ->where('comment_post_ID', $postId)
            ->get();

        if ($comments->isEmpty()) {
            return response()->json(['message' => 'No comments found for the post.'], 404);
        }

        foreach ($comments as $comment) {
            // Check if the comment already exists in your comments table
            if (Comment::where('id', $comment->comment_ID)->exists()) {
                continue; // Skip if the comment already exists
            }

            // Create and save the comment
            $newComment = new Comment();
            $newComment->fill([
                'id' => $comment->comment_ID, // Match WordPress comment_ID
                'reference_id' => $postId, // Post ID reference
                'reference_type' => 'Botble\Blog\Models\Post', // Assuming the comments belong to blog posts
                'name' => $comment->comment_author,
                'email' => $comment->comment_author_email,
                'content' => strip_tags($comment->comment_content), // Sanitize content
                'status' => $comment->comment_approved === '1' ? CommentStatus::APPROVED : CommentStatus::PENDING,
                'author_id' => $comment->user_id =='0'? null :Member::where('email',$comment->comment_author_email)->first()->id, // If available, update accordingly
                'author_type' => 'Botble\Member\Models\Member', // If available, update accordingly
                'reply_to' => $comment->comment_parent ?: null, // Parent comment reference
                'ip_address' => $comment->comment_author_IP,
                'user_agent' => null, // WordPress does not store user-agent by default
                'created_at' => $comment->comment_date,
                'updated_at' => $comment->comment_date_gmt,
            ]);

            $newComment->save();
        }

        return response()->json([
            'message' => 'Comments imported successfully!',
        ], 200);

    } catch (\Exception $e) {
        dd([
            'message' => 'Error importing comments.',
            'error' => $e->getMessage(),
        ]);
    }
}

    

    
}
