<x-enlighten-main-layout>
    @if(!empty($title))
        <x-slot name="title">{{ $title }}</x-slot>
    @endif

    <div class="w-full mx-auto">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mt-4">
            @forelse($modules as $module)
                <x-enlighten-area-module-panel :module="$module"></x-enlighten-area-module-panel>
            @empty
                <p class="text-white">
                    {{ __('enlighten::messages.there_are_no_examples_to_show') }}
                </p>
            @endforelse
        </div>
    </div>
</x-enlighten-main-layout>
