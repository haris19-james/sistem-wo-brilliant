# 🚀 QUICK SETUP - COPY PASTE COMMANDS

## Step 1: Run Migration (Setup Database)

```bash
cd c:\laragon\www\sistem-wo-brilliant2
php artisan migrate
```

## Step 2: Clear Cache

```bash
php artisan config:cache
php artisan view:clear
php artisan route:clear
```

## Step 3: Optional - Optimize (Production only)

```bash
composer dump-autoload -o
php artisan optimize
```

## Step 4: Access Application

Open in browser:
```
http://localhost/lapangan/tugas
```

Or create new task:
```
http://localhost/lapangan/tugas/create
```

---

## ✅ VERIFICATION

After setup, check these:

1. ✅ Dashboard loads without errors
   ```
   http://localhost/lapangan/dashboard
   ```

2. ✅ Tugas menu muncul di sidebar (untuk role lapangan)

3. ✅ Form buka tanpa errors
   ```
   http://localhost/lapangan/tugas/create
   ```

4. ✅ Checklist buttons work (add/remove)

5. ✅ Priority selection works

6. ✅ Category dropdown shows icons

7. ✅ Form submits successfully

---

## 🔧 TROUBLESHOOTING

### If routes not found:
```bash
php artisan route:clear
php artisan config:cache
```

### If table not found:
```bash
php artisan migrate:fresh
```

### If styling broken:
```bash
npm run build
```

### If form not submitting:
Check browser console (F12 → Console tab)

---

## 📁 FILES TO VERIFY

Setelah setup, pastikan files ini ada:

✅ `app/Models/Tugas.php`
✅ `app/Http/Controllers/Lapangan/TugasController.php`
✅ `app/Policies/TugasPolicy.php`
✅ `resources/views/lapangan/modules/tugas_form.blade.php`
✅ `resources/views/lapangan/modules/tugas.blade.php`
✅ `database/migrations/*create_tugas_table.php`

---

## 📊 TESTING DATA

Login as lapangan user and:

1. Go to `/lapangan/tugas`
2. Click "Tambah Tugas"
3. Fill form:
   - Nama: "Setup Dekorasi"
   - Acara: Pick any acara
   - Kategori: "Dekorasi"
   - Prioritas: "High"
   - Deadline: Today + 09:00
   - PIC: Select user
   - Checklist: Add items
   - Catatan: Type something
4. Click "Simpan Tugas"
5. Should redirect to list and see success message

---

## 🆘 IF SOMETHING BREAKS

### Reset Database
```bash
php artisan migrate:fresh
php artisan migrate
```

### Clear All Cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

### Full Restart
```bash
php artisan optimize:clear
composer dump-autoload -o
php artisan config:cache
```

---

## 📞 COMMON ERRORS

**Error: "Class 'App\Models\Tugas' not found"**
→ Check if file exists: `app/Models/Tugas.php`
→ Run: `php artisan config:cache`

**Error: "Route [lapangan.tugas.index] not defined"**
→ Check if route is in `routes/web.php`
→ Run: `php artisan route:clear`

**Error: "SQLSTATE[42S02]: Table 'tugas' doesn't exist"**
→ Run: `php artisan migrate`
→ Check database connection in `.env`

**Error: "Policy missing"**
→ Check if file exists: `app/Policies/TugasPolicy.php`
→ Register in: `app/Providers/AuthServiceProvider.php`

**Form not submitting**
→ Press F12 in browser
→ Check Console tab for JS errors
→ Check Network tab for request status

---

## 💡 TIPS

1. Always run `php artisan config:cache` after any routing changes
2. Use `php artisan tinker` to test database queries
3. Check `storage/logs/laravel.log` for backend errors
4. Use Chrome DevTools (F12) for frontend debugging
5. Test in incognito mode if cache issues persist

---

## 🎯 VERIFICATION CHECKLIST

- [ ] Migrate database successful
- [ ] Cache cleared
- [ ] Tugas menu appears in sidebar
- [ ] Form page loads
- [ ] Form fields visible
- [ ] Buttons work (Tambah checklist, etc)
- [ ] Form submits successfully
- [ ] Task appears in list
- [ ] Can edit task
- [ ] Can delete task
- [ ] Responsive on mobile
- [ ] No JS errors in console

---

## 📞 CONTACT

If you encounter issues:
1. Check the documentation files
2. Review TEST_GUIDE.md
3. Check browser console (F12)
4. Check server logs

---

**Setup Time:** ~2 minutes
**Status:** Ready for Testing ✅
