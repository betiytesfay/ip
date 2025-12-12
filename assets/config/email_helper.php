<?php
/**
 * Email Helper Function using SMTP
 * This function uses PHPMailer for proper SMTP authentication
 * 
 * To use PHPMailer:
 * 1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer
 * 2. Extract and place in: assets/vendor/PHPMailer/
 * 3. Or install via Composer: composer require phpmailer/phpmailer
 */

if (!function_exists('send_email_smtp')) {
function send_email_smtp($to, $subject, $message, $from_email = null, $from_name = null) {
    // Load email configuration
    if (!defined('SMTP_FROM_EMAIL')) {
        include __DIR__ . '/email_config.php';
    }
    
    $from_email = $from_email ?: (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@donorhub.com');
    $from_name = $from_name ?: (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'DonorHub');
    
    // Check if PHPMailer is available
    $phpmailer_path = __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
    
    if (file_exists($phpmailer_path)) {
        // Use PHPMailer for SMTP authentication
        require_once $phpmailer_path;
        require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
        require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
        
        // Use fully qualified class names to avoid namespace issues
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Suppress errors
            $old_error_reporting = error_reporting(0);
            $old_display_errors = ini_set('display_errors', 0);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
            $mail->Password   = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
            $mail->SMTPSecure = defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'tls';
            $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->CharSet    = 'UTF-8';
            
            // Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to);
            $mail->addReplyTo($from_email, $from_name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            // Plain text alternative
            $mail->AltBody = strip_tags($message);
            
            $mail->send();
            
            // Restore error reporting
            error_reporting($old_error_reporting);
            if($old_display_errors !== false) {
                ini_set('display_errors', $old_display_errors);
            }
            
            return true;
        } catch (\Exception $e) {
            // Log error for debugging
            error_log("PHPMailer Error: " . $e->getMessage());
            
            // Restore error reporting
            error_reporting($old_error_reporting);
            if($old_display_errors !== false) {
                ini_set('display_errors', $old_display_errors);
            }
            
            // Silently fail - don't show errors to users
            return false;
        }
    } else {
        // PHPMailer not found - use native PHP SMTP implementation
        return send_email_native_smtp($to, $subject, $message, $from_email, $from_name);
    }
}
} // End function_exists check for send_email_smtp

/**
 * Native PHP SMTP implementation (works without PHPMailer)
 */
if (!function_exists('send_email_native_smtp')) {
function send_email_native_smtp($to, $subject, $message, $from_email = null, $from_name = null) {
    // Load email configuration
    if (!defined('SMTP_FROM_EMAIL')) {
        include __DIR__ . '/email_config.php';
    }
    
    $smtp_host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
    $smtp_port = defined('SMTP_PORT') ? SMTP_PORT : 587;
    $smtp_username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
    $smtp_password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
    $smtp_encryption = defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'tls';
    $from_email = $from_email ?: (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'noreply@donorhub.com');
    $from_name = $from_name ?: (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'DonorHub');
    
    // Suppress errors
    $old_error_reporting = error_reporting(0);
    $old_display_errors = ini_set('display_errors', 0);
    
    try {
        // Create socket connection (plain connection first, then upgrade to TLS)
        $socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);
        
        if (!$socket) {
            error_log("SMTP Connection failed: $errstr ($errno)");
            // Restore error reporting
            error_reporting($old_error_reporting);
            if($old_display_errors !== false) {
                ini_set('display_errors', $old_display_errors);
            }
            return false;
        }
        
        // Helper function to read SMTP response
        $read_response = function($socket) {
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) == ' ') {
                    break;
                }
            }
            return $response;
        };
        
        // Read server greeting
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '220') {
            error_log("SMTP Error: $response");
            fclose($socket);
            return false;
        }
        
        // Send EHLO
        fputs($socket, "EHLO " . $smtp_host . "\r\n");
        $response = $read_response($socket);
        
        // Start TLS if needed
        if ($smtp_encryption === 'tls' && strpos($response, 'STARTTLS') !== false) {
            fputs($socket, "STARTTLS\r\n");
            $response = $read_response($socket);
            if (substr($response, 0, 3) != '220') {
                error_log("STARTTLS failed: $response");
                fclose($socket);
                return false;
            }
            $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $crypto_method = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            }
            if (!@stream_socket_enable_crypto($socket, true, $crypto_method)) {
                error_log("TLS encryption failed");
                fclose($socket);
                // Restore error reporting
                error_reporting($old_error_reporting);
                if($old_display_errors !== false) {
                    ini_set('display_errors', $old_display_errors);
                }
                return false;
            }
            fputs($socket, "EHLO " . $smtp_host . "\r\n");
            $response = $read_response($socket);
        }
        
        // Authenticate
        fputs($socket, "AUTH LOGIN\r\n");
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '334') {
            error_log("AUTH LOGIN failed: $response");
            fclose($socket);
            return false;
        }
        
        fputs($socket, base64_encode($smtp_username) . "\r\n");
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '334') {
            error_log("Username authentication failed: $response");
            fclose($socket);
            return false;
        }
        
        fputs($socket, base64_encode($smtp_password) . "\r\n");
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '235') {
            error_log("Password authentication failed: $response");
            fclose($socket);
            return false;
        }
        
        // Set sender
        fputs($socket, "MAIL FROM: <$from_email>\r\n");
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '250') {
            error_log("MAIL FROM failed: $response");
            fclose($socket);
            return false;
        }
        
        // Set recipient
        fputs($socket, "RCPT TO: <$to>\r\n");
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '250') {
            error_log("RCPT TO failed: $response");
            fclose($socket);
            return false;
        }
        
        // Send data
        fputs($socket, "DATA\r\n");
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '354') {
            error_log("DATA command failed: $response");
            fclose($socket);
            return false;
        }
        
        // Build email headers and body
        // Check if message is HTML
        $is_html = (strip_tags($message) !== $message);
        $content_type = $is_html ? 'text/html' : 'text/plain';
        
        $email_data = "From: $from_name <$from_email>\r\n";
        $email_data .= "To: <$to>\r\n";
        $email_data .= "Reply-To: $from_email\r\n";
        $email_data .= "Subject: $subject\r\n";
        $email_data .= "Content-Type: $content_type; charset=UTF-8\r\n";
        $email_data .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $email_data .= "\r\n";
        $email_data .= $message . "\r\n";
        $email_data .= ".\r\n";
        
        fputs($socket, $email_data);
        $response = $read_response($socket);
        if (substr($response, 0, 3) != '250') {
            error_log("Email sending failed: $response");
            fclose($socket);
            return false;
        }
        
        // Quit
        fputs($socket, "QUIT\r\n");
        $read_response($socket);
        fclose($socket);
        
        // Restore error reporting
        error_reporting($old_error_reporting);
        if($old_display_errors !== false) {
            ini_set('display_errors', $old_display_errors);
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("SMTP Exception: " . $e->getMessage());
        
        // Restore error reporting
        error_reporting($old_error_reporting);
        if($old_display_errors !== false) {
            ini_set('display_errors', $old_display_errors);
        }
        
        return false;
    }
}
} // End function_exists check for send_email_native_smtp

?>
