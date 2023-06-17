@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'filament-forms-input block w-full transition duration-75 rounded-lg shadow-sm outline-none focus:ring-1 focus:ring-inset disabled:opacity-70 dark:bg-gray-700 dark:text-white border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:focus:border-primary-500']) !!}>
