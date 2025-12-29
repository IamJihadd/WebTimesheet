<x-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Timesheet Approval - Manager') }}
            </h2>
            <a href="{{ route('timesheet.create') }}"
                class="px-4 py-0 bg-indigo-600 text-white rounded-md max-sm:py-1 max-sm:px-2 hover:bg-indigo-700">
                New Timesheet
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-sm:py-1">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-10">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($timesheets->isEmpty())
                        <p class="text-center text-gray-500">No timesheets pending approval.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3">Employee</th>
                                        <th class="px-6 py-3">Week Period</th>
                                        <th class="px-6 py-3">Week No.</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Total Hours</th>
                                        <th class="px-6 py-3">Submitted At</th>
                                        <th class="px-6 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timesheets as $timesheet)
                                        <tr class="border-b dark:border-gray-700">
                                            <td class="px-6 py-4 font-medium">
                                                {{ $timesheet->user ? $timesheet->user->name : 'Unknown User' }}
                                                <br>
                                                <span
                                                    class="text-xs text-gray-400">{{ $timesheet->user ? $timesheet->user->user_id : '-' }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $timesheet->week_start->format('d M Y') }} -
                                                {{ $timesheet->week_end->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py4">
                                                {{ $timesheet->week_start->format('Y-W') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($timesheet->status === 'submitted')
                                                    <span
                                                        class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                        Pending
                                                    </span>
                                                @elseif($timesheet->status === 'approved')
                                                    <span
                                                        class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($timesheet->status === 'rejected')
                                                    <span
                                                        class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                        Rejected
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
                                            <td
                                                class="px-6 py-4 flex flex-col space-y-2 justify-start sm:justify-center">
                                                <a href="{{ route('timesheet.show', $timesheet->id) }}"
                                                    class="px-3 py-1 bg-blue-600 text-center text-xs rounded text-white hover:bg-blue-700">
                                                    View Details
                                                </a>
                                                {{-- LOGIKA 1: Manager melihat Timesheet BAWAHAN --}}
                                                @if (auth()->user()->isManager() &&
                                                        $timesheet->status === 'submitted' &&
                                                        $timesheet->user_id !== auth()->user()->user_id)
                                                    {{-- bukan timesheet milik sendiri --}}

                                                    <!-- Approve Action (Form Terpisah) -->
                                                    <form action="{{ route('timesheet.approve', $timesheet->id) }}"
                                                        method="POST"
                                                        class="inline text-center bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                        @csrf
                                                        <button type="submit"
                                                            onclick="return confirm('Apakah Anda yakin ingin menyetujui timesheet ini?')"
                                                            class="px-3 py-1">
                                                            Approve
                                                        </button>
                                                    </form>

                                                    <!-- Reject Button -->
                                                    <button type="button"
                                                        onclick="openRejectModal('{{ $timesheet->id }}')"
                                                        class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                        Reject
                                                    </button>

                                                    {{-- LOGIKA 2: Manager melihat Timesheet DIRI SENDIRI --}}
                                                @elseif(auth()->user()->isManager() && $timesheet->user_id === auth()->user()->user_id)
                                                    @if ($timesheet->status === 'submitted')
                                                        <span
                                                            class=" mt-2 text-center py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                                            Waiting Superior
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $timesheets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-sm:w-80">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Timesheet</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Rejection Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea name="rejection_reason" class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md"
                            rows="4" required placeholder="Please provide reason for rejection..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-gray-700 rounded-md hover:bg-red-700">
                            Reject Timesheet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openRejectModal(timesheetId) {
                const modal = document.getElementById('rejectModal');
                const form = document.getElementById('rejectForm');
                form.action = `/timesheet/${timesheetId}/reject`;
                modal.classList.remove('hidden');
            }

            function closeRejectModal() {
                const modal = document.getElementById('rejectModal');
                modal.classList.add('hidden');
            }

            // Close modal when clicking outside
            document.getElementById('rejectModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRejectModal();
                }
            });
        </script>
    @endpush
</x-layout>
