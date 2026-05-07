@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center mb-2">
    <h1 class="text-xl font-bold tracking-tight text-[#ededed]">{{ $title }}</h1>
    <p class="text-xs text-[#a1a1a1] mt-1.5 leading-relaxed">{{ $description }}</p>
</div>
