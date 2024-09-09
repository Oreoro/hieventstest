<?php

namespace HiEvents\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Order extends BaseModel
{
    protected $keyType = 'string';
    public $incrementing = false;

    // If your ID column in the database is not named 'id', specify it here:
    // protected $primaryKey = 'your_id_column_name';

    public function question_and_answer_views(): HasMany
    {
        return $this->hasMany(QuestionAndAnswerView::class);
    }

    public function stripe_payment(): HasOne
    {
        return $this->hasOne(StripePayment::class);
    }

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function getCastMap(): array
    {
        return [
            'total_before_additions' => 'float',
            'total_tax' => 'float',
            'total_gross' => 'float',
            'total_discount' => 'float',
            'total_fee' => 'float',
            'total_refunded' => 'float',
            'point_in_time_data' => 'array',
            'address' => 'array',
            'taxes_and_fees_rollup' => 'array',
        ];
    }

    protected function getFillableFields(): array
    {
        return [];
    }

    public function getRouteKeyName()
    {
        return 'short_id'; // Replace 'short_id' with the actual column name for your order identifier
    }
}
