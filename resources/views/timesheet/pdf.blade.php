<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Timesheet {{ $timesheet->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 20px;
        }

        .header {
            position: relative;
            /* KUNCI: Agar elemen absolute di dalamnya terkunci di sini */
            text-align: center;
            margin-bottom: 20px;
            height: 60px;
            /* Menjaga tinggi header agar tidak kolaps */
            width: 100%;
        }

        /* Wadah untuk Logo */
        .header-logo-container {
            position: absolute;
            /* Lepas dari aliran teks, tempel ke pojok */
            top: 0;
            left: 0;
        }

        /* Mengatur ukuran gambar agar tidak raksasa */
        .header-logo-container img {
            height: 50px;
            /* Paksa tinggi gambar jadi 50px */
            width: auto;
            /* Lebar menyesuaikan proporsi */
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .info-section {
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 3px 5px;
            background-color: #f0f0f0;
            border: 1px solid #000;
        }

        .info-value {
            display: table-cell;
            padding: 3px 5px;
            border: 1px solid #000;
            border-left: none;
        }

        .timesheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }

        .timesheet-table th,
        .timesheet-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
        }

        .timesheet-table th {
            background-color: #DADADA;
            font-weight: bold;
        }

        .timesheet-table .label-col {
            text-align: left;
            padding-left: 5px;
            min-width: 80px;
        }

        .timesheet-table .total-row {
            background-color: #ECECEC;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 40px;
        }

        .signature-line {
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            margin-top: 20px;
            font-style: italic;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }

        .status-submitted {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }
    </style>
</head>

<body>
    @php
        // Ambil file gambar
        $path = public_path('img/logonama_DEC.png');
        // Ubah jadi data biner
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        // Ubah jadi base64
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    @endphp

    <!-- Header -->
    <div class="header">
        <div class="header-logo-container">
            <img src="{{ $base64 }}" alt="Your Company" />
        </div>
        <div class="company-name">PT. DARYA ELANG CARAKA</div>
        <div class="title">WEEKLY TIMESHEET</div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">WBS</div>
            <div class="info-value">Master</div>{{-- untuk memanggil WBS nya apa --}}
            <div class="info-label" style="width: 120px;">Cut-Off Date</div>
            <div class="info-value" style="width: 150px;">{{ $timesheet->week_end->format('d M Y') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">DEPARTMENT</div>
            <div class="info-value">{{ $timesheet->user->department ?? 'Engineering' }}</div>
            <div class="info-label" style="width: 120px;">Week No</div>
            <div class="info-value" style="width: 150px;">{{ $timesheet->week_start->format('Y-W') }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">NAME</div>
            <div class="info-value">{{ $timesheet->user->name }}</div>
            <div class="info-label" style="width: 120px;">Status</div>
            <div class="info-value" style="width: 150px;">
                @if ($timesheet->status === 'submitted')
                    <span class="status-badge status-submitted">SUBMITTED</span>
                @elseif($timesheet->status === 'approved')
                    <span class="status-badge status-approved">APPROVED</span>
                @elseif($timesheet->status === 'rejected')
                    <span class="status-badge status-rejected">REJECTED</span>
                @else
                    <span class="status-badge status-draft">DRAFT</span>
                @endif
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">LOCATION</div>
            <div class="info-value">{{ $timesheet->user->lokasi_kerja ?? '-' }}</div>
            <div class="info-label" style="width: 120px;"></div>
            <div class="info-value" style="width: 150px;"></div>
        </div>
    </div>

    <!-- Timesheet Table -->
    <table class="timesheet-table">
        <thead>
            <tr>
                <th rowspan="3" style="min-width: 60px; font-size: 10px">Discipline</th>
                <th rowspan="3" style="min-width: 60px; font-size: 10px">Level Grade</th>
                <th rowspan="3" style="min-width: 80px; font-size: 10px">Project Code</th>
                <th rowspan="3" style="min-width: 50px; font-size: 10px">Cost Code</th>
                <th rowspan="3" style="min-width: 80px; font-size: 10px">Task</th>
                @foreach ($timesheet->getWeekDates() as $date)
                    <th colspan="2">{{ $date['date']->format('d M Y') }}</th>
                @endforeach
                <th rowspan="2" colspan="2">Summary<br>TOTAL<br>MH</th>
            </tr>
            <tr>
                @foreach ($timesheet->getWeekDates() as $date)
                    <th colspan="2">{{ $date['day_full'] }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($timesheet->getWeekDates() as $date)
                    <th style="width: 25px;">R</th>
                    <th style="width: 25px;">OT</th>
                @endforeach
                <th style="width: 30px;">R</th>
                <th style="width: 30px;">OT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timesheet->entries as $entry)
                <tr>
                    <td class="label-col">{{ $entry->discipline }}</td>
                    <td class="label-col">{{ $entry->level_grade }}</td>
                    <td class="label-col">{{ $entry->project_code }}</td>
                    <td class="label-col">{{ $entry->cost_code }}</td>
                    <td class="label-col">{{ $entry->task }}</td>

                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <td>{{ $entry->{$day . '_regular'} > 0 ? $entry->{$day . '_regular'} : '' }}</td>
                        <td>{{ $entry->{$day . '_overtime'} > 0 ? $entry->{$day . '_overtime'} : '' }}</td>
                    @endforeach

                    <td style="font-weight: bold;">{{ $entry->total_regular > 0 ? $entry->total_regular : '' }}</td>
                    <td style="font-weight: bold;">{{ $entry->total_overtime > 0 ? $entry->total_overtime : '' }}</td>
                </tr>
            @empty
                @for ($i = 0; $i < 8; $i++)
                    <tr>
                        <td class="label-col">&nbsp;</td>
                        <td class="label-col">&nbsp;</td>
                        <td class="label-col">&nbsp;</td>
                        <td class="label-col">&nbsp;</td>
                        <td class="label-col">&nbsp;</td>
                        @for ($j = 0; $j < 14; $j++)
                            <td>&nbsp;</td>
                        @endfor
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            @endforelse

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="5" style="text-align: right; padding-right: 10px;">TOTAL MAN HOUR</td>
                @php
                    $grandTotalReg = 0;
                    $grandTotalOT = 0;
                @endphp
                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                    @php
                        $dayReg = $timesheet->entries->sum($day . '_regular');
                        $dayOT = $timesheet->entries->sum($day . '_overtime');
                        $grandTotalReg += $dayReg;
                        $grandTotalOT += $dayOT;
                    @endphp
                    <td>{{ $dayReg > 0 ? $dayReg : '' }}</td>
                    <td>{{ $dayOT > 0 ? $dayOT : '' }}</td>
                @endforeach
                <td>{{ $grandTotalReg > 0 ? $grandTotalReg : '' }}</td>
                <td>{{ $grandTotalOT > 0 ? $grandTotalOT : '' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">PREPARED BY</div>
            <div class="signature-line">Name : {{ $timesheet->user->name }}</div>
            <div class="signature-line">Date :
                {{ $timesheet->submitted_at ? $timesheet->submitted_at->format('d M Y') : '' }}</div>
            <br>
            <br>
            <div class="signature-line">Signature : ________________________</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">APPROVED BY</div>
            <div class="signature-line">Name : {{ $timesheet->approver ? $timesheet->approver->name : '' }}</div>
            <div class="signature-line">Date :
                {{ $timesheet->approved_at ? $timesheet->approved_at->format('d M Y') : '' }}</div>
            <br>
            <br>
            <div class="signature-line">Signature : ________________________</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        This document produced by PT. Darya Elang Caraka's System and not to be published
    </div>
</body>

</html>
