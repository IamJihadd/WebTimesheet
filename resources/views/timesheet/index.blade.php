<x-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center ">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Timesheets') }}
            </h2>
            <a href="{{ route('timesheet.create') }}"
                class="px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                New Timesheet
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-sm:py-1">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($timesheets->isEmpty())
                        <p class="text-center text-gray-500">No timesheets yet. Create your first one!</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3">Week Period</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Total Hours</th>
                                        <th class="px-6 py-3">Submitted At</th>
                                        <th class="px-6 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timesheets as $timesheet)
                                        <tr class="border-b dark:border-gray-700">
                                            <td class="px-6 py-4">
                                                {{ $timesheet->week_start->format('d M Y') }} -
                                                {{ $timesheet->week_end->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($timesheet->status === 'submitted')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Pending Approval
                                                    </span>
                                                @elseif($timesheet->status === 'approved')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($timesheet->status === 'rejected')
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ ucfirst($timesheet->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="font-semibold">{{ $timesheet->total_regular_hours }}h</span>
                                                regular
                                                <br>
                                                <span
                                                    class="font-semibold">{{ $timesheet->total_overtime_hours }}h</span>
                                                overtime
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $timesheet->submitted_at ? $timesheet->submitted_at->format('d M Y H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($timesheet->canEdit())
                                                    <a href="{{ route('timesheet.edit', $timesheet->id) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                        Edit
                                                    </a>
                                                @else
                                                    <a href="{{ route('timesheet.show', $timesheet->id) }}"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        View
                                                    </a>
                                                @endif

                                                @if ($timesheet->isRejected())
                                                    <div class="mt-1 text-xs text-red-600">
                                                        Reason: {{ $timesheet->rejection_reason }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
