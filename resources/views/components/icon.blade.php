@props(['name'])

<i {{ $attributes->merge(['class' => 'pi pi-' . $name]) }}></i>
