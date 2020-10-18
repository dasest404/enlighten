@props(['collapsed' => true])

<div x-data="{
        open: false,
        collapsed: {{ $collapsed }}
    }"
     x-bind:class="{
        'fixed top-0 left-0 w-screen h-screen p-8 bg-gray-800 z-50': open,
        'h-full relative': !open
     }"
     {{ $attributes }}
    >
    <div class="w-full py-1 px-4 flex justify-end bg-transparent">
        <button x-on:click="open = !open" type="button" class="text-gray-300 hover:text-gray-500 focus:outline-none">
            <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        </button>
    </div>
    <div class="w-full h-full overflow-y-auto pb-8" x-bind:class="{'max-h-120 pb-0': (!open && collapsed)}">
        {{ $slot }}
    </div>
</div>
