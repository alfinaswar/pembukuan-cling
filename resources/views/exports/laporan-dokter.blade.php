<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Calibri, Arial, sans-serif;
            font-size: 11pt;
            margin: 0;
            padding: 10px;
            color: #000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }

        th {
            background-color: #f9f9f9;
            font-weight: bold;
            text-align: center;
            font-size: 10pt;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        /* Optional: match exact column proportions from image */
        .col-day {
            width: 8%;
        }

        .col-date {
            width: 10%;
        }

        .col-branch {
            width: 10%;
        }

        .col-no {
            width: 5%;
        }

        .col-patient {
            width: 15%;
        }

        .col-treat {
            width: 32%;
        }

        .col-revenue {
            width: 20%;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th class="center col-day">DAY</th>
                <th class="center col-date">DATE</th>
                <th class="center col-branch">BRANCH</th>
                <th class="center col-no">NO.</th>
                <th class="center col-patient">PATIENT NAME</th>
                <th class="center col-treat">TREATMENT(S)</th>
                <th class="center col-revenue">REVENUE</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $index => $trx)
                @php
                    $details = $trx->TransaksiDetail ?? collect();
                    $rowCount = $details->count() > 0 ? $details->count() : 1;
                    $day = \Carbon\Carbon::parse($trx->Tanggal)->translatedFormat('l');
                    $date = \Carbon\Carbon::parse($trx->Tanggal)->format('j M Y');
                    $branch = $trx->getCabang?->Nama ?? ($trx->KodeCabang ?? '-');
                @endphp

                @forelse ($details as $dIdx => $detail)
                    <tr>
                        @if ($dIdx == 0)
                            <td class="center" rowspan="{{ $rowCount }}">{{ $day }}</td>
                            <td class="center" rowspan="{{ $rowCount }}">{{ $date }}</td>
                            <td class="center" rowspan="{{ $rowCount }}">{{ $branch }}</td>
                            <td class="center" rowspan="{{ $rowCount }}">{{ $index + 1 }}</td>
                            <td class="center" rowspan="{{ $rowCount }}">{{ $trx->NamaPasien }}</td>
                        @endif
                        <td class="left">{{ $detail->MasterJenisPerawatan?->Nama ?? '' }}</td>
                        <td class="right">{{ number_format($detail->Biaya ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="center">{{ $day }}</td>
                        <td class="center">{{ $date }}</td>
                        <td class="center">{{ $branch }}</td>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $trx->NamaPasien }}</td>
                        <td class="left"></td>
                        <td class="right">{{ number_format($trx->TotalBayar ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforelse
            @endforeach
        </tbody>
    </table>
</body>

</html>
