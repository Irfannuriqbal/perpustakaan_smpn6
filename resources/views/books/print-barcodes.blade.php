<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cetak Label Barcode - Perpustakaan SMPN 6 Bandung</title>
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
      border-color: var(--line);
      color: var(--ink);
    }

    .page {
      margin: 24px auto;
      max-width: 1040px;
      padding: 0 16px 32px;
    }

    .labels {
      display: grid;
      gap: 14px;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }

    .label {
      background: var(--paper);
      border: 1px solid var(--line);
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
      padding: 12px;
      page-break-inside: avoid;
      text-align: center;
    }

    .school-name {
      color: var(--school);
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .book-title {
      font-size: 12px;
      font-weight: 700;
      line-height: 1.25;
      margin: 6px 0 8px;
      min-height: 30px;
    }

    .barcode-box {
      align-items: center;
      background: white;
      display: flex;
      justify-content: center;
      margin: 0 auto;
      min-height: 78px;
      padding: 8px 12px;
    }

    .barcode-box svg {
      display: block;
      height: 72px;
      max-width: 100%;
      width: 210px;
    }

    .book-code {
      background: var(--soft);
      border: 1px solid var(--line);
      border-radius: 999px;
      display: inline-block;
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 0.08em;
      margin-top: 8px;
      padding: 5px 12px;
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
      margin: 10mm;
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

      .labels {
        gap: 5mm;
        grid-template-columns: repeat(3, 61mm);
      }

      .label {
        border-color: #111827;
        border-radius: 3mm;
        box-shadow: none;
        min-height: 34mm;
        padding: 3mm;
      }

      .barcode-box {
        padding: 1mm 2mm;
      }
    }
  </style>
</head>
<body>
  <header class="toolbar">
    <div>
      <h1>Cetak Label Barcode Buku</h1>
      <p>{{ $books->count() }} label siap dicetak. Barcode berisi book_code dan mendukung scanner USB.</p>
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('books.index') }}" class="button secondary">Kembali</a>
      <button type="button" class="button" onclick="window.print()">Cetak</button>
    </div>
  </header>

  <main class="page">
    @if ($books->isNotEmpty())
      <section class="labels" aria-label="Daftar label barcode buku">
        @foreach ($books as $book)
          <article class="label">
            <div class="school-name">SMP Negeri 6 Bandung</div>
            <div class="book-title">{{ $book->title }}</div>
            <div class="barcode-box" aria-label="Barcode {{ $book->book_code }}">
              {!! $barcodes[$book->id] !!}
            </div>
            <div class="book-code">{{ $book->book_code }}</div>
          </article>
        @endforeach
      </section>
    @else
      <section class="empty">
        <strong>Tidak ada buku untuk dicetak.</strong>
        <p>Ubah filter data buku, lalu buka halaman cetak lagi.</p>
      </section>
    @endif
  </main>
</body>
</html>
