
    
<?php
session_start();
if(isset($_SESSION['mail_sent']) && $_SESSION['mail_sent']) {
    unset($_SESSION['mail_sent']);
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Success</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-success">
                <h4 class="alert-heading">Mail Sent Successfully!</h4>
                <p>Your message has been successfully sent to the selected donors.</p>
                <hr>
                <p class="mb-0">Thank you for your contribution and support.</p>
            </div>
            <button class="btn btn-success text-center" onclick="window.history.back();">GO BACK</button>
        </div>
    </body>
    </html>';
    echo '<script>alert("Mail sent successfully to selected donors")</script>';
       
       exit;
} 
else {
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Success</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-danger">
                <h4 class="alert-heading">Failed To send mail to Selected donors</h4>
                <p>Your message has been not successfully sent to the selected donors.</p>
                <hr>
                <p class="mb-0">Thank you for your contribution and support.</p>
            </div>
            <button class="btn btn-danger text-center" onclick="window.history.back();">GO BACK</button>
        </div>
    </body>
    </html>';
    echo '<script>alert("Mail not sent to selected donors")</script>';
    sleep(10);
    header('Location: admin.php');
    exit;
}

?>