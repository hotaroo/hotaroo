@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-md shadow-sm border-gray-300 focus:border-lime-300 focus:ring focus:ring-lime-200 focus:ring-opacity-50']) !!}>
