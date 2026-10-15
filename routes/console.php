<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Sistem Informasi Perpustakaan SMP Negeri 6 Bandung');
})->purpose('Display an inspiring message');
