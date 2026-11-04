# SeekSaveAnd — Local Laravel Setup

SeekSaveAnd is a Laravel 12 lost-and-found application for MCC. This package is configured to use Laravel's local SQLite database by default, so you do not need to set up MySQL or PostgreSQL.

## Requirements

- Windows 10/11
- PHP 8.2 or newer
- PHP extensions `pdo_sqlite`, `sqlite3`, `fileinfo`, `openssl`, `mbstring`, `curl`, `xml`, and `dom`
- Composer (only needed if the included `vendor` directory is missing)

## Quick start (Windows)

1. Extract the whole ZIP to a normal writable folder (do not run it from inside the ZIP).
2. Make sure PHP is installed and `php` works in Command Prompt. If you use XAMPP, add its `php` folder to your Windows `PATH`.
3. Double-click **`run-local.bat`**.
4. It clears Laravel's cached configuration, runs database migrations, and starts the server. It attempts to open Chrome at `http://127.0.0.1:8000/login`.
5. Keep the command window open while using the app. Press **Ctrl+C** to stop the server.

If Chrome does not open automatically, open `http://127.0.0.1:8000/login` manually.

## Local demo sign-in

The local `.env` enables demo sign-in buttons only while `APP_ENV=local`. Select a demo user on the login page to explore the application without a Microsoft account. The demo accounts and sample lost/found posts are for local testing only.

The sample database is `database/database.sqlite`. Back it up before resetting or replacing it. To rebuild the sample data manually, run:

```bat
php artisan migrate:fresh --seed
```

**Warning:** `migrate:fresh --seed` deletes all current database tables and data before recreating them. Do not use it if you need to preserve your own records.

## Microsoft school sign-in (`@mcc.edu.ph`)

The app's Microsoft sign-in requires an app registration in Microsoft Entra ID. The code cannot create the school's app registration or change MCC's tenant security policies.

To configure Microsoft sign-in:

1. Ask MCC IT/the Microsoft 365 administrator to confirm that student accounts are allowed to use the app and to provide or create an Entra ID app registration.
2. Add this exact **Web redirect URI** to that registration: `http://localhost:8000/auth/microsoft/callback` (or use `http://127.0.0.1:8000/auth/microsoft/callback` consistently if that is the redirect URI you configure).
3. Put the app registration values in `.env`:
   - `AZURE_CLIENT_ID=...`
   - `AZURE_CLIENT_SECRET=...`
   - `AZURE_TENANT_ID=...` (MCC tenant ID, if supplied; `common` is the default)
   - `AZURE_REDIRECT_URI=http://localhost:8000/auth/microsoft/callback`
   - `MICROSOFT_ALLOWED_DOMAIN=mcc.edu.ph`
4. Run `php artisan optimize:clear` and restart the server.

The application checks the Microsoft sign-in identity against `@mcc.edu.ph`. Use the exact same host (`localhost` or `127.0.0.1`) in both `APP_URL`/the redirect URI and the browser to avoid callback URI mismatches.

### About the Authenticator app / MFA prompt

If Microsoft says you must set up or use the Microsoft Authenticator app, that is a Microsoft 365 account security requirement, not an application bug. This project cannot bypass MFA, enroll an authenticator on your behalf, or reset your school account's authentication methods. Follow Microsoft's on-screen setup, or contact MCC IT/help desk if you no longer have access to the registered method or the enrollment flow is stuck. Local demo sign-in remains available for testing the app itself.

## Photo uploads

Item photo uploads use Laravel's local `public` storage automatically when `SUPABASE_URL` and `SUPABASE_SERVICE_KEY` are blank. The launcher creates Laravel's `public/storage` link if it is missing. If you later configure Supabase Storage, set those two values and `SUPABASE_BUCKET`.

## Useful commands

```bat
php artisan route:list
php artisan migrate:status
php artisan optimize:clear
php artisan serve
```

## Troubleshooting

- **`could not find driver` / SQLite errors:** enable `pdo_sqlite` and `sqlite3` in your PHP installation. The PHP CLI used by Command Prompt must have them enabled; enabling them only for Apache is not enough.
- **`php is not recognized`:** add your PHP folder to the Windows `PATH`, then reopen Command Prompt.
- **Port 8000 is busy:** stop the other server or edit the port in `run-local.bat` and update the Microsoft redirect URI to match.
- **Microsoft `AADSTS50011`:** the redirect URI in Entra must match the URI used by the app exactly.
- **Microsoft requests Authenticator:** follow the MFA enrollment prompt or ask MCC IT to reset the account's authentication methods; do not try to bypass the policy.
