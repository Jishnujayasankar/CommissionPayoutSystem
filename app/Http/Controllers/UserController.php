<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Commission;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    // Show form to add user
    public function create()
    {
        $users = User::orderBy('vchr_name')->get();
        return view('users.create', compact('users'));
    }

    // Store new user and process sale if provided
    // TRANSACTION NEEDED: User creation + sale processing must be atomic
    // If sale fails, user should still be created (handled by service)
    public function store(Request $request)
    {
        $request->validate([
            'vchr_name' => 'required|string|max:100',
            'vchr_email' => 'required|email|unique:tbl_users,vchr_email',
            'fk_bint_parent_id' => 'required|exists:tbl_users,pk_bint_user_id',
            'dec_sale_amount' => 'nullable|numeric|min:0',
        ]);

        // Single INSERT - No transaction needed (atomic by default)
        $user = User::create([
            'vchr_name' => $request->vchr_name,
            'vchr_email' => $request->vchr_email,
            'fk_bint_parent_id' => $request->fk_bint_parent_id,
        ]);

        // Sale processing has its own transaction in service
        if ($request->dec_sale_amount && $request->dec_sale_amount > 0) {
            $result = $this->commissionService->processSaleWithCommissions(
                $user->pk_bint_user_id,
                $request->dec_sale_amount
            );

            if ($result['success']) {
                return redirect()->route('dashboard')
                    ->with('success', 'User created and sale processed successfully!');
            } else {
                return redirect()->route('dashboard')
                    ->with('error', 'User created but sale processing failed: ' . $result['error']);
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'User created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $users = User::where('pk_bint_user_id', '!=', $id)->orderBy('vchr_name')->get();
        $sales = $user->sales;
        return view('users.edit', compact('user', 'users', 'sales'));
    }

    // Update user and sales
    // TRANSACTION NEEDED: Multiple sales updates must be atomic
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'vchr_name' => 'required|string|max:100',
            'vchr_email' => 'required|email|unique:tbl_users,vchr_email,' . $id . ',pk_bint_user_id',
            'fk_bint_parent_id' => 'nullable|exists:tbl_users,pk_bint_user_id',
            'sales.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Update user details (single UPDATE)
            $user->update([
                'vchr_name' => $request->vchr_name,
                'vchr_email' => $request->vchr_email,
                'fk_bint_parent_id' => $request->fk_bint_parent_id,
            ]);

            // Update multiple sales - each has DELETE + UPDATE + multiple INSERTs
            // If one fails, all should rollback
            if ($request->has('sales')) {
                foreach ($request->sales as $saleId => $amount) {
                    $result = $this->commissionService->updateSaleAndRecalculate($saleId, $amount);
                    if (!$result['success']) {
                        throw new \Exception($result['error']);
                    }
                }
            }

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'User and sales updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    // Delete user
    // NO TRANSACTION NEEDED: Single DELETE with CASCADE
    // Foreign keys automatically delete related sales and commissions
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->delete(); // Single DELETE - atomic by default
    
            DB::commit();
            return redirect()->route('dashboard')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard')->with('error', 'User deletion failed: ' . $e->getMessage());
        }

    }

    // Update sale amount and recalculate commissions
    public function updateSale(Request $request, $saleId)
    {
        $request->validate([
            'dec_amount' => 'required|numeric|min:0',
        ]);

        $result = $this->commissionService->updateSaleAndRecalculate($saleId, $request->dec_amount);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Sale updated and commissions recalculated!');
        }

        return redirect()->back()->with('error', $result['error']);
    }
}
