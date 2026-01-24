# TESTING GUIDE - 5-Level Affiliate Commission System

## Prerequisites
- Database created: `commissionpayoutsystem`
- Migrations run: `php artisan migrate`
- Root user seeded: `php artisan db:seed --class=RootUserSeeder`
- Server running: `php artisan serve`

---

## TEST CASE 1: Basic 3-Level Hierarchy

### Setup
1. Navigate to: http://localhost:8000/users/create
2. Create users in order:

**User 1:**
- Name: Alice
- Email: alice@test.com
- Parent: Root Admin
- Sale Amount: (leave empty)

**User 2:**
- Name: Bob
- Email: bob@test.com
- Parent: Alice
- Sale Amount: (leave empty)

**User 3:**
- Name: Charlie
- Email: charlie@test.com
- Parent: Bob
- Sale Amount: $500.00

### Expected Results
Go to Dashboard and verify:

| User | Total Commission |
|------|------------------|
| Root Admin | $0.00 |
| Alice | $25.00 (5% of $500) |
| Bob | $50.00 (10% of $500) |
| Charlie | $0.00 |

**Calculation:**
- Charlie makes $500 sale
- Bob (Level 1 parent): $500 × 10% = $50.00
- Alice (Level 2 parent): $500 × 5% = $25.00
- Root Admin (Level 3 parent): $500 × 3% = $15.00

---

## TEST CASE 2: Full 5-Level Hierarchy

### Setup
Create this hierarchy:

```
Root Admin (ID: 1)
└── Level1 User (ID: 2)
    └── Level2 User (ID: 3)
        └── Level3 User (ID: 4)
            └── Level4 User (ID: 5)
                └── Level5 User (ID: 6)
                    └── Seller (ID: 7, Sale: $1000)
```

### Create Users:
1. **Level1 User** → Parent: Root Admin
2. **Level2 User** → Parent: Level1 User
3. **Level3 User** → Parent: Level2 User
4. **Level4 User** → Parent: Level3 User
5. **Level5 User** → Parent: Level4 User
6. **Seller** → Parent: Level5 User, Sale: $1000.00

### Expected Results

| User | Level | Commission Rate | Amount |
|------|-------|----------------|---------|
| Level5 User | 1 | 10% | $100.00 |
| Level4 User | 2 | 5% | $50.00 |
| Level3 User | 3 | 3% | $30.00 |
| Level2 User | 4 | 2% | $20.00 |
| Level1 User | 5 | 1% | $10.00 |
| Root Admin | 6 | 0% | $0.00 ✓ (Beyond level 5) |
| Seller | - | - | $0.00 |

**Total Distributed:** $210.00 (21% of $1000)

---

## TEST CASE 3: Multiple Sales

### Setup
Using existing hierarchy from Test Case 1:

1. Create **David** → Parent: Charlie, Sale: $200.00
2. Create **Eve** → Parent: Alice, Sale: $300.00

### Expected Results After All Sales

**Alice's Total Commission:**
- From Charlie's $500 sale: $25.00 (Level 2)
- From David's $200 sale: $6.00 (Level 3)
- From Eve's $300 sale: $0.00 (She's the seller's parent, Level 1 = $30)
- **Total: $61.00**

**Bob's Total Commission:**
- From Charlie's $500 sale: $50.00 (Level 1)
- From David's $200 sale: $20.00 (Level 2)
- **Total: $70.00**

**Charlie's Total Commission:**
- From David's $200 sale: $20.00 (Level 1)
- **Total: $20.00**

---

## TEST CASE 4: Transaction Rollback (Manual Test)

### Purpose
Verify that if commission calculation fails, the sale is also rolled back.

### Test Method
1. Temporarily modify `CommissionService.php` to throw an exception after creating sale
2. Try to create a user with a sale
3. Check database - sale should NOT be created
4. Restore original code

---

## VERIFICATION QUERIES

### Check All Commissions for a Sale
```sql
SELECT 
    c.int_level,
    u.vchr_name,
    c.dec_percentage,
    c.dec_amount
FROM tbl_commissions c
JOIN tbl_users u ON c.fk_bint_user_id = u.pk_bint_user_id
WHERE c.fk_bint_sale_id = 1
ORDER BY c.int_level;
```

### Check User's Commission Breakdown
```sql
SELECT 
    int_level,
    COUNT(*) as times_earned,
    SUM(dec_amount) as total
FROM tbl_commissions
WHERE fk_bint_user_id = 2
GROUP BY int_level;
```

### Verify No Level 6+ Commissions Exist
```sql
SELECT COUNT(*) as invalid_commissions
FROM tbl_commissions
WHERE int_level > 5;
-- Should return 0
```

---

## EDGE CASES TO TEST

### Edge Case 1: Root User Makes Sale
- Create user under Root Admin with sale
- Root Admin should receive $0 (no parent)

### Edge Case 2: Shallow Hierarchy
- Create user directly under Root with $100 sale
- Only Root should get commission: $10 (10%)

### Edge Case 3: Duplicate Email
- Try creating user with existing email
- Should show validation error

### Edge Case 4: Negative Sale Amount
- Try entering negative sale amount
- Should be prevented by validation

### Edge Case 5: Zero Sale Amount
- Enter $0.00 as sale amount
- Should create user but no sale/commissions

---

## PERFORMANCE TEST

### Create 100 Users in Deep Hierarchy
```php
// Run in tinker: php artisan tinker

$parentId = 1; // Root
for ($i = 1; $i <= 100; $i++) {
    $user = App\Models\User::create([
        'vchr_name' => "User $i",
        'vchr_email' => "user$i@test.com",
        'fk_bint_parent_id' => $parentId
    ]);
    $parentId = $user->pk_bint_user_id;
}

// Make sale by last user
$service = new App\Services\CommissionService();
$result = $service->processSaleWithCommissions($parentId, 1000);

// Check: Only 5 commissions should be created
echo "Levels processed: " . $result['levels_processed']; // Should be 5
```

---

## SUCCESS CRITERIA

✅ All commissions calculated correctly
✅ Only 5 levels receive commission
✅ Level 6+ receive nothing
✅ Transaction rollback works
✅ Dashboard shows correct totals
✅ No SQL injection possible
✅ Validation prevents invalid data
✅ UI displays properly in black/blue theme

---

## TROUBLESHOOTING

**Issue:** Commissions not showing on dashboard
- Check: `SELECT * FROM tbl_commissions;`
- Verify sale was created: `SELECT * FROM tbl_sales;`

**Issue:** Wrong commission amounts
- Verify commission rates in `CommissionService.php`
- Check calculation: amount × percentage ÷ 100

**Issue:** More than 5 levels getting commission
- Check while loop condition in `CommissionService.php`
- Should stop at `$level <= 5`

---

## FINAL VERIFICATION CHECKLIST

- [ ] Root user exists in database
- [ ] Can create users via web interface
- [ ] Can record sales with user creation
- [ ] Dashboard displays all users
- [ ] Commission totals are accurate
- [ ] No level 6+ commissions exist
- [ ] UI is black and blue themed
- [ ] No authentication required
- [ ] Forms have CSRF protection
- [ ] Email validation works
- [ ] Parent dropdown shows all users
- [ ] Success messages display correctly
