# Arvi Restaurant Day Night Cafe

A Laravel 12 Day Night Cafe menu for PHP 8.2 or newer. Customers can order without an account. Admins manage categories, products, orders, and one UPI payment QR code.

The site supports English and Marathi interface text, a day/night theme, a compact mobile menu, and horizontal product browsing on phones. Admins can enter Marathi names and descriptions alongside English menu data; when a Marathi field is empty, the English value is shown.

## Local setup

1. Install PHP 8.2+, Composer, and either SQLite or MySQL. Enable PHP extensions required by Laravel, including `pdo_sqlite` or `pdo_mysql` and `fileinfo`. Enable `gd` for the test suite.
2. Run `composer install` in this folder.
3. Copy `.env.example` to `.env` and run `php artisan key:generate`.
4. For SQLite, set `DB_CONNECTION=sqlite` and create `database/database.sqlite`. For MySQL, create a database and set `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.
5. Run `php artisan migrate --seed` and `php artisan storage:link`.
6. Run `php artisan serve` and open `http://127.0.0.1:8000`.

Create the **first** admin account at `/admin/create.php`. After that account exists, the setup page closes. Log in at `/admin/login`. Replace demo menu items in the dashboard. Add your logo as `public/logo.png` and upload product photos through Admin.

## UPI payments

Open **Admin → UPI QR** and upload a clear, tightly cropped QR image. Enter the UPI ID and payee name **that match the image**. Only one QR can be active; delete it before uploading a replacement. No payment image is bundled with this repository.

When a customer selects UPI, the menu shows the QR. After ordering, the confirmation page shows the final amount and a standard `upi://pay` link. On supported phones, it opens the installed UPI app chooser, including apps such as GPay or PhonePe. The customer can also scan the QR from another device. A static QR may not include the exact order amount, so the customer should check the displayed total. The site does **not** verify the bank transfer; all customer orders start **unpaid** until an admin changes their payment status. UPI invoices display the QR below the total.

Paid UPI invoices omit the payment QR to avoid asking the customer to pay twice.

## Customer order history

On the customer menu, entering a 10 digit mobile number reveals **View history**. The history page lists orders, dates, times, items, amounts, and payment status for that exact number, including an empty state. No customer account or SMS verification is used, so anyone who knows a mobile number could look up its orders. Add phone verification before using this with sensitive customer data.

## Move to another PC

Copy this project, the `.env` file, uploaded files in `storage/app/public`, and your database. Keep the same `APP_KEY` when moving an existing installation. For SQLite, copy `database/database.sqlite`. For MySQL, export with `mysqldump` and import into the new MySQL database. On the destination PC, run `composer install`, `php artisan migrate`, and `php artisan storage:link`. The `database/dine_in_mysql.sql` file is only an empty menu schema reference; a normal installation should use Laravel migrations.

Run automated checks with `php artisan test`. Before public hosting, use HTTPS, set `APP_DEBUG=false`, and protect your `.env` file.
