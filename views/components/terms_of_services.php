<?php 

?>
<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php baseurl("css/bootstrap.min.css") ?>" />
    <link rel="stylesheet" href="<?php baseurl("css/style.css") ?>" />
    <title>Terms of Services - Scriptores</title>
    <style>
        .terms-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .terms-container h1, .terms-container h2, .terms-container h3 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .terms-container p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .terms-container ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body style="background-color: #f5f5f5; margin: 0; padding: 20px;">
    <div class="terms-container">
        <h1>Terms of Services</h1>
        <p>Welcome to Scriptores. By accessing or using our platform, you agree to comply with and be bound by the following terms and conditions:</p>
        
        <h2>1. Acceptance of Terms</h2>
        <p>By using Scriptores, you agree to these Terms of Services and our Privacy Policy. If you do not agree, please do not use our platform.</p>
        
        <h2>2. User Responsibilities</h2>
        <ul>
            <li>You are responsible for maintaining the confidentiality of your account information.</li>
            <li>You agree to use Scriptores in compliance with all applicable laws and regulations.</li>
            <li>You will not post content that is unlawful, harmful, or infringes on the rights of others.</li>
        </ul>
        
        <h2>3. Content Ownership</h2>
        <p>All content you submit remains your property. However, by submitting content to Scriptores, you grant us a non-exclusive, worldwide, royalty-free license to use, display, and distribute your content on our platform.</p>
        
        <h2>4. Termination</h2>
        <p>We reserve the right to terminate or suspend your account at any time for violations of these terms or for any reason deemed necessary.</p>
        
        <h2>5. Changes to Terms</h2>
        <p>We may update these Terms of Services from time to time. We will notify you of any changes by posting the new terms on this page.</p>
        
        <h2>6. Contact Us</h2>
        <p>If you have any questions about these Terms of Services, please contact us at <?php echo Env('email'); ?>.</p>    

    </div>
</body>
</html>