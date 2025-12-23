<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Timesheet - Week {{ $timesheet->week_start->format('d M') }} to
            {{ $timesheet->week_end->format('d M Y') }} - Week No. {{ $timesheet->week_start->format('Y-W') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('timesheet.store-new') }}" method="POST" id="timesheetForm">
                        @csrf
                        <input type="hidden" name="week_start" value="{{ $timesheet->week_start->format('Y-m-d') }}">
                        <input type="hidden" name="week_end" value="{{ $timesheet->week_end->format('Y-m-d') }}">

                        <x-form-table :timesheet="$timesheet" :disciplines="$disciplines" :level-grades="$levelGrades" :project-codes="$projectCodes"
                            :cost-codes="$costCodes" :tasks="$tasks" />

                        <div class="w-full mt-6 flex justify-between">
                            <a href="{{ route('timesheet.index') }}"
                                class="px-6 py-2 bg-gray-300 max-h-10 text-center text-gray-700 rounded-md hover:bg-gray-400">
                                {{-- sm:max-w-32 --}}
                                Cancel
                            </a>

                            <div class="space-x-2 space-y-0 flex max-sm:flex-col max-sm:space-x-0 max-sm:space-y-2">
                                <button type="submit" name="action" value="save"
                                    class="px-4 py-2 bg-blue-600 h-10  text-white rounded-md hover:bg-blue-700">
                                    {{-- sm:max-w-32 --}}
                                    Save Draft
                                </button>
                                <button type="submit" name="action" value="submit"
                                    class="px-4 py-2 bg-green-600 h-10 text-white rounded-md hover:bg-green-700"
                                    {{-- sm:max-w-32 --}}
                                    onclick="return confirm('Submit timesheet for approval?')">
                                    Submit for Approval
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Script Hitung Jam (Lama) --}}
        <script src="{{ asset('js/timesheet-calculator.js') }}"></script>
        {{-- Script Autofill (BARU) --}}
        <script src="{{ asset('js/timesheet-automation.js') }}"></script>
    @endpush
</x-layout>
