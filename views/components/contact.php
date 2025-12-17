<?php
require_once __DIR__ . '/../../utils/db_conn.php';
require_once __DIR__ . "/../../mail.php";

$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    

        $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
        // Validation
    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    }
     else {

        $to = $_ENV['email'];

            // Escape user input for HTML email
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safeSubject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

            $emailSubject = "Contact Form: {$safeSubject}";

            $body = "
            <h1>Contact Us – Journal</h1>
            <p><strong>Name:</strong> {$safeName}</p>
            <p><strong>Email:</strong> {$safeEmail}</p>
            <p><strong>Subject:</strong> {$safeSubject}</p>
            <p><strong>Message:</strong><br>{$safeMessage}</p>
        ";

            $altbody = "
Contact Form – Journal

    Name: {$name}
Email: {$email}
Subject: {$subject}
Message:
{$message}
        ";

            $sent = sendMailToNewAdmin(
            $to,
            $safeName,
            $emailSubject,
            $body,
            $altbody
        );

            if ($sent) {
            $message_sent = true;
            header("Location: http://localhost/Journal/main.php?page=contact&status=success");
        } else {
            $error_message = "Failed to send message. Please check your email configuration or try again later.";
        }
    }
}else{?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php baseurl("css/bootstrap.min.css") ?>" />
    <link rel="stylesheet" href="<?php baseurl("css/style.css") ?>" />
    <title>Contact Us - Journal</title>
    <style>
        body {
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-header {
            padding: 2rem;
            text-align: center;
            color: var(--bs-primary);
            margin-bottom: 3rem;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--bs-primary);
        }

        .contact-header p {
            font-size: 1.1rem;
            opacity: 0.95;
            color: #666;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .contact-info {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .info-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: flex-start;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background-color: #ebebebff;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .info-content h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
            margin-top: 0;
        }

        .info-content p {
            color: #666;
            margin: 0;
            line-height: 1.5;
        }

        .info-content a {
            color: var(--bs-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .info-content a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e3e3e3;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            background-color: var(--bs-primary);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            opacity: 0.9;
            box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e3e3e3;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bs-primary);
            text-decoration: none;
            font-size: 1.2rem;
            transition: background 0.3s, color 0.3s;
        }

        .social-link:hover {
            background-color: var(--bs-primary);
            color: white;
        }

        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
            }

            .contact-header h1 {
                font-size: 2rem;
            }

            .contact-info,
            .contact-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="contact-container">
        <div class="contact-header">
            <h1>Get In Touch</h1>
            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>

        <div class="contact-content">
            <!-- Contact Information -->
            <div class="contact-info">
                <h2 style="color: #333; margin-bottom: 2rem;">Contact Information</h2>

                <div class="info-item">
                    <div class="info-icon">📧</div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p>
                            <a href="mailto:<?php echo Env('email') ?? 'support@journal.app'; ?>">
                                <?php echo Env('email') ?? 'support@journal.app'; ?>
                            </a>
                        </p>
                        <p style="font-size: 0.9rem; color: #999; margin-top: 0.3rem;">We typically respond within 24
                            hours</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <h3>Phone</h3>
                        <p><a href="tel:+1234567890">+1 (234) 567-890</a></p>
                        <p style="font-size: 0.9rem; color: #999; margin-top: 0.3rem;">Monday - Friday, 9am - 5pm</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <h3>Address</h3>
                        <p>Banamali Naskar Road, Dhopapara, Parnasree Pally, Behala, Kolkata, West Bengal, 700060</p>
                    </div>
                </div>


                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">f</a>
                    <a href="#" class="social-link" title="Twitter">𝕏</a>
                    <a href="#" class="social-link" title="LinkedIn">in</a>
                    <a href="#" class="social-link" title="Instagram">📷</a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form">
                <h2 style="color: #333; margin-bottom: 2rem;">Send us a Message</h2>

                <?php if ($message_sent): ?>
                    <div class="alert alert-success">
                        <strong>Success!</strong> Thank you for your message. We'll get back to you soon.
                    </div>
                    <script>
                        setTimeout(() => {
                            document.querySelector('.alert').style.display = 'none';
                        }, 5000);
                    </script>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <strong>Error!</strong> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="http://localhost/Journal/views/components/contact.php">
                    <div class="form-group">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control"
                            placeholder="What is this about?" required>
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-control"
                            placeholder="Tell us more about your inquiry..." required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Send Message</button>
                </form>

                <div
                    style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e3e3e3; text-align: center; color: #666; font-size: 0.9rem;">
                    <p>We respect your privacy. Your information will not be shared with third parties.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php baseurl("js/bootstrap.bundle.min.js") ?>"></script>
</body>

</html>
<?php } ?>