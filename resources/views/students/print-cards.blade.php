<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cetak Kartu Anggota - Perpustakaan SMPN 6 Bandung</title>
  <style>
    :root {
      color-scheme: light;
      --ink: #0f172a;
      --muted: #475569;
      --line: #cbd5e1;
      --school: #1e3a8a;
      --paper: #ffffff;
      --soft: #f8fafc;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      background: #e2e8f0;
      color: var(--ink);
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
    }

    .toolbar {
      align-items: center;
      background: var(--paper);
      border-bottom: 1px solid var(--line);
      display: flex;
      gap: 12px;
      justify-content: space-between;
      padding: 16px 24px;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .toolbar h1 {
      font-size: 18px;
      margin: 0;
    }

    .toolbar p {
      color: var(--muted);
      margin: 4px 0 0;
    }

    .toolbar-actions {
      display: flex;
      gap: 8px;
    }

    .button {
      background: var(--school);
      border: 1px solid var(--school);
      border-radius: 8px;
      color: white;
      cursor: pointer;
      font-weight: 700;
      padding: 10px 14px;
      text-decoration: none;
    }

    .button.secondary {
      background: white;
      color: var(--ink);
      border-color: var(--line);
    }

    .page {
      margin: 24px auto;
      max-width: 1020px;
      padding: 0 16px 32px;
    }

    .cards {
      display: grid;
      gap: 16px;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    }

    .card {
      background: var(--paper);
      border: 1px solid var(--line);
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
      min-height: 204px;
      overflow: hidden;
      page-break-inside: avoid;
    }

    .card-header {
      background: var(--school);
      color: white;
      padding: 12px 16px;
    }

    .school-name {
      font-size: 14px;
      font-weight: 800;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    .card-title {
      font-size: 10px;
      letter-spacing: 0.08em;
      margin-top: 3px;
      opacity: 0.85;
      text-transform: uppercase;
    }

    .card-body {
      display: grid;
      gap: 12px;
      grid-template-columns: 68px 1fr 110px;
      padding: 14px 16px 16px;
    }

    .student-photo,
    .student-photo-placeholder {
      border: 1px solid var(--line);
      border-radius: 10px;
      height: 68px;
      object-fit: cover;
      width: 68px;
    }

    .student-photo-placeholder {
      align-items: center;
      background: var(--soft);
      color: var(--muted);
      display: flex;
      font-size: 24px;
      font-weight: 800;
      justify-content: center;
    }

    .student-name {
      font-size: 16px;
      font-weight: 800;
      line-height: 1.25;
      margin: 0 0 12px;
    }

    .meta {
      display: grid;
      gap: 8px;
    }

    .meta dt {
      color: var(--muted);
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.06em;
      margin: 0;
      text-transform: uppercase;
    }

    .meta dd {
      font-size: 13px;
      font-weight: 700;
      margin: 2px 0 0;
    }

    .qr-box {
      align-items: center;
      background: white;
      border: 1px solid var(--line);
      border-radius: 12px;
      display: flex;
      height: 110px;
      justify-content: center;
      padding: 8px;
      width: 110px;
    }

    .qr-box svg {
      display: block;
      height: 94px;
      width: 94px;
    }

    .member-code {
      background: var(--soft);
      border: 1px solid var(--line);
      border-radius: 10px;
      display: inline-block;
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 0.04em;
      margin-top: 12px;
      padding: 6px 10px;
    }

    .empty {
      background: var(--paper);
      border: 1px dashed var(--line);
      border-radius: 16px;
      color: var(--muted);
      padding: 48px 24px;
      text-align: center;
    }

    @page {
      margin: 12mm;
      size: A4;
    }

    @media print {
      body {
        background: white;
      }

      .toolbar {
        display: none;
      }

      .page {
        margin: 0;
        max-width: none;
        padding: 0;
      }

      .cards {
        gap: 8mm;
        grid-template-columns: repeat(2, 86mm);
      }

      .card {
        border-color: #111827;
        border-radius: 4mm;
        box-shadow: none;
        min-height: 54mm;
      }
    }
  </style>
</head>
<body>
  <header class="toolbar">
    <div>
      <h1>Cetak Kartu Anggota</h1>
      <p>{{ $students->count() }} kartu siap dicetak. QR Code hanya berisi member_code.</p>
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('students.index') }}" class="button secondary">Kembali</a>
      <button type="button" class="button" onclick="window.print()">Cetak</button>
    </div>
  </header>

  <main class="page">
    @if ($students->isNotEmpty())
      <section class="cards" aria-label="Daftar kartu anggota">
        @foreach ($students as $student)
          <article class="card">
            <div class="card-header">
              <div class="school-name">SMP Negeri 6 Bandung</div>
              <div class="card-title">Kartu Anggota Perpustakaan</div>
            </div>
            <div class="card-body">
              @if ($student->photo_path)
                <img src="{{ asset('storage/'.$student->photo_path) }}" alt="Foto {{ $student->name }}" class="student-photo">
              @else
                <div class="student-photo-placeholder" aria-label="Foto tidak tersedia">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
              @endif
              <div>
                <p class="student-name">{{ $student->name }}</p>
                <dl class="meta">
                  <div>
                    <dt>NIS</dt>
                    <dd>{{ $student->nis }}</dd>
                  </div>
                  <div>
                    <dt>Kelas</dt>
                    <dd>{{ $student->class }}</dd>
                  </div>
                </dl>
                <div class="member-code">{{ $student->member_code }}</div>
              </div>
              <div class="qr-box" aria-label="QR Code {{ $student->member_code }}">
                {!! $qrCodes[$student->id] !!}
              </div>
            </div>
          </article>
        @endforeach
      </section>
    @else
      <section class="empty">
        <strong>Tidak ada siswa untuk dicetak.</strong>
        <p>Ubah filter data siswa, lalu buka halaman cetak lagi.</p>
      </section>
    @endif
  </main>
</body>
</html>
