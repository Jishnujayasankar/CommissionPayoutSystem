@extends('layout')

@section('title', 'Edit User - Commission System')

@section('content')
<form action="{{ route('users.update', $user->pk_bint_user_id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card">
        <h2>✏️ Edit User</h2>
        
        <div class="form-group">
            <label for="vchr_name">User Name *</label>
            <input type="text" id="vchr_name" name="vchr_name" value="{{ old('vchr_name', $user->vchr_name) }}" required>
        </div>

        <div class="form-group">
            <label for="vchr_email">Email Address *</label>
            <input type="email" id="vchr_email" name="vchr_email" value="{{ old('vchr_email', $user->vchr_email) }}" required>
        </div>

        <div class="form-group">
            <label for="fk_bint_parent_id">Parent User</label>
            <select id="fk_bint_parent_id" name="fk_bint_parent_id">
                <option value="">-- No Parent (Root) --</option>
                @foreach($users as $u)
                    <option value="{{ $u->pk_bint_user_id }}" {{ $user->fk_bint_parent_id == $u->pk_bint_user_id ? 'selected' : '' }}>
                        {{ $u->vchr_name }} ({{ $u->vchr_email }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card">
        <h2>💰 Sales History</h2>
        
        @if($sales->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                        <tr>
                            <td>{{ $sale->pk_bint_sale_id }}</td>
                            <td>
                                <input type="number" name="sales[{{ $sale->pk_bint_sale_id }}]" value="{{ $sale->dec_amount }}" step="0.01" min="0" 
                                       style="width: 120px; padding: 0.5rem; background: rgba(0,0,0,0.3); border: 1px solid #4da6ff; border-radius: 5px; color: #fff;">
                            </td>
                            <td>{{ $sale->tim_created_at }}</td>
                            <td>
                                <span style="color: #aaa; font-size: 0.9rem;">Commissions auto-recalculated</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #aaa; text-align: center; padding: 2rem;">No sales recorded for this user.</p>
        @endif
    </div>

    <div class="card">
        <button type="submit" class="btn">Update User & Sales</button>
        <a href="{{ route('dashboard') }}" class="btn" style="background: #555; margin-left: 1rem;">Cancel</a>
    </div>
</form>

<div class="card">
    <h2>🗑️ Delete User</h2>
    <p style="color: #aaa; margin-bottom: 1rem;">Warning: This will delete the user and all associated sales and commissions.</p>
    
    <form action="{{ route('users.destroy', $user->pk_bint_user_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn" style="background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);">Delete User</button>
    </form>
</div>
@endsection
