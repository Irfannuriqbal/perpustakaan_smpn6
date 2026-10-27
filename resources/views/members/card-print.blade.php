<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cetak Kartu Anggota - Perpustakaan SMP Negeri 6 Bandung</title>
  <style>
    :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; color: #0f172a; }
    * { box-sizing: border-box; }
    body { margin: 0; background: #e2e8f0; }
    .toolbar { align-items: center; background: #fff; border-bottom: 1px solid #cbd5e1; display: flex; justify-content: space-between; padding: 16px 24px; }
    .toolbar h1 { font-size: 18px; margin: 0; }
    .toolbar p { color: #475569; font-size: 13px; margin: 4px 0 0; }
    .actions { display: flex; gap: 8px; }
    .button { background: #1e3a8a; border: 1px solid #1e3a8a; border-radius: 8px; color: white; cursor: pointer; font-size: 13px; font-weight: 700; padding: 10px 14px; text-decoration: none; }
    .button.secondary { background: white; color: #0f172a; border-color: #cbd5e1; }
    main { margin: 32px auto; max-width: 760px; padding: 0 16px; }
    .card { background: #fff; border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 8px 24px #0f172a1f; overflow: hidden; }
    .card-header { align-items: center; background: #1e3a8a; color: white; display: flex; gap: 12px; padding: 16px 20px; }
    .logo { background: white; border-radius: 8px; height: 44px; object-fit: contain; padding: 3px; width: 44px; }
    .school { font-size: 15px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .subtitle { font-size: 11px; margin-top: 3px; opacity: .85; }
    .body { align-items: center; display: grid; gap: 20px; grid-template-columns: 90px 1fr 132px; padding: 22px; }
    .photo, .placeholder { align-items: center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; display: flex; height: 90px; justify-content: center; object-fit: cover; width: 90px; }
    .placeholder { color: #475569; font-size: 30px; font-weight: 800; }
    .name { font-size: 20px; font-weight: 800; margin: 0 0 7px; }
    .type { background: #dbeafe; border-radius: 99px; color: #1e40af; display: inline-block; font-size: 11px; font-weight: 700; padding: 4px 9px; }
    dl { display: grid; gap: 9px 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); margin: 14px 0 0; }
    dt { color: #475569; font-size: 9px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    dd { font-size: 12px; font-weight: 700; margin: 3px 0 0; }
    .qr { align-items: center; border: 1px solid #cbd5e1; border-radius: 12px; display: flex; flex-direction: column; gap: 6px; justify-content: center; padding: 9px; }
    .qr svg { display: block; height: 104px; width: 104px; }
    .qr-label { color: #475569; font-size: 9px; font-weight: 700; margin: 0; text-align: center; }
    @page { margin: 15mm; size: A4; }
    @media print {
      body { background: white; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
      .toolbar { display: none; }
      main { margin: 0; max-width: none; padding: 0; }
      .card { border-color: #111827; border-radius: 4mm; box-shadow: none; page-break-inside: avoid; }
    }
    @media (max-width: 560px) {
      .toolbar { align-items: flex-start; flex-direction: column; gap: 12px; padding: 14px; }
      .body { gap: 12px; grid-template-columns: 68px minmax(0, 1fr); padding: 14px; }
      .photo, .placeholder { height: 68px; width: 68px; }
      .name { font-size: 16px; }
      .qr { grid-column: 1 / -1; justify-self: center; }
    }
  </style>
</head>
<body>
  <header class="toolbar">
    <div>
      <h1>Cetak Kartu Anggota</h1>
      <p>Kartu anggota perpustakaan atas nama {{ $member->name }}.</p>
    </div>
    <div class="actions">
      <a href="{{ route('members.profile') }}" class="button secondary">Kembali ke Profil</a>
      <button type="button" class="button" onclick="window.print()">Cetak Kartu</button>
    </div>
  </header>

  <main>
    <article class="card">
      <div class="card-header">
        <img src="{{ asset('images/logo-smpn-6.jpg') }}" alt="Logo SMP Negeri 6 Bandung" class="logo">
        <div>
          <div class="school">SMP Negeri 6 Bandung</div>
          <div class="subtitle">Kartu Anggota Perpustakaan</div>
        </div>
      </div>
      <div class="body">
        @if ($member->photo_path)
          <img src="{{ asset('storage/'.$member->photo_path) }}" alt="Foto {{ $member->name }}" class="photo">
        @else
          <div class="placeholder" aria-hidden="true">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
        @endif
        <div>
          <h2 class="name">{{ $member->name }}</h2>
          <span class="type">{{ $member->member_type }}</span>
          <dl>
            <div>
              <dt>No. Anggota</dt>
              <dd>{{ $member->no }}</dd>
            </div>
            @if ($member->class)
              <div>
                <dt>Kelas</dt>
                <dd>{{ $member->class }}</dd>
              </div>
            @endif
            <div>
              <dt>Kode Anggota</dt>
              <dd>{{ $member->member_code }}</dd>
            </div>
            <div>
              <dt>Status</dt>
              <dd>{{ ucfirst($member->status) }}</dd>
            </div>
          </dl>
        </div>
        <div class="qr" aria-label="QR Code {{ $member->member_code }}">
          {!! $qrCode !!}
          <p class="qr-label">Scan kode anggota</p>
        </div>
      </div>
    </article>
  </main>
</body>
</html>
