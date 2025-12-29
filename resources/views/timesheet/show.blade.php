<x-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                Timesheet Details - Week {{ $timesheet->week_start->format('d M') }} to
                {{ $timesheet->week_end->format('d M Y') }}
            </h2>
            <div class="flex items-center space-x-2">
                @if ($timesheet->status === 'submitted')
                    <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">Pending Approval</span>
                @elseif($timesheet->status === 'approved')
                    <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Approved</span>
                @elseif($timesheet->status === 'rejected')
                    <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">Rejected</span>
                @elseif($timesheet->status === 'draft')
                    <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">Draft</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
            <!-- Employee Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Employee Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                            <p class="font-medium">{{ $timesheet->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">User ID</p>
                            <p class="font-medium">{{ $timesheet->user->user_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Department</p>
                            <p class="font-medium">{{ $timesheet->user->department ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Status Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Submitted At</p>
                            <p class="font-medium">
                                {{ $timesheet->submitted_at ? $timesheet->submitted_at->format('d M Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Week No</p>
                            <p class="font-medium">
                                {{ $timesheet->week_start->format('Y-W') }}</p>
                        </div>
                        @if ($timesheet->approved_at)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Approved At</p>
                                <p class="font-medium">{{ $timesheet->approved_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Approved By</p>
                                <p class="font-medium">{{ $timesheet->approver ? $timesheet->approver->name : '-' }}</p>
                            </div>
                        @endif
                        @if ($timesheet->isRejected())
                            <div class="md:col-span-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Rejection Reason</p>
                                <p class="font-medium text-red-600">{{ $timesheet->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timesheet Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Timesheet Entries</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-center">
                                <tr>
                                    <th rowspan="3" class="px-2 py-2">Discipline</th>
                                    <th rowspan="3" class="px-2 py-2">Level Grade</th>
                                    <th rowspan="3" class="px-2 py-2">Project Code</th>
                                    <th rowspan="3" class="px-2 py-2 ">Cost Code</th>
                                    <th rowspan="3" class="px-2 py-2">Task</th>

                                    @foreach ($timesheet->getWeekDates() as $date)
                                        <th colspan="2" class="px-2 py-1  bg-gray-800/50">{{ $date['formatted'] }}</th>
                                    @endforeach

                                    <th rowspan="2" colspan="2" class="px-2 py-2">Total</th>
                                </tr>
                                <tr>
                                    @foreach ($timesheet->getWeekDates() as $date)
                                        <th colspan="2" class="px-2 py-1">{{ $date['day'] }}</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($timesheet->getWeekDates() as $date)
                                        <th class="px-3 py-1 border-r-2 border-gray-700/50 bg-gray-800">R</th>
                                        <th class="px-2 py-1 border-r-2 border-gray-600/50 bg-gray-800">OT</th>
                                    @endforeach
                                    <th class="px-2 py-1 border-r-2 border-gray-700/50 bg-gray-800">R</th>
                                    <th class="px-2 py-1 border-r-2 border-gray-600/50 bg-gray-800">OT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($timesheet->entries as $entry)
                                    <tr class="border-b-gray-500">
                                        <td class="px-3 py-3">{{ $entry->discipline }}</td>
                                        <td class="px-3 py-3">{{ $entry->level_grade }}</td>
                                        <td class="px-3 py-3">{{ $entry->project_code }}</td>
                                        <td class="px-3 py-3">{{ $entry->cost_code }}</td>
                                        <td class="px-3 py-3">{{ $entry->task }}</td>

                                        @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                            <td class="px-2 py-2 border-r-2 border-gray-700/50 text-center">{{ (float) $entry->{$day . '_regular'} }}
                                            </td>
                                            <td class="px-2 py-2 border-r-2 border-gray-600/50 text-center">
                                                {{ (float) $entry->{$day . '_overtime'} }}
                                            </td>
                                        @endforeach

                                        <td class="px-2 py-2 border-r-2 border-gray-700/50 text-center font-semibold">
                                            {{ (float) $entry->total_regular }}</td>
                                        <td class="px-2 py-2 text-center font-semibold">
                                            {{ (float) $entry->total_overtime }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="19" class="px-6 py-4 text-center text-gray-500">No entries found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                                <tr>
                                    <td colspan="5" class="px-2 py-3 text-right">Grand Total:</td>
                                    @php
                                        $grandTotalReg = 0;
                                        $grandTotalOT = 0;
                                        foreach (
                                            [
                                                'monday',
                                                'tuesday',
                                                'wednesday',
                                                'thursday',
                                                'friday',
                                                'saturday',
                                                'sunday',
                                            ]
                                            as $day
                                        ) {
                                            $dayReg = $timesheet->entries->sum($day . '_regular');
                                            $dayOT = $timesheet->entries->sum($day . '_overtime');
                                            $grandTotalReg += $dayReg;
                                            $grandTotalOT += $dayOT;
                                            echo "<td class='px-2 py-3 border-r-2 border-gray-700/50 text-center'>{$dayReg}</td>";
                                            echo "<td class='px-2 py-3 border-r-2 border-gray-600/50 text-center'>{$dayOT}</td>";
                                        }
                                    @endphp
                                    <td class="px-2 py-3 border-r-2 border-gray-700/50 text-center">{{ $grandTotalReg }}</td>
                                    <td class="px-2 py-3 border-r-2 border-gray-600/50 text-center">{{ $grandTotalOT }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex justify-between items-center">
                        <a href="{{ auth()->user()->isManager() ? route('timesheet.index') : route('timesheet.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Back
                        </a>
                        <a href="{{ route('timesheet.pdf', $timesheet->id) }}"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700" target="_blank">
                            Download PDF
                        </a>

                        @if (auth()->user()->isManager() && $timesheet->status === 'submitted' && $timesheet->user_id !== auth()->user()->user_id)
                            <div class="space-x-2">
                                <form action="{{ route('timesheet.approve', $timesheet->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                                        onclick="return confirm('Approve this timesheet?')">
                                        Approve
                                    </button>
                                </form>

                                <button type="button" onclick="openRejectModal()"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    Reject
                                </button>
                            </div>
                        @elseif($timesheet->canEdit() && $timesheet->user_id === auth()->id())
                            <a href="{{ route('timesheet.edit', $timesheet->id) }}"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Edit Timesheet
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    @if (auth()->user()->isManager() && $timesheet->status === 'submitted')
        <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Timesheet</h3>
                    <form action="{{ route('timesheet.reject', $timesheet->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rejection Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md" rows="4" required
                                placeholder="Please provide reason for rejection..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Reject Timesheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function openRejectModal() {
                    document.getElementById('rejectModal').classList.remove('hidden');
                }

                function closeRejectModal() {
                    document.getElementById('rejectModal').classList.add('hidden');
                }

                document.getElementById('rejectModal')?.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeRejectModal();
                    }
                });
            </script>
        @endpush
    @endif
</x-layout>
