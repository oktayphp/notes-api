```markdown
# Notes API — upgraded (Slim + JWT + SQLite)

این نسخهٔ ارتقا یافتهٔ Notes API با ساختار مدرن و آمادهٔ استفاده در نمونه‌کار.

ویژگی‌ها
- PSR-4 autoloading (Composer)
- Slim 4 routing + middleware
- SQLite + PDO (repository pattern)
- JWT-based auth (simple user seed)
- Input validation و error handling استاندارد
- PHPUnit test scaffold
- GitHub Actions برای CI

راه‌اندازی سریع (local)
1. کلون یا کپی این پوشه:
   cd 2_notes-api
2. نصب وابستگی‌ها:
   composer install
3. فایل نمونهٔ env را کپی و مقداردهی کن:
   cp .env.example .env
   (مقدار SECRET_KEY را عوض کن)
4. ایجاد دیتابیس و جدول‌ها:
   php bin/migrate.php
5. یک کاربر تست (seed) بساز:
   php bin/seed_user.php
6. سرور توسعه را اجرا کن:
   php -S localhost:8002 -t public
7. API:
   - POST /auth/login  (body: email, password) -> returns JWT
   - GET /notes (public read)
   - POST /notes (auth required)
   - PUT /notes/{id} (auth required)
   - DELETE /notes/{id} (auth required)

تست‌ها
- اجرای تست‌ها:
  composer test

CI
- یک workflow ساده برای نصب composer و اجرای phpunit اضافه شده است (.github/workflows/ci.yml).

نکات امنیتی
- این یک نمونهٔ آماده‌سازی‌شده برای نمونه‌کار است؛ در production آخرین سخت‌افزارها و سیاست‌های امنیتی، rate-limiting، CORS و hardening لازم است.