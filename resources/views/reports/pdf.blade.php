<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] }}</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      color: #111827;
      font-family: DejaVu Sans, Arial, sans-serif;
      font-size: 10px;
      margin: 0;
    }

    .header {
      border-bottom: 2px solid #111827;
      margin-bottom: 14px;
      padding-bottom: 10px;
      text-align: center;
    }

    .school {
      font-size: 17px;
      font-weight: 700;
      letter-spacing: .03em;
      text-transform: uppercase;
    }

    .system {
      font-size: 12px;
      margin-top: 3px;
    }

    .meta {
      margin: 10px 0 14px;
      width: 100%;
    }

    .meta td {
      padding: 2px 0;
      vertical-align: top;
    }

    .meta .label {
      color: #475569;
      font-weight: 700;
      width: 90px;
    }

    table.data {
      border-collapse: collapse;
      width: 100%;
    }

    table.data th,
    table.data td {
      border: 1px solid #cbd5e1;
      padding: 5px 6px;
      text-align: left;
      vertical-align: top;
    }

    table.data th {
      background: #e2e8f0;
      color: #0f172a;
      font-size: 9px;
      text-transform: uppercase;
    }

    table.data tr:nth-child(even) td {
      background: #f8fafc;
    }

    .empty {
      border: 1px dashed #cbd5e1;
      color: #475569;
      padding: 24px;
      text-align: center;
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="school">SMP Negeri 6 Bandung</div>
    <div class="system">Sistem Informasi Perpustakaan</div>
  </header>

  <table class="meta">
    <tr>
      <td class="label">Jenis Laporan</td>
      <td>: {{ $meta['title'] }}</td>
      <td class="label">Total Data</td>
      <td>: {{ number_format($rows->count(), 0, ',', '.') }} data</td>
    </tr>
    <tr>
      <td class="label">Periode</td>
      <td>: {{ $period }}</td>
      <td class="label">Tanggal Cetak</td>
      <td>: {{ $printedAt }}</td>
    </tr>
  </table>

  @if ($rows->isNotEmpty())
    <table class="data">
      <thead>
        <tr>
          @foreach ($meta['columns'] as $label)
            <th>{{ $label }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($rows as $row)
          <tr>
            @foreach (array_keys($meta['columns']) as $key)
              <td>{{ $row[$key] ?? '-' }}</td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <div class="empty">Data laporan tidak ditemukan.</div>
  @endif
</body>
</html>
