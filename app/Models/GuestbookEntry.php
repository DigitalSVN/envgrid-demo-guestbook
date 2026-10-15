<?php

namespace App\Models;

use Database\Factories\GuestbookEntryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestbookEntry extends Model
{
    /** @use HasFactory<GuestbookEntryFactory> */
    use HasFactory, SoftDeletes;

    public const MAX_DISPLAYED = 50;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'message',
    ];

    /**
     * Limit to the newest entries so the page stays small however many people sign.
     *
     * @param  Builder<GuestbookEntry>  $query
     */
    public function scopeRecent(Builder $query): void
    {
        $query->latest()->latest('id')->limit(self::MAX_DISPLAYED);
    }
}
