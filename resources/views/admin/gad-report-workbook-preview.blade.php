<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SINAG GAD workbook preview</title>
    <style>
        * { box-sizing:border-box; }
        body { margin:0; padding:1.25rem; color:#211936; background:#f7f5fb; font:14px/1.5 Arial,sans-serif; }
        header { margin:0 auto 1rem; max-width:1100px; }
        h1 { margin:0; color:#4a2d96; font-size:1.2rem; }
        header p { margin:.3rem 0 0; color:#6b6379; }
        main { display:grid; gap:.75rem; margin:0 auto; max-width:1100px; }
        details { overflow:hidden; border:1px solid #e3dcef; border-radius:.8rem; background:#fff; }
        summary { padding:.85rem 1rem; color:#4a2d96; font-weight:700; cursor:pointer; }
        .sheet-wrap { max-height:55vh; overflow:auto; border-top:1px solid #e3dcef; }
        table { width:100%; border-collapse:collapse; font-size:.8rem; }
        th,td { min-width:7rem; max-width:24rem; padding:.55rem .65rem; border:1px solid #e7e2ee; text-align:left; vertical-align:top; overflow-wrap:anywhere; white-space:pre-wrap; }
        thead th { position:sticky; top:0; z-index:1; background:#eee8ff; color:#4a2d96; }
        tbody tr:nth-child(even) { background:#faf9fc; }
        @media(max-width:600px) { body { padding:.65rem; } th,td { min-width:6rem; } }
    </style>
</head>
<body>
    <header>
        <h1>SINAG GAD workbook preview</h1>
        <p>{{ $data['period_label'] }} · {{ $data['start_date'] }} – {{ $data['end_date'] }} · {{ count($sheets) }} sheets</p>
        <p>Preview of the workbook sheets and cell contents generated for this export.</p>
    </header>
    <main>
        @foreach($sheets as $sheetName => $rows)
            <details @if($loop->first) open @endif>
                <summary>{{ $sheetName }} · {{ count($rows) }} rows</summary>
                <div class="sheet-wrap">
                    <table>
                        @if(!empty($rows))
                            <thead>
                                <tr>
                                    @foreach($rows[0] as $cell)
                                        <th>{{ is_scalar($cell) || $cell === null ? $cell : json_encode($cell, JSON_UNESCAPED_UNICODE) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_slice($rows, 1) as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td>{{ is_scalar($cell) || $cell === null ? $cell : json_encode($cell, JSON_UNESCAPED_UNICODE) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </details>
        @endforeach
    </main>
</body>
</html>
