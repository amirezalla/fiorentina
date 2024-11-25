<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Video;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        set_time_limit(900); // Sets the maximum execution time to 15 minutes

        view()->composer('ads.includes.main-page', function (View $view) {
            $view->with('ads', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_MAIN_PAGE)->get());
        });
        view()->composer('ads.includes.blog-page', function (View $view) {
            $view->with('ads', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_BLOG_PAGE)->get());
        });
        view()->composer('ads.includes.dblog-title', function (View $view) {
            $view->with('ads', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_DBLOG_TITLE)->get());
        });
        view()->composer('ads.includes.dblog-author', function (View $view) {
            $view->with('ads', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_DBLOG_AUTHOR)->get());
        });
        view()->composer('ads.includes.dblog-P1', function (View $view) {
            $view->with('ads', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_DBLOG_P1)->first());
        });

        view()->composer('ads.includes.background-page', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_BACKGROUND_PAGE)->inRandomOrder()->first());
        });      view()->composer('ads.includes.adsdiretta', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_diretta_1)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.adsrecentp1', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp1)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.adsrecentp2', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp2)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.adsrecentp3', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp3)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.adsrecentp4', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::GROUP_recentp4)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.adsense', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::Google_adsense)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.SIZE_468X60_TOP_SX', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_468X60_TOP_SX)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.SIZE_468X60_TOP_DX', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_468X60_TOP_DX)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.SIZE_300X250_TOP', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_300X250_TOP)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.SIZE_300X250_C1', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_300X250_C1)->inRandomOrder()->first());
        });

        view()->composer('ads.includes.SIZE_230X90_DX', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::SIZE_230X90_DX)->inRandomOrder()->first());
        });

        view()->composer('ads.includes.MOBILE_HOME_TOP_24', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_HOME_TOP_24)->inRandomOrder()->first());
        });
        view()->composer('ads.includes.MOBILE_HOME_HERO_25', function (View $view) {
            $view->with('ad', Ad::query()->typeAnnuncioImmagine()->whereGroup(Ad::MOBILE_HOME_HERO_25)->inRandomOrder()->first());
        });

        view()->composer('videos.includes.adsvideo', function (View $view) {
            $is_home = request()->route()->getName();
            $video = Video::query()
                ->when($is_home, function ($q) {
                    $q->onlyForHome();
                }, function ($q) {
                    $q->onlyForPost();
                })
                ->published()
                ->first();
            if ($video) {
                $mediaFiles = $video->mediaFiles()
                    ->when($video->isRandom(), function ($q) {
                        $q->inRandomOrder();
                    }, function ($q) {
                        $q->orderBy('priority');
                    })
                    ->get();
                $video_files = $mediaFiles->map(function ($item) {
                    return url('storage/' . $item->url);
                });
                $video_file_urls = $mediaFiles->map(function ($item) {
                    return $item->pivot->url;
                });;
            } else {
                $video_files = collect();
                $video_file_urls = collect();
            }
            $view->with('video', $video)->with('video_files', $video_files)->with('video_file_urls',$video_file_urls);
        });


    }
}
