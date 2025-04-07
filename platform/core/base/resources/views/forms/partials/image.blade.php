@php
    $allowThumb = Arr::get($attributes, 'allow_thumb', true);
@endphp
@dd($allowThumb, $name, $value);
<x-core::form.image :allow-thumb="$allowThumb" :name="$name" :value="$value" action="select-image" />
