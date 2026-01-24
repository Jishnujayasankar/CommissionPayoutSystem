<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommissionLevel;

class CommissionLevelController extends Controller
{
    public function index()
    {
        $levels = CommissionLevel::orderBy('int_level')->get();
        return view('commission-levels.index', compact('levels'));
    }

    // Add new commission level
    // NO TRANSACTION NEEDED: Single INSERT operation
    public function store(Request $request)
    {
        $request->validate([
            'int_level' => 'required|integer|min:1|unique:tbl_commission_levels,int_level',
            'dec_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Single INSERT - atomic by default
        CommissionLevel::create([
            'int_level' => $request->int_level,
            'dec_percentage' => $request->dec_percentage,
            'bool_active' => true,
        ]);

        return redirect()->route('commission-levels.index')->with('success', 'Commission level added successfully!');
    }

    // Update commission level
    // Validate: Cannot change percentage if commissions already exist for this level
    public function update(Request $request, $id)
    {
        $level = CommissionLevel::findOrFail($id);
        
        $request->validate([
            'dec_percentage' => 'required|numeric|min:0|max:100',
            'bool_active' => 'required|boolean',
        ]);

        // Check if percentage is being changed
        if ($level->dec_percentage != $request->dec_percentage) {
            // Check if any commissions exist for this level
            $commissionCount = \App\Models\Commission::where('int_level', $level->int_level)->count();
            
            if ($commissionCount > 0) {
                return redirect()->back()->with('error', 
                    "Cannot change percentage for Level {$level->int_level}. "
                    . "{$commissionCount} commission(s) already exist. "
                    . "Deactivate the level instead or create a new level."
                );
            }
        }

        // Single UPDATE - atomic by default
        $level->update([
            'dec_percentage' => $request->dec_percentage,
            'bool_active' => $request->bool_active,
        ]);

        return redirect()->route('commission-levels.index')->with('success', 'Commission level updated successfully!');
    }
}
