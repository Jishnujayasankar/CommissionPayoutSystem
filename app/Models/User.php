<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'tbl_users';
    protected $primaryKey = 'pk_bint_user_id';
    public $timestamps = false;
    
    protected $fillable = [
        'vchr_name',
        'vchr_email',
        'fk_bint_parent_id',
    ];

    // Self-referencing relationship: parent user
    public function parent()
    {
        return $this->belongsTo(User::class, 'fk_bint_parent_id', 'pk_bint_user_id');
    }

    // Self-referencing relationship: child users
    public function children()
    {
        return $this->hasMany(User::class, 'fk_bint_parent_id', 'pk_bint_user_id');
    }

    // Sales made by this user
    public function sales()
    {
        return $this->hasMany(Sale::class, 'fk_bint_user_id', 'pk_bint_user_id');
    }

    // Commissions earned by this user
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'fk_bint_user_id', 'pk_bint_user_id');
    }
}
