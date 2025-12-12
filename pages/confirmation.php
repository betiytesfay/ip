<?php
// Enable error reporting temporarily for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();
include '../assets/db/conn.php';

// Include email config and helper with error handling
$email_config_path = '../assets/config/email_config.php';
$email_helper_path = '../assets/config/email_helper.php';

if (file_exists($email_config_path)) {
    include $email_config_path;
} else {
    // Define defaults if config file doesn't exist
    if (!defined('SMTP_FROM_EMAIL')) {
        define('SMTP_FROM_EMAIL', 'noreply@donorhub.com');
        define('SMTP_FROM_NAME', 'DonorHub');
    }
}

if (file_exists($email_helper_path)) {
    include $email_helper_path;
}

// Fallback function if helper doesn't exist or function wasn't defined
if (!function_exists('send_email_smtp')) {
    function send_email_smtp($to, $subject, $message, $from_email = null, $from_name = null) {
        $old_error_reporting = error_reporting(0);
        $old_display_errors = ini_set('display_errors', 0);
        
        $from_email = $from_email ?: 'noreply@donorhub.com';
        $from_name = $from_name ?: 'DonorHub';
        
        $headers = "From: $from_name <$from_email>\r\n";
        $headers .= "Reply-To: $from_email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        $result = @mail($to, $subject, $message, $headers);
        
        error_reporting($old_error_reporting);
        if($old_display_errors !== false) {
            ini_set('display_errors', $old_display_errors);
        }
        
        return $result;
    }
} // End function_exists check

// Get and validate parameters
$camp_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$recip_id = isset($_GET['crby']) ? intval($_GET['crby']) : 0;
$don_id = isset($_SESSION['d_id']) ? intval($_SESSION['d_id']) : 0;

// Validate required parameters
if (empty($camp_id) || empty($recip_id) || empty($don_id)) {
    header("Location: log_in.php");
    exit();
}

// Query to retrieve campaign details (with SQL injection protection - using intval makes it safe)
$campaignQuery = "SELECT camp_title, est_amt, amt_collected, camp_type, donation_address, donation_date, donation_time, blood_group FROM campaigns WHERE camp_id = $camp_id";
$campaignResult = mysqli_query($conn, $campaignQuery);

// Check for query errors
if (!$campaignResult) {
    error_log("Campaign query error: " . mysqli_error($conn));
    header("Location: log_in.php");
    exit();
}

if (!$campaignResult || mysqli_num_rows($campaignResult) == 0) {
    header("Location: log_in.php");
    exit();
}

$campaignData = mysqli_fetch_assoc($campaignResult);

$camp_title = isset($campaignData['camp_title']) ? $campaignData['camp_title'] : '';
$est_amt = isset($campaignData['est_amt']) ? $campaignData['est_amt'] : 0;
$amt_collected = isset($campaignData['amt_collected']) ? $campaignData['amt_collected'] : 0;
$camp_type = isset($campaignData['camp_type']) ? $campaignData['camp_type'] : '';
$donation_address = isset($campaignData['donation_address']) ? $campaignData['donation_address'] : '';
$donation_date = isset($campaignData['donation_date']) ? $campaignData['donation_date'] : '';
$donation_time = isset($campaignData['donation_time']) ? $campaignData['donation_time'] : '';
$blood_group = isset($campaignData['blood_group']) ? $campaignData['blood_group'] : '';
$isBloodCampaign = ($camp_type === 'blood');

// Query to retrieve donor details (with SQL injection protection - using intval makes it safe)
$donorQuery = "SELECT donated_amt FROM donations WHERE donor_id = $don_id AND camp_id = $camp_id";
$donorResult = mysqli_query($conn, $donorQuery);

// Check for query errors
if (!$donorResult) {
    error_log("Donor query error: " . mysqli_error($conn));
}

if ($donorResult && mysqli_num_rows($donorResult) > 0) {
    $donorData = mysqli_fetch_assoc($donorResult);
    $donated_amt = isset($donorData['donated_amt']) ? $donorData['donated_amt'] : 0;
} else {
    $donated_amt = 0;
}

// Query to retrieve donor name from users table (with SQL injection protection - using intval makes it safe)
$userQuery = "SELECT fname, lname, email FROM users WHERE user_id = $don_id";
$userResult = mysqli_query($conn, $userQuery);

// Check for query errors
if (!$userResult) {
    error_log("User query error: " . mysqli_error($conn));
}

if ($userResult && mysqli_num_rows($userResult) > 0) {
    $userData = mysqli_fetch_assoc($userResult);
    $donor_mail = isset($userData['email']) ? $userData['email'] : '';
    $donor_name = (isset($userData['fname']) ? $userData['fname'] : '') . ' ' . (isset($userData['lname']) ? $userData['lname'] : '');
} else {
    $donor_mail = '';
    $donor_name = '';
}

// Query to retrieve recipient name (with SQL injection protection - using intval makes it safe)
$recipientQuery = "SELECT fname, lname, email FROM users WHERE user_id = $recip_id";
$recipientResult = mysqli_query($conn, $recipientQuery);

// Check for query errors
if (!$recipientResult) {
    error_log("Recipient query error: " . mysqli_error($conn));
}

if ($recipientResult && mysqli_num_rows($recipientResult) > 0) {
    $recipientData = mysqli_fetch_assoc($recipientResult);
    $recipient_name = (isset($recipientData['fname']) ? $recipientData['fname'] : '') . ' ' . (isset($recipientData['lname']) ? $recipientData['lname'] : '');
    $recipient_mail = isset($recipientData['email']) ? $recipientData['email'] : '';
} else {
    $recipient_name = '';
    $recipient_mail = '';
}
//function
$don_amt = isset($_SESSION['amt_new']) ? $_SESSION['amt_new'] : 0;
//mail functions
function send_don_mail ($cname,$don_amt,$damt,$raised,$benfit,$donor_name,$donor_mail){
  
   // Suppress all errors and warnings for mail function
   $old_error_reporting = error_reporting(0);
   $old_display_errors = ini_set('display_errors', 0);
  
   $subject = "Thank You For Donating - DonorHub";
   
   // HTML Email Template
   $message = '
   <!DOCTYPE html>
   <html>
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <style>
           body {
               font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
               line-height: 1.6;
               color: #333;
               max-width: 600px;
               margin: 0 auto;
               padding: 20px;
               background-color: #f4f4f4;
           }
           .email-container {
               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
               border-radius: 10px;
               padding: 30px;
               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
           }
           .header {
               text-align: center;
               color: white;
               margin-bottom: 30px;
           }
           .header h1 {
               margin: 0;
               font-size: 28px;
               font-weight: bold;
           }
           .content {
               background: white;
               border-radius: 8px;
               padding: 30px;
               margin-top: 20px;
           }
           .greeting {
               font-size: 18px;
               color: #667eea;
               margin-bottom: 20px;
           }
           .info-box {
               background: #f8f9fa;
               border-left: 4px solid #667eea;
               padding: 15px;
               margin: 15px 0;
               border-radius: 4px;
           }
           .info-row {
               display: flex;
               justify-content: space-between;
               padding: 10px 0;
               border-bottom: 1px solid #e0e0e0;
           }
           .info-row:last-child {
               border-bottom: none;
           }
           .info-label {
               font-weight: 600;
               color: #555;
           }
           .info-value {
               color: #667eea;
               font-weight: bold;
           }
           .highlight {
               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
               color: white;
               padding: 20px;
               border-radius: 8px;
               text-align: center;
               margin: 20px 0;
           }
           .highlight h2 {
               margin: 0;
               font-size: 24px;
           }
           .footer {
               text-align: center;
               margin-top: 30px;
               padding-top: 20px;
               border-top: 2px solid #e0e0e0;
               color: #777;
               font-size: 14px;
           }
           .logo {
               font-size: 32px;
               font-weight: bold;
               color: white;
               margin-bottom: 10px;
           }
       </style>
   </head>
   <body>
       <div class="email-container">
           <div class="header">
               <div class="logo">❤️ DonorHub</div>
               <h1>Thank You For Your Generous Donation!</h1>
           </div>
           
           <div class="content">
               <div class="greeting">
                   Dear ' . htmlspecialchars($donor_name) . ',
               </div>
               
               <p style="font-size: 16px; color: #555;">
                   We are incredibly grateful for your generous donation! Your contribution makes a real difference and helps us continue our mission.
               </p>
               
               <div class="highlight">
                   <h2>₹' . number_format($don_amt, 2) . '</h2>
                   <p style="margin: 5px 0 0 0; font-size: 14px;">Your Donation Amount</p>
               </div>
               
               <div class="info-box">
                   <div class="info-row">
                       <span class="info-label">Campaign Name:</span>
                       <span class="info-value">' . htmlspecialchars($cname) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Your Total Donation:</span>
                       <span class="info-value">₹' . number_format($damt, 2) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Total Raised:</span>
                       <span class="info-value">₹' . number_format($raised, 2) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Benefited By:</span>
                       <span class="info-value">' . htmlspecialchars($benfit) . '</span>
                   </div>
               </div>
               
               <p style="font-size: 16px; color: #555; margin-top: 25px;">
                   Your kindness and generosity are truly appreciated. Together, we are making a positive impact in the lives of those in need.
               </p>
               
               <p style="font-size: 16px; color: #555;">
                   Thank you for being a part of the DonorHub community!
               </p>
               
               <div class="footer">
                   <p><strong>DonorHub</strong></p>
                   <p>Making a difference, one donation at a time.</p>
                   <p style="font-size: 12px; color: #999; margin-top: 10px;">
                       This is an automated email. Please do not reply to this message.
                   </p>
               </div>
           </div>
       </div>
   </body>
   </html>';

    // Send the email using SMTP helper function
    $to = $donor_mail; 
    
    // Use configured email settings
    $from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@donorhub.com';
    $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'DonorHub';
    
    // Use SMTP helper function (supports PHPMailer if available)
    $mailSent = send_email_smtp($to, $subject, $message, $from_email, $from_name);
    
    // Restore error reporting
    error_reporting($old_error_reporting);
    if($old_display_errors !== false) {
        ini_set('display_errors', $old_display_errors);
    }
    
    // Email functionality may not work in local development without proper mail server configuration
    // Silently return - don't show errors to users
    return $mailSent;
}
function send_recip_mail (  $cname,$don_amt,$donor_name,$raised,$estamt,$recipient_mail){
  
   // Suppress all errors and warnings for mail function
   $old_error_reporting = error_reporting(0);
   $old_display_errors = ini_set('display_errors', 0);
  
  $subject = "🎉 New Donation Received - DonorHub";
  
  // Calculate progress percentage
  $progress = ($estamt > 0) ? round(($raised / $estamt) * 100) : 0;
  $progress = min($progress, 100); // Cap at 100%
  
  // HTML Email Template
   $message = '
   <!DOCTYPE html>
   <html>
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <style>
           body {
               font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
               line-height: 1.6;
               color: #333;
               max-width: 600px;
               margin: 0 auto;
               padding: 20px;
               background-color: #f4f4f4;
           }
           .email-container {
               background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
               border-radius: 10px;
               padding: 30px;
               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
           }
           .header {
               text-align: center;
               color: white;
               margin-bottom: 30px;
           }
           .header h1 {
               margin: 0;
               font-size: 28px;
               font-weight: bold;
           }
           .content {
               background: white;
               border-radius: 8px;
               padding: 30px;
               margin-top: 20px;
           }
           .greeting {
               font-size: 18px;
               color: #f5576c;
               margin-bottom: 20px;
           }
           .alert-box {
               background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
               color: white;
               padding: 20px;
               border-radius: 8px;
               text-align: center;
               margin: 20px 0;
           }
           .alert-box h2 {
               margin: 0;
               font-size: 32px;
           }
           .info-box {
               background: #f8f9fa;
               border-left: 4px solid #f5576c;
               padding: 15px;
               margin: 15px 0;
               border-radius: 4px;
           }
           .info-row {
               display: flex;
               justify-content: space-between;
               padding: 10px 0;
               border-bottom: 1px solid #e0e0e0;
           }
           .info-row:last-child {
               border-bottom: none;
           }
           .info-label {
               font-weight: 600;
               color: #555;
           }
           .info-value {
               color: #f5576c;
               font-weight: bold;
           }
           .progress-bar {
               background: #e0e0e0;
               border-radius: 10px;
               height: 30px;
               margin: 20px 0;
               overflow: hidden;
               position: relative;
           }
           .progress-fill {
               background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
               height: 100%;
               border-radius: 10px;
               display: flex;
               align-items: center;
               justify-content: center;
               color: white;
               font-weight: bold;
               transition: width 0.3s ease;
           }
           .footer {
               text-align: center;
               margin-top: 30px;
               padding-top: 20px;
               border-top: 2px solid #e0e0e0;
               color: #777;
               font-size: 14px;
           }
           .logo {
               font-size: 32px;
               font-weight: bold;
               color: white;
               margin-bottom: 10px;
           }
           .donor-name {
               font-size: 20px;
               color: #f5576c;
               font-weight: bold;
               margin: 10px 0;
           }
       </style>
   </head>
   <body>
       <div class="email-container">
           <div class="header">
               <div class="logo">🎉 DonorHub</div>
               <h1>New Donation Received!</h1>
           </div>
           
           <div class="content">
               <div class="greeting">
                   Great News!
               </div>
               
               <p style="font-size: 16px; color: #555;">
                   Your campaign has received a new donation! We are excited to share this update with you.
               </p>
               
               <div class="alert-box">
                   <h2>₹' . number_format($don_amt, 2) . '</h2>
                   <p style="margin: 5px 0 0 0; font-size: 16px;">New Donation Received!</p>
               </div>
               
               <div class="donor-name">
                   👤 Donated by: ' . htmlspecialchars($donor_name) . '
               </div>
               
               <div class="info-box">
                   <div class="info-row">
                       <span class="info-label">Campaign Name:</span>
                       <span class="info-value">' . htmlspecialchars($cname) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Amount:</span>
                       <span class="info-value">₹' . number_format($don_amt, 2) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Total Raised:</span>
                       <span class="info-value">₹' . number_format($raised, 2) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Target Amount:</span>
                       <span class="info-value">₹' . number_format($estamt, 2) . '</span>
                   </div>
               </div>
               
               <div style="margin: 25px 0;">
                   <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                       <span style="font-weight: 600; color: #555;">Campaign Progress:</span>
                       <span style="color: #f5576c; font-weight: bold;">' . $progress . '%</span>
                   </div>
                   <div class="progress-bar">
                       <div class="progress-fill" style="width: ' . $progress . '%;">
                           ' . $progress . '%
                       </div>
                   </div>
               </div>
               
               <p style="font-size: 16px; color: #555; margin-top: 25px;">
                   Keep up the great work! Your campaign is making a real difference. Share your campaign to reach even more supporters!
               </p>
               
               <div class="footer">
                   <p><strong>DonorHub</strong></p>
                   <p>Making a difference, one donation at a time.</p>
                   <p style="font-size: 12px; color: #999; margin-top: 10px;">
                       This is an automated email. Please do not reply to this message.
                   </p>
               </div>
           </div>
       </div>
   </body>
   </html>';

   // Send the email using SMTP helper function
   $to = $recipient_mail; 
   
   // Use configured email settings
   $from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@donorhub.com';
   $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'DonorHub';
   
   // Use SMTP helper function (supports PHPMailer if available)
   $mailSent = send_email_smtp($to, $subject, $message, $from_email, $from_name);
   
   // Restore error reporting
   error_reporting($old_error_reporting);
   if($old_display_errors !== false) {
       ini_set('display_errors', $old_display_errors);
   }
   
   // Email functionality may not work in local development without proper mail server configuration
   // Silently return - don't show errors to users
   return $mailSent;
}

// Blood Donation Email Functions
function send_don_mail_blood($cname, $don_amt, $damt, $raised, $benfit, $donor_name, $donor_mail, $blood_group, $donation_address, $donation_date, $donation_time){
  
   // Suppress all errors and warnings for mail function
   $old_error_reporting = error_reporting(0);
   $old_display_errors = ini_set('display_errors', 0);
  
   $subject = "Thank You For Your Blood Donation - DonorHub";
  
   // Format date and time
   $formatted_date = !empty($donation_date) ? date('F j, Y', strtotime($donation_date)) : 'To be announced';
   $formatted_time = !empty($donation_time) ? date('g:i A', strtotime($donation_time)) : 'To be announced';
  
   // HTML Email Template for Blood Donation
   $message = '
   <!DOCTYPE html>
   <html>
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <style>
           body {
               font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
               line-height: 1.6;
               color: #333;
               max-width: 600px;
               margin: 0 auto;
               padding: 20px;
               background-color: #f4f4f4;
           }
           .email-container {
               background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
               border-radius: 10px;
               padding: 30px;
               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
           }
           .header {
               text-align: center;
               color: white;
               margin-bottom: 30px;
           }
           .header h1 {
               margin: 0;
               font-size: 28px;
               font-weight: bold;
           }
           .content {
               background: white;
               border-radius: 8px;
               padding: 30px;
               margin-top: 20px;
           }
           .greeting {
               font-size: 18px;
               color: #dc3545;
               margin-bottom: 20px;
           }
           .info-box {
               background: #fff5f5;
               border-left: 4px solid #dc3545;
               padding: 15px;
               margin: 15px 0;
               border-radius: 4px;
           }
           .info-row {
               display: flex;
               justify-content: space-between;
               padding: 10px 0;
               border-bottom: 1px solid #e0e0e0;
           }
           .info-row:last-child {
               border-bottom: none;
           }
           .info-label {
               font-weight: 600;
               color: #555;
           }
           .info-value {
               color: #dc3545;
               font-weight: bold;
           }
           .highlight {
               background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
               color: white;
               padding: 20px;
               border-radius: 8px;
               text-align: center;
               margin: 20px 0;
           }
           .highlight h2 {
               margin: 0;
               font-size: 24px;
           }
           .blood-details {
               background: #ffe6e6;
               border: 2px dashed #dc3545;
               border-radius: 8px;
               padding: 20px;
               margin: 20px 0;
           }
           .blood-details h3 {
               color: #dc3545;
               margin-top: 0;
               font-size: 20px;
           }
           .footer {
               text-align: center;
               margin-top: 30px;
               padding-top: 20px;
               border-top: 2px solid #e0e0e0;
               color: #777;
               font-size: 14px;
           }
           .logo {
               font-size: 32px;
               font-weight: bold;
               color: white;
               margin-bottom: 10px;
           }
           .blood-icon {
               font-size: 48px;
               margin: 10px 0;
           }
       </style>
   </head>
   <body>
       <div class="email-container">
           <div class="header">
               <div class="logo">🩸 DonorHub</div>
               <div class="blood-icon">❤️</div>
               <h1>Thank You For Your Blood Donation!</h1>
           </div>
           
           <div class="content">
               <div class="greeting">
                   Dear ' . htmlspecialchars($donor_name) . ',
               </div>
               
               <p style="font-size: 16px; color: #555;">
                   We are incredibly grateful for your life-saving blood donation! Your selfless act of donating blood will help save lives and make a real difference in someone\'s life.
               </p>
               
               <div class="highlight">
                   <h2>1 Pint (450-500 ml)</h2>
                   <p style="margin: 5px 0 0 0; font-size: 14px;">Your Blood Donation</p>
               </div>
               
               <div class="blood-details">
                   <h3>🩸 Blood Donation Details</h3>
                   <div class="info-row">
                       <span class="info-label">Blood Group Required:</span>
                       <span class="info-value">' . htmlspecialchars($blood_group) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Date:</span>
                       <span class="info-value">' . htmlspecialchars($formatted_date) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Time:</span>
                       <span class="info-value">' . htmlspecialchars($formatted_time) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Address:</span>
                       <span class="info-value" style="text-align: right; max-width: 60%;">' . htmlspecialchars($donation_address) . '</span>
                   </div>
               </div>
               
               <div class="info-box">
                   <div class="info-row">
                       <span class="info-label">Campaign Name:</span>
                       <span class="info-value">' . htmlspecialchars($cname) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Your Total Donations:</span>
                       <span class="info-value">' . number_format($damt, 0) . ' pint' . ($damt != 1 ? 's' : '') . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Total Collected:</span>
                       <span class="info-value">' . number_format($raised, 0) . ' pint' . ($raised != 1 ? 's' : '') . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Benefited By:</span>
                       <span class="info-value">' . htmlspecialchars($benfit) . '</span>
                   </div>
               </div>
               
               <p style="font-size: 16px; color: #555; margin-top: 25px;">
                   <strong>Important Reminders:</strong><br>
                   • Please arrive 15 minutes before your scheduled donation time<br>
                   • Bring a valid ID with you<br>
                   • Eat a healthy meal before donating<br>
                   • Stay hydrated and get plenty of rest
               </p>
               
               <p style="font-size: 16px; color: #555;">
                   Your generosity and courage to donate blood are truly appreciated. You are a hero saving lives!
               </p>
               
               <p style="font-size: 16px; color: #555;">
                   Thank you for being a part of the DonorHub community!
               </p>
               
               <div class="footer">
                   <p><strong>DonorHub</strong></p>
                   <p>Making a difference, one donation at a time.</p>
                   <p style="font-size: 12px; color: #999; margin-top: 10px;">
                       This is an automated email. Please do not reply to this message.
                   </p>
               </div>
           </div>
       </div>
   </body>
   </html>';

    // Send the email using SMTP helper function
    $to = $donor_mail; 
    
    // Use configured email settings
    $from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@donorhub.com';
    $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'DonorHub';
    
    // Use SMTP helper function (supports PHPMailer if available)
    $mailSent = send_email_smtp($to, $subject, $message, $from_email, $from_name);
    
    // Restore error reporting
    error_reporting($old_error_reporting);
    if($old_display_errors !== false) {
        ini_set('display_errors', $old_display_errors);
    }
    
    return $mailSent;
}

function send_recip_mail_blood($cname, $don_amt, $donor_name, $raised, $estamt, $recipient_mail, $blood_group, $donation_address, $donation_date, $donation_time){
  
   // Suppress all errors and warnings for mail function
   $old_error_reporting = error_reporting(0);
   $old_display_errors = ini_set('display_errors', 0);
  
  $subject = "🩸 New Blood Donation Received - DonorHub";
  
  // Calculate progress percentage
  $progress = ($estamt > 0) ? round(($raised / $estamt) * 100) : 0;
  $progress = min($progress, 100); // Cap at 100%
  
  // Format date and time
  $formatted_date = !empty($donation_date) ? date('F j, Y', strtotime($donation_date)) : 'To be announced';
  $formatted_time = !empty($donation_time) ? date('g:i A', strtotime($donation_time)) : 'To be announced';
  
  // HTML Email Template for Blood Donation
   $message = '
   <!DOCTYPE html>
   <html>
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <style>
           body {
               font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
               line-height: 1.6;
               color: #333;
               max-width: 600px;
               margin: 0 auto;
               padding: 20px;
               background-color: #f4f4f4;
           }
           .email-container {
               background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
               border-radius: 10px;
               padding: 30px;
               box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
           }
           .header {
               text-align: center;
               color: white;
               margin-bottom: 30px;
           }
           .header h1 {
               margin: 0;
               font-size: 28px;
               font-weight: bold;
           }
           .content {
               background: white;
               border-radius: 8px;
               padding: 30px;
               margin-top: 20px;
           }
           .greeting {
               font-size: 18px;
               color: #dc3545;
               margin-bottom: 20px;
           }
           .alert-box {
               background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
               color: white;
               padding: 20px;
               border-radius: 8px;
               text-align: center;
               margin: 20px 0;
           }
           .alert-box h2 {
               margin: 0;
               font-size: 32px;
           }
           .info-box {
               background: #fff5f5;
               border-left: 4px solid #dc3545;
               padding: 15px;
               margin: 15px 0;
               border-radius: 4px;
           }
           .info-row {
               display: flex;
               justify-content: space-between;
               padding: 10px 0;
               border-bottom: 1px solid #e0e0e0;
           }
           .info-row:last-child {
               border-bottom: none;
           }
           .info-label {
               font-weight: 600;
               color: #555;
           }
           .info-value {
               color: #dc3545;
               font-weight: bold;
           }
           .progress-bar {
               background: #e0e0e0;
               border-radius: 10px;
               height: 30px;
               margin: 20px 0;
               overflow: hidden;
               position: relative;
           }
           .progress-fill {
               background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
               height: 100%;
               border-radius: 10px;
               display: flex;
               align-items: center;
               justify-content: center;
               color: white;
               font-weight: bold;
               transition: width 0.3s ease;
           }
           .footer {
               text-align: center;
               margin-top: 30px;
               padding-top: 20px;
               border-top: 2px solid #e0e0e0;
               color: #777;
               font-size: 14px;
           }
           .logo {
               font-size: 32px;
               font-weight: bold;
               color: white;
               margin-bottom: 10px;
           }
           .donor-name {
               font-size: 20px;
               color: #dc3545;
               font-weight: bold;
               margin: 10px 0;
           }
           .blood-details {
               background: #ffe6e6;
               border: 2px dashed #dc3545;
               border-radius: 8px;
               padding: 20px;
               margin: 20px 0;
           }
           .blood-details h3 {
               color: #dc3545;
               margin-top: 0;
               font-size: 18px;
           }
           .blood-icon {
               font-size: 48px;
               margin: 10px 0;
           }
       </style>
   </head>
   <body>
       <div class="email-container">
           <div class="header">
               <div class="logo">🩸 DonorHub</div>
               <div class="blood-icon">❤️</div>
               <h1>New Blood Donation Received!</h1>
           </div>
           
           <div class="content">
               <div class="greeting">
                   Great News!
               </div>
               
               <p style="font-size: 16px; color: #555;">
                   Your blood donation campaign has received a new donation! We are excited to share this life-saving update with you.
               </p>
               
               <div class="alert-box">
                   <h2>1 Pint</h2>
                   <p style="margin: 5px 0 0 0; font-size: 16px;">New Blood Donation Received!</p>
               </div>
               
               <div class="donor-name">
                   👤 Donated by: ' . htmlspecialchars($donor_name) . '
               </div>
               
               <div class="blood-details">
                   <h3>🩸 Campaign Details</h3>
                   <div class="info-row">
                       <span class="info-label">Required Blood Group:</span>
                       <span class="info-value">' . htmlspecialchars($blood_group) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Date:</span>
                       <span class="info-value">' . htmlspecialchars($formatted_date) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Time:</span>
                       <span class="info-value">' . htmlspecialchars($formatted_time) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Address:</span>
                       <span class="info-value" style="text-align: right; max-width: 60%;">' . htmlspecialchars($donation_address) . '</span>
                   </div>
               </div>
               
               <div class="info-box">
                   <div class="info-row">
                       <span class="info-label">Campaign Name:</span>
                       <span class="info-value">' . htmlspecialchars($cname) . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Donation Amount:</span>
                       <span class="info-value">1 pint (450-500 ml)</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Total Collected:</span>
                       <span class="info-value">' . number_format($raised, 0) . ' pint' . ($raised != 1 ? 's' : '') . '</span>
                   </div>
                   <div class="info-row">
                       <span class="info-label">Target Amount:</span>
                       <span class="info-value">' . number_format($estamt, 0) . ' pint' . ($estamt != 1 ? 's' : '') . '</span>
                   </div>
               </div>
               
               <div style="margin: 25px 0;">
                   <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                       <span style="font-weight: 600; color: #555;">Campaign Progress:</span>
                       <span style="color: #dc3545; font-weight: bold;">' . $progress . '%</span>
                   </div>
                   <div class="progress-bar">
                       <div class="progress-fill" style="width: ' . $progress . '%;">
                           ' . $progress . '%
                       </div>
                   </div>
               </div>
               
               <p style="font-size: 16px; color: #555; margin-top: 25px;">
                   Keep up the great work! Your blood donation campaign is saving lives. Share your campaign to reach even more donors!
               </p>
               
               <div class="footer">
                   <p><strong>DonorHub</strong></p>
                   <p>Making a difference, one donation at a time.</p>
                   <p style="font-size: 12px; color: #999; margin-top: 10px;">
                       This is an automated email. Please do not reply to this message.
                   </p>
               </div>
           </div>
       </div>
   </body>
   </html>';

   // Send the email using SMTP helper function
   $to = $recipient_mail; 
   
   // Use configured email settings
   $from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@donorhub.com';
   $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'DonorHub';
   
   // Use SMTP helper function (supports PHPMailer if available)
   $mailSent = send_email_smtp($to, $subject, $message, $from_email, $from_name);
   
   // Restore error reporting
   error_reporting($old_error_reporting);
   if($old_display_errors !== false) {
       ini_set('display_errors', $old_display_errors);
   }
   
   return $mailSent;
}

// Send emails with error handling - use blood donation emails if it's a blood campaign
try {
    if ($isBloodCampaign) {
        // Use blood donation email functions
        if (function_exists('send_don_mail_blood')) {
            @send_don_mail_blood($camp_title, $don_amt, $donated_amt, $amt_collected, $recipient_name, $donor_name, $donor_mail, $blood_group, $donation_address, $donation_date, $donation_time);
        }
        if (function_exists('send_recip_mail_blood')) {
            @send_recip_mail_blood($camp_title, $don_amt, $donor_name, $amt_collected, $est_amt, $recipient_mail, $blood_group, $donation_address, $donation_date, $donation_time);
        }
    } else {
        // Use regular donation email functions
        if (function_exists('send_don_mail')) {
            @send_don_mail($camp_title, $don_amt, $donated_amt, $amt_collected, $recipient_name, $donor_name, $donor_mail);
        }
        if (function_exists('send_recip_mail')) {
            @send_recip_mail($camp_title, $don_amt, $donor_name, $amt_collected, $est_amt, $recipient_mail);
        }
    }
} catch (Exception $e) {
    // Silently continue - email errors shouldn't prevent page display
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <title>Donation Bill</title>
</head>

<body>
  <div class="container py-5">
    <style>
      html, body {
        overflow-x: hidden !important;
        max-width: 100vw;
        width: 100%;
      }
      body {
        scroll-behavior: smooth;
      }

      .section {
        min-height: 800px;
        max-width: 100%;
        overflow-x: hidden;
      }

      .section {
        width: 100%;
        padding-top: 1.5px;
      }

      .title-text {
        font-family: 'Times New Roman', Times, serif;
        font-size: 70px;
        font-weight: bold;
      }
    </style>
    <!--Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href=" 	https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <!--Icon--->
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <title>Donate</title>
    </head>

    <body class="pt-5">

      <nav class="navbar navbar-expand-md navbar-light bg-white fixed-top shadow rounded-bottom" style="z-index: 1;">
        <div class="container px-1">
          <a class="navbar-brand fw-bold" href="#">
            <img src="../assets/images/logo.png" alt="logo" width="40" height="40" class="img-fluid me-2">
            <span class="logo-text">DonorHub</span>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="bi bi-list-nested"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav m-auto">

            </ul>
            <ul class="navbar-nav">
            <a href="#" class="btn btn-outline-danger me-1 w-md-1" onclick="window.print(); return false;"><i class="bi bi-printer me-1"></i>PrintBill</a>
              <a href="camp_view.php?id=<?php echo $camp_id; ?>&crby=<?php echo $recip_id; ?>" class="btn btn-outline-danger me-1 w-md-1"><i class="bi bi-arrow-left me-1"></i>BACK</a>

            </ul>
          </div>
        </div>
      </nav>
      <div class="text-center">
        <img src="../assets/images/thank-logo.gif" class="img-fluid   " alt="Thank_logo" style="border-radius: 50%; ">
        <h1 class="mt-2 fw-bolder ">Donation Successful</h1>
      </div>
      <hr>
      <h1 class="mb-4 fs-4 ">Donation Details</h1>

      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Thank you for your donation!</h5>

          <p class="card-text">Your donation details:</p>

          <ul class="list-group">
            <li class="list-group-item">
              <strong>Campaign Name:</strong>
              <?php echo $camp_title; ?>
              <?php if($isBloodCampaign && !empty($blood_group)): ?>
                <br><small class="text-danger"><i class="bi bi-heart-pulse"></i> Required Blood Group: <?= $blood_group; ?></small>
              <?php endif; ?>
            </li>
            <li class="list-group-item">
              <strong>Donor Name:</strong>
              <?php echo $donor_name; ?>
            </li>
            <li class="list-group-item">
              <strong>Donated <?php echo $isBloodCampaign ? 'Quantity' : 'Amount'; ?>:</strong>
              <?php if($isBloodCampaign): ?>
                <i class="bi bi-droplet"></i><?php echo $donated_amt; ?> pint<?= $donated_amt != 1 ? 's' : ''; ?>
              <?php else: ?>
                <i class="bi bi-currency-rupee"></i><?php echo $donated_amt; ?>
              <?php endif; ?>
            </li>
            <li class="list-group-item">
              <strong>Total Raised:</strong>
              <?php if($isBloodCampaign): ?>
                <i class="bi bi-droplet"></i><?php echo $amt_collected; ?> pint<?= $amt_collected != 1 ? 's' : ''; ?>
              <?php else: ?>
                <i class="bi bi-currency-rupee"></i><?php echo $amt_collected; ?>
              <?php endif; ?>
            </li>
            <li class="list-group-item">
              <strong><?php echo $isBloodCampaign ? 'Required Quantity' : 'Estimated Amount'; ?>:</strong>
              <?php if($isBloodCampaign): ?>
                <i class="bi bi-droplet"></i><?php echo $est_amt; ?> pint<?= $est_amt != 1 ? 's' : ''; ?>
              <?php else: ?>
                <i class="bi bi-currency-rupee"></i><?php echo $est_amt; ?>
              <?php endif; ?>
            </li>
            <li class="list-group-item">
              <strong>Benfited By:</strong>
              <?php echo $recipient_name; ?>
            </li>
          </ul>
          
          <?php if($isBloodCampaign && !empty($donation_address) && !empty($donation_date) && !empty($donation_time)): ?>
          <!-- Blood Donation Details Card -->
          <div class="card mt-4 border-danger" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white;">
            <div class="card-body">
              <h5 class="card-title text-center mb-4"><i class="bi bi-heart-pulse-fill me-2"></i>Blood Donation Appointment Details</h5>
              
              <div class="alert alert-light" role="alert">
                <h6 class="alert-heading"><i class="bi bi-info-circle-fill text-danger me-2"></i>Please Note:</h6>
                <p class="mb-0">Please arrive at the specified location on the given date and time for your blood donation.</p>
              </div>
              
              <div class="row mt-3">
                <div class="col-md-12 mb-3">
                  <div class="d-flex align-items-start">
                    <i class="bi bi-geo-alt-fill fs-4 me-3 mt-1"></i>
                    <div>
                      <strong>Donation Address:</strong><br>
                      <p class="mb-0"><?php echo nl2br(htmlspecialchars($donation_address)); ?></p>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-event fs-4 me-3"></i>
                    <div>
                      <strong>Date:</strong><br>
                      <span><?php echo date('F d, Y', strtotime($donation_date)); ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-clock fs-4 me-3"></i>
                    <div>
                      <strong>Time:</strong><br>
                      <span><?php echo date('h:i A', strtotime($donation_time)); ?></span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-3 p-3 bg-white text-dark rounded">
                <small><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i><strong>Important:</strong> Please bring a valid ID proof. Make sure you have eaten before donating blood and are well hydrated.</small>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <p class="mt-4">Thank you for supporting our cause!</p>
        </div>
      </div>
   
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>