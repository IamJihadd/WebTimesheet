@props([
    'timesheet',
    'disciplines' => collect(),
    'levelGrades' => collect(),
    'projectCodes' => collect(),
    'costCodes' => collect(),
    'tasks' => collect(),
])

@php
    $userLevel = auth()->user()->level_grade ?? '-';
@endphp

<div class="overflow-x-auto">
    <table class="w-full text-sm border-collapse" id="timesheetTable">
        <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-center">
            <tr>
                <th rowspan="3" class="px-2 py-2">Discipline</th>
                <th rowspan="3" class="px-2 py-2">Level Grade</th>
                <th rowspan="3" class="px-2 py-2">Project Code</th>
                <th rowspan="3" class="px-2 py-2">Cost Code</th>
                <th rowspan="3" class="px-2 py-2">Task</th>

                @foreach ($timesheet->getWeekDates() as $date)
                    <th colspan="2" class="px-1 py-1 bg-gray-800/50">{{ $date['formatted'] }}</th>
                @endforeach

                <th rowspan="2" colspan="2" class="px-2 py-2">Total</th>
                <th rowspan="3" class="px-2 py-2">Actions</th>
            </tr>
            <tr>
                @foreach ($timesheet->getWeekDates() as $date)
                    <th colspan="2" class="px-1 py-1">{{ $date['day'] }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($timesheet->getWeekDates() as $date)
                    <th class="px-1 py-1 border-r-2 border-gray-700/50 bg-gray-800">R</th>
                    <th class="px-1 py-1 border-r-2 border-gray-600/50 bg-gray-800">OT</th>
                @endforeach
                <th class="px-1 py-2 border-r-2 border-gray-700/50 bg-gray-800">R</th>
                <th class="px-1 py-2 border-r-2 border-gray-600/50 bg-gray-800">OT</th>
            </tr>
        </thead>
        <tbody id="entriesContainer" class="text-white">
            @forelse($timesheet->entries as $index => $entry)
                <tr class="entry-row" data-index="{{ $index }}">
                    <td class="px-2 py-2 border-b-2 border-gray-600">
                        <select name="entries[{{ $index }}][discipline]"
                            class="w-full px-2 py-1 bg-gray-800 border-none rounded discipline-select" required>
                            <option value="">Select...</option>
                            @foreach ($disciplines as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $entry->discipline == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-2 py-2 border-b-2 border-gray-600">
                        <input type="text" value="{{ $entry->level_grade ?? $userLevel }}"
                            class="w-full bg-gray-800 rounded shadow-sm border-none cursor-not-allowed" readonly>

                        <input type="hidden" name="entries[{{ $index }}][level_grade]"
                            value="{{ $entry->level_grade ?? $userLevel }}">
                    </td>
                    <td class="px-2 py-2 border-b-2 border-gray-600">
                        <select name="entries[{{ $index }}][project_code]"
                            class="w-full px-2 py-1 bg-gray-800 border-none rounded" required>
                            <option value="">Select...</option>
                            @foreach ($projectCodes as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $entry->project_code == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    {{-- KOLOM COST CODE (Loop Utama) --}}
                    <td class="px-2 py-2 border-b-2 border-gray-600">
                        {{-- Tambahkan class 'cost-code-select' dan 'pointer-events-none' (biar read-only visual) --}}
                        <select name="entries[{{ $index }}][cost_code]"
                            class="w-full px-2 py-1 bg-gray-800 border-none rounded shadow-sm cost-code-select pointer-events-none"
                            tabindex="-1"> {{-- tabindex -1 supaya tidak bisa difokuskan lewat tab --}}
                            <option value="">Select Cost Code</option>
                            @foreach ($costCodes as $costCode)
                                {{-- Perhatikan: Value harus sama persis dengan kode depan Task --}}
                                <option value="{{ $costCode->code }}"
                                    {{ $entry->cost_code == $costCode->code ? 'selected' : '' }}>
                                    {{ $costCode->code }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- KOLOM TASK (Loop Utama) --}}
                    <td class="px-2 py-2 border-b-2 border-gray-600">
                        {{-- Tambahkan class 'task-select' --}}
                        <select name="entries[{{ $index }}][task]"
                            class="w-full px-2 py-1 bg-gray-800 border-none rounded shadow-sm task-select">
                            <option value="">Select Task</option>
                            @foreach ($tasks as $task)
                                <option value="{{ $task->name }}" data-discipline="{{ $task->discipline }}"
                                    {{ $entry->task == $task->name ? 'selected' : '' }}>
                                    {{ $task->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        @php
                            $isWeekend = in_array($day, ['saturday', 'sunday']);
                            // Jika weekend, input regular jadi readonly & abu-abu
                            $readOnlyClass = $isWeekend ? 'bg-gray-100 cursor-not-allowed text-gray-400' : 'bg-gray-800';
                            $readOnlyAttr = $isWeekend ? 'readonly tabindex="-1"' : '';
                            // Nilai regular di weekend dipaksa 0
                            $regularValue = $isWeekend ? 0 : (float) $entry->{$day . '_regular'};
                        @endphp
                        <td class="px-1 py-2 border-b-2 border-gray-600">
                            <input type="number" name="entries[{{ $index }}][{{ $day }}_regular]"
                                value="{{ (float) $entry->{$day . '_regular'} }}"
                                class="w-8 px-1 py-1 bg-gray-800 border-none rounded text-center hour-input {{ $readOnlyClass }}"
                                min="0" max="8" step="0.5" data-type="regular"
                                data-day="{{ $day }}" {!! $readOnlyAttr !!}>
                        </td>
                        <td class="px-1 py-2 border-b-2 border-gray-600">
                            <input type="number" name="entries[{{ $index }}][{{ $day }}_overtime]"
                                value="{{ (float) $entry->{$day . '_overtime'} }}"
                                class="w-8 px-1 py-1 bg-gray-800 border-none rounded text-center hour-input"
                                min="0" max="4" step="0.5" data-type="overtime"
                                data-day="{{ $day }}">
                        </td>
                    @endforeach

                    <td class="px-2 py-2 border-b-2 border-gray-600 text-center font-semibold total-regular">
                        {{ $entry->total_regular }}
                    </td>
                    <td class="px-2 py-2 border-b-2 border-gray-600 text-center font-semibold total-overtime">
                        {{ $entry->total_overtime }}
                    </td>
                    <td class="px-2 py-2 border-b-2 border-gray-600 text-center">
                        <button type="button" class="text-red-600 hover:text-red-800 remove-row text-xs">
                            Remove
                        </button>
                    </td>
                </tr>
            @empty
                <tr class="entry-row border-b-2 border-gray-700/50" data-index="0">
                    <td class="px-2 py-2 ">
                        <select name="entries[0][discipline]"
                            class="w-full px-2 py-1 border-none rounded bg-gray-800 discipline-select " required>
                            <option value="">Select...</option>
                            @foreach ($disciplines as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-2 py-2 ">
                        <input type="text" name="entries[0][level_grade]" value="{{ $userLevel }}"
                            class="w-full bg-gray-800 border-none rounded shadow-sm cursor-not-allowed" readonly>

                        {{-- FIX: Ganti {{ $index }} menjadi 0 (Hardcode untuk baris baru) --}}
                        <input type="hidden" name="entries[0][level_grade]" value="{{ $userLevel }}">
                    </td>
                    <td class="px-2 py-2 ">
                        <select name="entries[0][project_code]"
                            class="w-full px-2 py-1 border-none rounded bg-gray-800" required>
                            <option value="">Select...</option>
                            @foreach ($projectCodes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td class="px-2 py-2">
                        <select name="entries[0][cost_code]"
                            class="w-full border-gray-300 px-2 py-1 border-none rounded bg-gray-800 cost-code-select pointer-events-none"
                            tabindex="-1">
                            <option value="">Select Cost Code</option>
                            @foreach ($costCodes as $costCode)
                                <option value="{{ $costCode->code }}">{{ $costCode->code }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td class="px-2 py-2">
                        <select name="entries[0][task]"
                            class="w-full px-2 py-1 border-none rounded bg-gray-800 shadow-sm task-select">
                            <option value="">Select Task</option>
                            @foreach ($tasks as $task)
                                <option value="{{ $task->name }}" data-discipline="{{ $task->discipline }}">
                                    {{ $task->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>


                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        @php
                            $isWeekend = in_array($day, ['saturday', 'sunday']);
                            $readOnlyClass = $isWeekend ? 'bg-gray-800 cursor-not-allowed text-gray-400' : 'bg-gray-800';
                            $readOnlyAttr = $isWeekend ? 'readonly tabindex="-1"' : '';
                        @endphp
                        <td class="px-1 py-2 border-r-2 border-gray-700/80">
                            <input type="number" name="entries[0][{{ $day }}_regular]"
                                class="w-8 px-1 py-1 border-none rounded bg-gray-800 text-center hour-input {{ $readOnlyClass }}"
                                min="0" max="8" step="0.5" data-type="regular"
                                data-day="{{ $day }}" value="0" 
                                {!! $readOnlyAttr !!}>
                        </td>
                        <td class="px-1 py-2 border-r-2 border-gray-600/50">
                            <input type="number" name="entries[0][{{ $day }}_overtime]"
                                class="w-8 px-1 py-1 border-none rounded bg-gray-800 text-center hour-input"
                                min="0" max="4" step="0.5" data-type="overtime"
                                data-day="{{ $day }}" value="0">
                        </td>
                    @endforeach

                    <td class="w-8 px-4 py-3 border-r-2 border-gray-600/50 text-center font-semibold total-regular">0
                    </td>
                    <td class="w-8 px-4 py-3 text-center font-semibold total-overtime">0</td>
                    <td class="w-8 px-4 py-3 text-center">
                        <button type="button" class="text-red-600 hover:text-red-800 remove-row text-xs">
                            Remove
                        </button>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="my-4">
        <button type="button" id="addRowBtn"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + Add Task Row
        </button>
    </div>
</div>
