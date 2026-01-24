<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    // Show dashboard with all users and their total commissions
    public function index()
    {
        // Get all users with their total commissions
        $users = User::leftJoin('tbl_commissions', 'tbl_users.pk_bint_user_id', '=', 'tbl_commissions.fk_bint_user_id')
            ->select(
                'tbl_users.pk_bint_user_id',
                'tbl_users.vchr_name',
                'tbl_users.vchr_email',
                'tbl_users.fk_bint_parent_id',
                DB::raw('COALESCE(SUM(tbl_commissions.dec_amount), 0) as total_commission')
            )
            ->groupBy('tbl_users.pk_bint_user_id', 'tbl_users.vchr_name', 'tbl_users.vchr_email', 'tbl_users.fk_bint_parent_id')
            ->orderByRaw('CASE WHEN tbl_users.fk_bint_parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('tbl_users.pk_bint_user_id', 'asc')
            ->get();

        // Get parent names
        foreach ($users as $user) {
            if ($user->fk_bint_parent_id) {
                $parent = User::find($user->fk_bint_parent_id);
                $user->parent_name = $parent ? $parent->vchr_name : 'N/A';
            } else {
                $user->parent_name = 'Root';
            }
        }

        return view('dashboard', compact('users'));
    }
}
