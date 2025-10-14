<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recipient email from client portfolio
    $mail_to = "amalragc@gmail.com";
    // Sender Data (newsletter forms may only have name/email)
    $name = isset($_POST["name"]) ? str_replace(array("\r", "\n"), array(" ", " "), strip_tags(trim($_POST["name"]))) : '';
    $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : '';
    $subject = isset($_POST["subject"]) ? trim($_POST["subject"]) : '';
    $message = isset($_POST["message"]) ? trim($_POST["message"]) : '';

    // Determine form type: full contact vs newsletter
    $is_newsletter = empty($phone) && empty($subject) && empty($message) && !empty($name) && !empty($email);

    if ($is_newsletter) {
        if (empty($name) or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo "Please provide a valid name and email.";
            exit;
        }
        $subject = "Newsletter Signup";
        $content = "A new user subscribed to the newsletter.\n\n";
        $content .= "Name: $name\n";
        $content .= "Email: $email\n";
    } else {
        if (empty($name) or !filter_var($email, FILTER_VALIDATE_EMAIL) or empty($phone) or empty($subject) or empty($message)) {
            http_response_code(400);
            echo "Please complete the form and try again.";
            exit;
        }
        $content = "Name: $name\n";
        $content .= "Email: $email\n\n";
        $content .= "Phone: $phone\n";
        $content .= "Message:\n$message\n";
    }

    // email headers.
    $headers = "From: $name <$email>";
    // Send the email.
    $success = mail($mail_to, $subject, $content, $headers);
    if ($success) {
        # Set a 200 (okay) response code.
        http_response_code(200);
        echo "Thank You! Your message has been sent.";
    } else {
        # Set a 500 (internal server error) response code.
        http_response_code(500);
        echo "Oops! Something went wrong, we couldn't send your message.";
    }
} else {
    # Not a POST request, set a 403 (forbidden) response code.
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
