<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Rules\ValidateMediaFileIds;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Media\Models\MediaFile;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Throwable;


class VideoController extends BaseController
{

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add("Videos");
    }

    public function index()
    {
        /*Schema::table('videos', function (Blueprint $table) {
            $table->boolean('is_for_home')->default(false);
            $table->boolean('is_for_post')->default(false);
        });*/
        $this->pageTitle("Videos List");
        $videos = Video::query()->withCount('mediaFiles')->latest()->paginate(20);
        return view('videos.view', compact('videos'));
    }


    public function create()
    {
        $this->pageTitle("Crea Video");

        return view('videos.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string'],
            'mode' => ['required', Rule::in(Video::PLAYLIST_MODES)],
            'status' => ['required', Rule::in(Video::STATUSES)],
            'delay' => ['required', 'integer', 'in:1,5,10,15,30,60,120'], // Updated delay validation to include new values
            'videos' => ['nullable', 'array'],
            'videos.*' => [Rule::exists(MediaFile::class,'id')],
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $video_ids = $request->videos;
                $is_random = $request->mode == Video::PLAYLIST_MODE_RANDOM;
                $is_published = $request->status == Video::STATUS_PUBLISHED;

                // Create a new Video record with delay included
                $video = Video::query()->create([
                    'title' => $request->title,
                    'is_random' => $is_random,
                    'published_at' => $is_published ? now() : null,
                    'is_for_home'=>$request->has('is_for_home'),
                    'is_for_post'=>$request->has('is_for_post'),
                    'delay' => $request->delay, // Store the delay value
                ]);

                // Sync video files with priorities if provided
                if ($request->filled('videos')) {
                    $mediaFilesData = collect($video_ids)
                        ->mapWithKeys(function ($item, $key) {
                            return [
                                $item => [
                                    'priority' => $key + 1,
                                ],
                            ];
                        })->toArray();
                    $video->mediaFiles()->sync($mediaFilesData);
                }

                return redirect()->route('videos.index')->with('success', 'Videos uploaded successfully.');
            });
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Failed to upload videos.');
        }
    }



    public function edit($video)
    {
        $video = Video::query()
            ->with('mediaFiles')
            ->findOrFail($video);
        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, $video)
    {
        /** @var Video $video */
        $video = Video::query()
            ->with('mediaFiles')
            ->findOrFail($video);
        $this->validate($request, [
            'title' => ['required', 'string'],
            'mode' => ['required', Rule::in(Video::PLAYLIST_MODES)],
            'status' => ['required', Rule::in(Video::STATUSES)],
            'videos' => ['nullable', 'array'],
            'videos.*' => [Rule::exists(MediaFile::class,'id')],
        ]);
        try {
            return DB::transaction(function () use ($request, $video) {
                $video_ids = $request->videos;
                $diffCount = $video->mediaFiles->pluck('id')->diff($video_ids)->count();
                $is_random = $request->mode == Video::PLAYLIST_MODE_RANDOM;
                $is_published = $request->status == Video::STATUS_PUBLISHED;
                $video->update([
                    'title' => $request->title,
                    'is_random' => $is_random,
                    'published_at' => $is_published ? now() : null,
                    'is_for_home'=>$request->has('is_for_home'),
                    'is_for_post'=>$request->has('is_for_post'),
                ]);
                if ($diffCount && $request->filled('videos')) {
                    $mediaFilesData = collect($video_ids)
                        ->mapWithKeys(function ($item, $key) {
                            return [
                                $item => [
                                    'priority' => $key + 1,
                                ],
                            ];
                        })->toArray();
                    $video->mediaFiles()->sync($mediaFilesData);
                }
                return redirect()->route('videos.index')->with('success', 'Videos uploaded successfully.');
            });
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Failed to upload videos.');
        }
    }

    public function destroy($video)
    {
        $video = Video::query()
            ->with('mediaFiles')
            ->findOrFail($video);
        try {
            return DB::transaction(function () use ($video) {
                $video->mediaFiles()->detach();
                $video->delete();
                return redirect()->route('videos.index')->with('success', 'Video deleted successfully.');
            });
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete video.');
        }
    }
}
