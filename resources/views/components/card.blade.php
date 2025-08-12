@props(['class' => ''])

<div {{ $attributes->merge([
    'class' => "bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6 " . $class
]) }}>
    {{ $slot }}
</div>
