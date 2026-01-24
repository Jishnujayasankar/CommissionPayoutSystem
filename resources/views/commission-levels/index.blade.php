@extends('layout')

@section('title', 'Commission Levels - Commission System')

@section('content')
<div class="card">
    <h2>⚙️ Commission Level Settings</h2>
    <p style="color: #aaa; margin-bottom: 1rem;">Manage commission percentages for each level</p>
    
    <table>
        <thead>
            <tr>
                <th>Level</th>
                <th>Percentage (%)</th>
                <th>Status</th>
                <th>Commissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($levels as $level)
                @php
                    $commissionCount = \App\Models\Commission::where('int_level', $level->int_level)->count();
                    $hasCommissions = $commissionCount > 0;
                @endphp
                <tr>
                    <form action="{{ route('commission-levels.update', $level->pk_bint_level_id) }}" method="POST" style="display: contents;">
                        @csrf
                        @method('PUT')
                        <td><strong>Level {{ $level->int_level }}</strong></td>
                        <td>
                            <input type="number" name="dec_percentage" value="{{ $level->dec_percentage }}" step="0.01" min="0" max="100" 
                                   {{ $hasCommissions ? 'readonly' : '' }}
                                   style="width: 100px; padding: 0.5rem; background: rgba(0,0,0,0.3); border: 1px solid {{ $hasCommissions ? '#888' : '#4da6ff' }}; border-radius: 5px; color: {{ $hasCommissions ? '#888' : '#fff' }};">
                            @if($hasCommissions)
                                <small style="color: #ff6b6b; display: block; margin-top: 0.3rem;">🔒 Locked</small>
                            @endif
                        </td>
                        <td>
                            <select name="bool_active" style="padding: 0.5rem; background: rgba(0,0,0,0.3); border: 1px solid #4da6ff; border-radius: 5px; color: #fff;">
                                <option value="1" {{ $level->bool_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$level->bool_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </td>
                        <td>
                            @if($hasCommissions)
                                <span style="color: #ffa500;">{{ $commissionCount }} records</span>
                            @else
                                <span style="color: #aaa;">None</span>
                            @endif
                        </td>
                        <td>
                            <button type="submit" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Update</button>
                        </td>
                    </form>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h2>➕ Add New Commission Level</h2>
    
    <form action="{{ route('commission-levels.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="int_level">Level Number *</label>
            <input type="number" id="int_level" name="int_level" min="1" required>
        </div>

        <div class="form-group">
            <label for="dec_percentage">Commission Percentage (%) *</label>
            <input type="number" id="dec_percentage" name="dec_percentage" step="0.01" min="0" max="100" required>
        </div>

        <button type="submit" class="btn">Add Level</button>
    </form>
</div>

<div class="card">
    <h2>ℹ️ Information</h2>
    <p style="color: #aaa; line-height: 1.8;">
        • <strong style="color: #4da6ff;">Active levels</strong> will be used for commission calculation<br>
        • <strong style="color: #4da6ff;">Inactive levels</strong> will be skipped during distribution<br>
        • <strong style="color: #ff6b6b;">Locked percentages</strong> cannot be changed (commissions already exist)<br>
        • You can deactivate locked levels or create new levels<br>
        • Changes apply immediately to new sales
    </p>
</div>
@endsection
