<x-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Help Desk IT') }}
            </h2>
        </div>
    </x-slot>
    <div class="flex justify-center m-5">
        @include('components.undermaintenance-view')
    </div>
</x-layout>
