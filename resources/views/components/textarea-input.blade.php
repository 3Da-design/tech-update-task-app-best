@props(['disabled' => false])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'app-input']) }}>{{ $slot }}</textarea>
