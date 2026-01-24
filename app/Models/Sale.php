<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'tbl_sales';
    protected $primaryKey = 'pk_bint_sale_id';
    public $timestamps = false;
    
    protected $fillable = [
        'fk_bint_user_id',
        'dec_amount',
    ];

    // User who made the sale
    public function user()
    {
        return $this->belongsTo(User::class, 'fk_bint_user_id', 'pk_bint_user_id');
    }

    // Commissions generated from this sale
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'fk_bint_sale_id', 'pk_bint_sale_id');
    }
}
