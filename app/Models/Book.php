<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_code',
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'category_id',
        'stock',
        'available_stock',
        'shelf_location',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'available_stock' => 'integer',
            'publication_year' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }
}
