<?php

namespace App\Services;

use App\Models\User;
use App\Models\Sale;
use App\Models\Commission;
use App\Models\CommissionLevel;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Get active commission rates from database
     * NO TRANSACTION NEEDED: Single SELECT query (read-only)
     */
    private function getCommissionRates()
    {
        return CommissionLevel::where('bool_active', true)
            ->orderBy('int_level')
            ->pluck('dec_percentage', 'int_level')
            ->toArray();
    }

    /**
     * Process sale and distribute commissions dynamically
     * 
     * TRANSACTION REQUIRED: Multiple related operations must be atomic
     * Operations:
     * 1. INSERT into tbl_sales (1 record)
     * 2. INSERT into tbl_commissions (up to N records, where N = active levels)
     * 
     * Why transaction is needed:
     * - If sale is created but commission creation fails, we have orphaned sale
     * - If some commissions are created but others fail, we have partial distribution
     * - All-or-nothing: Either complete sale + all commissions, or nothing
     * 
     * Example scenario without transaction:
     * - Sale of $1000 created
     * - Level 1 commission ($100) created
     * - Level 2 commission fails (network/DB error)
     * - Result: Sale exists, only 1 commission exists (data inconsistency)
     */
    public function processSaleWithCommissions($userId, $saleAmount)
    {
        if ($saleAmount <= 0) {
            throw new \InvalidArgumentException('Sale amount must be greater than 0');
        }

        DB::beginTransaction();
        
        try {
            $sale = Sale::create([
                'fk_bint_user_id' => $userId,
                'dec_amount' => $saleAmount,
            ]);

            $commissionRates = $this->getCommissionRates();
            $currentUser = User::find($userId);
            $level = 1;

            while ($currentUser && $currentUser->fk_bint_parent_id && isset($commissionRates[$level])) {
                $parentUser = User::find($currentUser->fk_bint_parent_id);
                
                if ($parentUser) {
                    $percentage = $commissionRates[$level];
                    $commissionAmount = ($saleAmount * $percentage) / 100;

                    Commission::create([
                        'fk_bint_sale_id' => $sale->pk_bint_sale_id,
                        'fk_bint_user_id' => $parentUser->pk_bint_user_id,
                        'int_level' => $level,
                        'dec_percentage' => $percentage,
                        'dec_amount' => $commissionAmount,
                    ]);

                    $currentUser = $parentUser;
                    $level++;
                } else {
                    break;
                }
            }

            DB::commit();
            
            return [
                'success' => true,
                'sale_id' => $sale->pk_bint_sale_id,
                'levels_processed' => $level - 1,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get total commission earned by a user
     * NO TRANSACTION NEEDED: Single SELECT with SUM (read-only)
     */
    public function getTotalCommission($userId)
    {
        return Commission::where('fk_bint_user_id', $userId)
            ->sum('dec_amount');
    }

    /**
     * Update sale amount and recalculate commissions
     * 
     * TRANSACTION REQUIRED: Multiple dependent operations
     * Operations:
     * 1. DELETE from tbl_commissions (N records)
     * 2. UPDATE tbl_sales (1 record)
     * 3. INSERT into tbl_commissions (up to N new records)
     * 
     * Why transaction is needed:
     * - If we delete old commissions but fail to create new ones, users lose earnings
     * - If we update sale but fail to recalculate, commission data is stale
     * - All operations must succeed together or rollback together
     * 
     * Example scenario without transaction:
     * - Old commissions deleted (users had $210 total)
     * - Sale updated from $1000 to $500
     * - New commission creation fails
     * - Result: Sale is $500 but no commissions exist (users lost $210)
     */
    public function updateSaleAndRecalculate($saleId, $newAmount)
    {

        if ($newAmount < 0) {
            return ['success' => false, 'error' => 'Sale amount cannot be negative'];
        }

        DB::beginTransaction();
        
        try {
            $sale = Sale::findOrFail($saleId);
            Commission::where('fk_bint_sale_id', $saleId)->delete();
            $sale->update(['dec_amount' => $newAmount]);
            
            if ($newAmount > 0) {
                $commissionRates = $this->getCommissionRates();
                $currentUser = User::find($sale->fk_bint_user_id);
                $level = 1;

                while ($currentUser && $currentUser->fk_bint_parent_id && isset($commissionRates[$level])) {
                    $parentUser = User::find($currentUser->fk_bint_parent_id);
                    
                    if ($parentUser) {
                        $percentage = $commissionRates[$level];
                        $commissionAmount = ($newAmount * $percentage) / 100;

                        Commission::create([
                            'fk_bint_sale_id' => $sale->pk_bint_sale_id,
                            'fk_bint_user_id' => $parentUser->pk_bint_user_id,
                            'int_level' => $level,
                            'dec_percentage' => $percentage,
                            'dec_amount' => $commissionAmount,
                        ]);

                        $currentUser = $parentUser;
                        $level++;
                    } else {
                        break;
                    }
                }
            }
            
            DB::commit();
            return ['success' => true];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
