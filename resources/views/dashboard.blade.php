@extends('layout')

@section('title', 'Dashboard - Commission System')

@section('content')
<div class="card">
    <h2>📊 Commission Dashboard</h2>
    <p style="color: #aaa; margin-bottom: 1rem;">View all users and their total commission earnings</p>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Parent</th>
                <th>Total Commission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->vchr_name }}</td>
                    <td>{{ $user->vchr_email }}</td>
                    <td>{{ $user->parent_name }}</td>
                    <td style="color: #00ff00; font-weight: bold;">
                        ₹{{ number_format($user->total_commission, 2) }}
                    </td>
                    <td>
                        @if($user->fk_bint_parent_id !== null)
                            <a href="{{ route('users.edit', $user->pk_bint_user_id) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Edit</a>
                        @else
                            <span style="color: #aaa; font-size: 0.9rem;">Root User</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #aaa;">
                        No users found. Add your first user!
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card">
    <h2>ℹ️ System Information</h2>
    <p style="color: #aaa; line-height: 1.8;">
        <strong style="color: #4da6ff;">Commission Structure:</strong><br>
        • Level 1 (Direct Parent): 10%<br>
        • Level 2: 5%<br>
        • Level 3: 3%<br>
        • Level 4: 2%<br>
        • Level 5: 1%<br><br>
        <strong style="color: #4da6ff;">Note:</strong> Commissions are distributed up to 5 levels in the hierarchy.
    </p>
</div>
@endsection
