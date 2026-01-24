@extends('layout')

@section('title', 'Add User - Commission System')

@section('content')
<div class="card">
    <h2>➕ Add New User</h2>
    <p style="color: #aaa; margin-bottom: 1.5rem;">Create a new user under an existing parent</p>
    
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="vchr_name">User Name *</label>
            <input type="text" id="vchr_name" name="vchr_name" value="{{ old('vchr_name') }}" required>
        </div>

        <div class="form-group">
            <label for="vchr_email">Email Address *</label>
            <input type="email" id="vchr_email" name="vchr_email" value="{{ old('vchr_email') }}" required>
        </div>

        <div class="form-group">
            <label for="fk_bint_parent_id">Select Parent User *</label>
            <select id="fk_bint_parent_id" name="fk_bint_parent_id" required>
                <option value="">-- Choose Parent --</option>
                @foreach($users as $user)
                    <option value="{{ $user->pk_bint_user_id }}" {{ old('fk_bint_parent_id') == $user->pk_bint_user_id ? 'selected' : '' }}>
                        {{ $user->vchr_name }} ({{ $user->vchr_email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="dec_sale_amount">Sale Amount (Optional)</label>
            <input type="number" id="dec_sale_amount" name="dec_sale_amount" step="0.01" min="0" value="{{ old('dec_sale_amount') }}" placeholder="0.00">
            <small style="color: #aaa; display: block; margin-top: 0.5rem;">
                If provided, a sale will be recorded and commissions distributed automatically
            </small>
        </div>

        <button type="submit" class="btn">Create User & Process Sale</button>
    </form>
</div>

<div class="card">
    <h2>📋 Existing Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->pk_bint_user_id }}</td>
                    <td>{{ $user->vchr_name }}</td>
                    <td>{{ $user->vchr_email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
