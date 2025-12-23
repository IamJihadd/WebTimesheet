<x-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monthly Employee Report') }}
        </h2>
    </x-slot>

    <div class="py-8 max-sm:py-0">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-10">

            {{-- 1. FILTER SECTION --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('report.monthly') }}"
                        class="flex w-auto flex-col sm:flex-row gap-4 items-end max-sm:items-start max-sm:w-full">

                        {{-- Filter Bulan --}}
                        <div class="w-auto max-sm:w-full">
                            <label for="month"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                            <select name="month" id="month"
                                class="w-auto rounded-md max-sm:w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Filter Tahun --}}
                        <div class="w-auto max-sm:w-full">
                            <label for="year"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                            <select name="year" id="year"
                                class=" w-auto rounded-md max-sm:w-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300">
                                @foreach (range(now()->year - 2, now()->year + 1) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                            class="px-4 py-2 w-auto bg-blue-600 text-white rounded-md max-sm:w-full hover:bg-blue-700 focus:outline-none transition">
                            View Report
                        </button>
                    </form>
                </div>
            </div>

            {{-- 2. SUMMARY CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Regular Hours</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ (float) $grandTotalRegular }} <span class="text-lg font-normal text-gray-500">hrs</span>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Overtime Hours</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">
                        {{ (float) $grandTotalOvertime }} <span class="text-lg font-normal text-gray-500">hrs</span>
                    </div>
                </div>
            </div>

            {{-- 3. EMPLOYEE TABLE --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Employee Breakdown</h3>

                    @if ($summary->isEmpty())
                        <div class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                            <p>No submitted/approved timesheets found for this period.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border border-gray-200 dark:border-gray-700">
                                <thead
                                    class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <tr>
                                        <th class="px-6 py-3 border-b dark:border-gray-600">Employee Name</th>
                                        <th class="px-6 py-3 border-b dark:border-gray-600">ID / NIK</th>

                                        {{-- Kolom Divisi hanya muncul untuk Manager --}}
                                        @if (auth()->user()->isManager())
                                            <th class="px-6 py-3 border-b dark:border-gray-600">Department</th>
                                        @endif

                                        <th class="px-6 py-3 border-b dark:border-gray-600 text-center">Regular</th>
                                        <th class="px-6 py-3 border-b dark:border-gray-600 text-center text-red-600">
                                            Overtime</th>
                                        <th
                                            class="px-6 py-3 border-b dark:border-gray-600 text-center bg-gray-200 dark:bg-gray-600">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summary as $row)
                                        <tr
                                            class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            {{-- Nama Karyawan --}}
                                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                                {{ $row->employee_name }}
                                            </td>
                                            {{-- ID Karyawan --}}
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $row->employee_id }}
                                            </td>
                                            {{-- Divisi --}}
                                            @if (auth()->user()->isManager())
                                                <td class="px-6 py-4 text-gray-500">
                                                    {{ $row->employee_divisi ?? '-' }}
                                                </td>
                                            @endif

                                            {{-- Jam Kerja --}}
                                            <td class="px-6 py-4 text-center font-medium">
                                                {{ (float) $row->total_regular }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-center font-bold text-red-600 bg-red-50 dark:bg-red-900/10">
                                                {{ (float) $row->total_overtime }}
                                            </td>
                                            <td class="px-6 py-4 text-center font-bold bg-gray-50 dark:bg-gray-900">
                                                {{ (float) ($row->total_regular + $row->total_overtime) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{-- Footer Total --}}
                                <tfoot class="bg-gray-100 dark:bg-gray-700 font-bold border-t-2 border-gray-300">
                                    <tr>
                                        <td class="px-6 py-3 text-right"
                                            colspan="{{ auth()->user()->isManager() ? 3 : 2 }}">
                                            GRAND TOTAL
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            {{ (float) $grandTotalRegular }}
                                        </td>
                                        <td class="px-6 py-3 text-center text-red-600">
                                            {{ (float) $grandTotalOvertime }}
                                        </td>
                                        <td class="px-6 py-3 text-center bg-gray-200 dark:bg-gray-600">
                                            {{ (float) ($grandTotalRegular + $grandTotalOvertime) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
