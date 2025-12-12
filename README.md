# DonorHub - Online Donation Platform

DonorHub is a web-based platform that connects donors and recipients for various types of donations including blood donations, education support, health campaigns, and food donations.

## Features

- **User Registration & Authentication**: Separate registration for donors and recipients
- **Campaign Management**: Create and manage donation campaigns
- **Blood Donation Support**: Specialized features for blood donation campaigns with blood group matching
- **Donation Tracking**: Track donations and campaign progress
- **Email Notifications**: Automated email notifications for donations
- **Admin Panel**: Comprehensive admin dashboard for managing campaigns and users
- **Profile Management**: User profile management with image uploads

## Technology Stack

- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5
- **Backend**: PHP
- **Database**: MySQL
- **Server**: XAMPP (Apache, MySQL, PHP)

## Installation

### Prerequisites
- XAMPP (or any PHP/MySQL server)
- Web browser
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Clone or Download
Clone this repository or download as ZIP:
```bash
git clone https://github.com/Ashish-Poojary/DonorHub.git
```

### Step 2: Setup Database
1. Start XAMPP and ensure Apache and MySQL are running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create a new database named `dms`
4. Import the database schema or create tables manually

### Step 3: Configure Database Connection
1. Copy `assets/db/conn.php.example` to `assets/db/conn.php`
2. Update database credentials in `assets/db/conn.php`:
```php
$host = "localhost";
$user = "root";           // Your MySQL username
$pass = "";               // Your MySQL password
$db = "dms";              // Your database name
```

### Step 4: Configure Email Settings
1. Copy `assets/config/email_config.php.example` to `assets/config/email_config.php`
2. Update SMTP settings in `assets/config/email_config.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');    // Your Gmail address
define('SMTP_PASSWORD', 'your_app_password');        // Gmail App Password
define('SMTP_FROM_EMAIL', 'your_email@gmail.com');   // Sender email
define('SMTP_FROM_NAME', 'DonorHub');
define('SMTP_ENCRYPTION', 'tls');
```

**Note for Gmail:**
- Enable 2-Step Verification
- Generate an App Password: https://myaccount.google.com/apppasswords
- Use the App Password (not your regular Gmail password)

### Step 5: File Permissions
Ensure these directories are writable:
- `pages/profile_pic/`
- `pages/camp_image/`

### Step 6: Access the Application
1. Start XAMPP (Apache and MySQL)
2. Navigate to `http://localhost/Dproject/` in your browser
3. Register as a donor, recipient, or use admin credentials

## Default Admin Credentials

**Note:** Change these after first login!
- Email: admin@donorhub.com
- Password: Admin@123

## Project Structure

```
Dproject/
├── assets/
│   ├── config/          # Configuration files (email settings)
│   ├── css/             # Stylesheets
│   ├── db/              # Database connection
│   └── images/          # Static images (logos, icons, backgrounds)
├── includes/            # PHP includes (header, footer, navbar, etc.)
├── pages/               # Main application pages
│   ├── admin.php        # Admin dashboard
│   ├── d_home.php       # Donor homepage
│   ├── r_home.php       # Recipient homepage
│   ├── register.php     # Registration page
│   ├── log_in.php       # Login page
│   ├── camp_view.php    # Campaign details and donation
│   └── ...
└── index.php            # Main entry point
```

## Features in Detail

### For Donors
- Browse available campaigns (filtered by donation type)
- Make donations (monetary or blood)
- View donation history
- Update profile information

### For Recipients
- Create donation campaigns
- Track campaign progress
- View donor information
- Manage campaigns (edit, stop, view details)

### For Admins
- Approve/reject campaigns
- Manage users (donors and recipients)
- View all campaigns
- Stop/start campaigns
- Send notifications to donors
- View campaign analytics

## Database Schema

Key tables:
- `users` - Stores donor, recipient, and admin information
- `campaigns` - Stores campaign details
- `donations` - Stores donation records

## Security Notes

- **Never commit** `conn.php` or `email_config.php` to version control
- Use strong passwords for database and email accounts
- Keep PHP and MySQL updated
- Validate and sanitize all user inputs (already implemented)
- Change default admin credentials immediately

## Troubleshooting

### Database Connection Error
- Check if MySQL is running in XAMPP
- Verify database credentials in `conn.php`
- Ensure database `dms` exists

### Email Not Sending
- Check SMTP credentials in `email_config.php`
- For Gmail, ensure you're using an App Password, not regular password
- Check if 2-Step Verification is enabled

### Image Upload Issues
- Ensure `profile_pic/` and `camp_image/` directories exist
- Check file permissions (directories should be writable)
- Verify file size limits in PHP configuration

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is for educational/demonstration purposes.

## Support

For issues or questions, please open an issue on GitHub.
