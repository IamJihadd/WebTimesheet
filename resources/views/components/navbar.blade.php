<nav x-data="{ open: false }" class="bg-gray-900 sticky top-0 z-50 border-b border-gray-800">
    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-10">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="shrink-0">
                    <!-- Logo -->
                    <img src="{{ asset('img/logo_DEC.png') }}" alt="Your Company" class="size-10" />
                </div>
                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <x-nav-link href="{{ route('home') }} " :active="request()->is('home')">Home</x-nav-link>
                        <x-nav-link href="{{ route('timesheet.index') }}" :active="request()->is('timesheet')">Timesheet</x-nav-link>
                        @if (auth()->user()->isAdmin())
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('User Management') }}
                            </x-nav-link>
                        @endif
                        <x-nav-link href="{{ route('report.monthly') }}" :active="request()->routeIs('report.monthly')">
                            {{ __('Monthly Report') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('helpdesk.it') }}" :active="request()->is('helpdeskit')">Help Desk IT</x-nav-link>
                        <x-nav-link href="{{ route('helpdesk.hr') }}" :active="request()->is('helpdeskhr')">Help Desk HR</x-nav-link>
                    </div>
                </div>
            </div>

            <!-- User Dropdown (Desktop Only) -->
            <div class="hidden md:flex md:items-center md:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button (Mobile Only) -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-300 hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <!-- Icon Hamburger -->
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <!-- Icon Close (X) -->
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Container -->
    <!-- Menu ini akan muncul jika variabel 'open' di Alpine.js bernilai true -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         class="md:hidden bg-gray-900 border-t border-gray-800">
        
        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link href="{{ route('home') }}" :active="request()->is('home')">
                Home
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('timesheet.index') }}" :active="request()->is('timesheet')">
                Timesheet
            </x-responsive-nav-link>
            
            @if (auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    User Management
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link href="{{ route('report.monthly') }}" :active="request()->routeIs('report.monthly')">
                Monthly Report
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('helpdesk.it') }}" :active="request()->is('helpdeskit')">
                Help Desk IT
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('helpdesk.hr') }}" :active="request()->is('helpdeskhr')">
                Help Desk HR
            </x-responsive-nav-link>
        </div>

        <!-- Mobile User Info & Logout -->
        <div class="pt-4 pb-1 border-t border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>