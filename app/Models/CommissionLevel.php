<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionLevel extends Model
{
    use HasFactory;

    protected $table = 'tbl_commission_levels';
    protected $primaryKey = 'pk_bint_level_id';
    public $timestamps = false;
    
    protected $fillable = [
        'int_level',
        'dec_percentage',
        'bool_active',
    ];
}
