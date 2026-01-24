<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'tbl_commissions';
    protected $primaryKey = 'pk_bint_commission_id';
    public $timestamps = false;
    
    protected $fillable = [
        'fk_bint_sale_id',
        'fk_bint_user_id',
        'int_level',
        'dec_percentage',
        'dec_amount',
    ];

    // Sale that generated this commission
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'fk_bint_sale_id', 'pk_bint_sale_id');
    }

    // User receiving this commission
    public function user()
    {
        return $this->belongsTo(User::class, 'fk_bint_user_id', 'pk_bint_user_id');
    }
}
