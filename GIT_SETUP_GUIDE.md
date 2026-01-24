# GitHub Repository Setup - Step by Step

## Step 1: Create GitHub Repository

1. Go to https://github.com
2. Click "+" icon → "New repository"
3. Repository name: `CommissionPayoutSystem`
4. Description: `Laravel-based 5-level affiliate commission system with dynamic payout distribution`
5. Choose: Public or Private
6. **DO NOT** initialize with README, .gitignore, or license
7. Click "Create repository"

## Step 2: Initialize Local Git Repository

Open terminal in project directory and run:

```bash
cd "c:\Users\JISHNU JAYASANKAR\commissionpayoutsystem"
git init
```

## Step 3: Configure Git (if not already done)

```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

## Step 4: Add All Files

```bash
git add .
```

## Step 5: Create Initial Commit

```bash
git commit -m "Initial commit: 5-level affiliate commission payout system

Features:
- Self-referencing user hierarchy
- Dynamic commission levels (database-driven)
- Automatic commission distribution up to 5 levels
- Transaction-safe operations (BEGIN, COMMIT, ROLLBACK)
- User CRUD with sale management
- Commission level management with validation
- Black & blue gradient UI
- Comprehensive documentation

Tech Stack: Laravel 10.x, PHP 8.x, MySQL 8.x"
```

## Step 6: Rename Main Branch (if needed)

```bash
git branch -M main
```

## Step 7: Add Remote Repository

Replace `YOUR_USERNAME` with your actual GitHub username:

```bash
git remote add origin https://github.com/YOUR_USERNAME/CommissionPayoutSystem.git
```

## Step 8: Push to GitHub

```bash
git push -u origin main
```

If prompted for credentials:
- Username: Your GitHub username
- Password: Use Personal Access Token (not your password)

### To create Personal Access Token:
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token
3. Select scopes: `repo` (full control)
4. Copy token and use as password

## Step 9: Verify

Go to: `https://github.com/YOUR_USERNAME/CommissionPayoutSystem`

You should see all your files!

---

## Quick Command Summary (Copy & Paste)

```bash
# Navigate to project
cd "c:\Users\JISHNU JAYASANKAR\commissionpayoutsystem"

# Initialize Git
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit: 5-level affiliate commission payout system"

# Rename branch
git branch -M main

# Add remote (replace YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/CommissionPayoutSystem.git

# Push
git push -u origin main
```

---

## Troubleshooting

### Error: "remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/CommissionPayoutSystem.git
```

### Error: "failed to push some refs"
```bash
git pull origin main --allow-unrelated-histories
git push -u origin main
```

### Error: "Authentication failed"
- Use Personal Access Token instead of password
- Or use SSH key authentication

---

## After Pushing

### Update README on GitHub
1. Go to repository on GitHub
2. Rename `README_GITHUB.md` to `README.md` (or update existing README.md)
3. Commit changes

### Add Topics/Tags
1. Go to repository
2. Click "⚙️" next to "About"
3. Add topics: `laravel`, `php`, `mysql`, `affiliate-system`, `commission`, `payout`

### Create Releases (Optional)
1. Go to "Releases" → "Create a new release"
2. Tag: `v1.0.0`
3. Title: `Initial Release - 5-Level Commission System`
4. Description: List features
5. Publish release

---

## Repository Structure on GitHub

```
CommissionPayoutSystem/
├── README.md (main documentation)
├── SYSTEM_DOCUMENTATION.md
├── SETUP.md
├── TESTING.md
├── TRANSACTION_GUIDE.md
├── IMPLEMENTATION_SUMMARY.md
├── app/
├── database/
├── resources/
├── routes/
├── .gitignore
├── composer.json
└── ...
```

---

## Clone Command for Others

After pushing, others can clone with:

```bash
git clone https://github.com/YOUR_USERNAME/CommissionPayoutSystem.git
cd CommissionPayoutSystem
composer install
cp .env.example .env
php artisan key:generate
# Follow SETUP.md for database setup
```

---

**Done! Your code is now on GitHub! 🎉**
