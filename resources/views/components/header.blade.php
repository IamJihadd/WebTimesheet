<header
    class=" bg-gray-800 after:pointer-events-none after:absolute after:inset-x-0 after:inset-y-0 after:border-y after:border-white/10">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">{{ $slot }}</h2>
        {{-- {{ $slot }} adalah variabel khusus untuk mengambil isi dari tag komponen yang ada di view --}}
    </div>
</header>
