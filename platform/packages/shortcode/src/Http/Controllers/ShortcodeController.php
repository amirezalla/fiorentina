<?php

namespace Botble\Shortcode\Http\Controllers;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Shortcode\Events\ShortcodeAdminConfigRendering;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Http\Requests\GetShortcodeDataRequest;
use Botble\Shortcode\Http\Requests\RenderBlockUiRequest;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class ShortcodeController extends BaseController
{
    public function ajaxGetAdminConfig(?string $key, GetShortcodeDataRequest $request)
    {
        ShortcodeAdminConfigRendering::dispatch();

        $registered = shortcode()->getAll();

        $key = $key ?: $request->input('key');

        $data = Arr::get($registered, $key . '.admin_config');

        $attributes = [];
        $content = null;

        if ($code = $request->input('code')) {
            $compiler = shortcode()->getCompiler();
            $attributes = $compiler->getAttributes(html_entity_decode($code));
            $content = $compiler->getContent();
        }

        if ($data instanceof Closure || is_callable($data)) {
            $data = call_user_func($data, $attributes, $content);

            if ($modifier = Arr::get($registered, $key . '.admin_config_modifier')) {
                $data = call_user_func($modifier, $data, $attributes, $content);
            }

            $data = $data instanceof FormAbstract ? $data->renderForm() : $data;
        }

        $data = apply_filters(SHORTCODE_REGISTER_CONTENT_IN_ADMIN, $data, $key, $attributes);

        if (! $data) {
            $data = Html::tag('code', Shortcode::generateShortcode($key, $attributes))->toHtml();
        }

        return $this
            ->httpResponse()
            ->setData($data);
    }

public function ajaxRenderUiBlock(RenderBlockUiRequest $request)
{
    $name       = (string) $request->input('name');
    $attributes = (array)  $request->input('attributes', []);

    if (! array_key_exists($name, Shortcode::getAll())) {
        return $this->httpResponse()->setData(null);
    }

    // Build a stable cache key (include anything that changes the HTML)
    $vary = [
        'attrs'  => $attributes,
        'locale' => app()->getLocale(),
        // If output differs by user/role, uncomment the next line:
        // 'user'   => auth()->id() ?: 'guest',
    ];
    $key = 'ui:shortcode:'.$name.':'.md5(json_encode($vary));
    $ttl = now()->addMinutes(5);

    $render = function () use ($name, $attributes) {
        $code = Shortcode::generateShortcode($name, $attributes);
        return Shortcode::compile($code, true)->toHtml();
    };

    // Use tags if your cache store supports them (e.g., Redis) for easy invalidation
    if (method_exists(Cache::getStore(), 'tags')) {
        $html = Cache::tags(['ui-shortcodes', 'shortcode-'.$name])->remember($key, $ttl, $render);
    } else {
        $html = Cache::remember($key, $ttl, $render);
    }

    return $this->httpResponse()->setData($html);
}
}
