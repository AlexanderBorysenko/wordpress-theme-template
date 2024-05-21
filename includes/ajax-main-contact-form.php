<?php
// ajax action for main contact form
add_action('wp_ajax_main_contact_form_submit', 'main_contact_form_submit');
add_action('wp_ajax_nopriv_main_contact_form_submit', 'main_contact_form_submit');

function main_contact_form_submit()
{
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Please enter your name';
    }

    if (empty($subject)) {
        $errors['subject'] = 'Please enter a subject';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Please enter your phone number';
    }

    if (empty($email)) {
        $errors['email'] = 'Please enter your email';
    }

    if (empty($message)) {
        $errors['message'] = 'Please enter a message';
    }

    if (!empty($errors)) {
        return wp_send_json_error($errors);
    }

    $to = carbon_get_theme_option('crb_email_to');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    $subject = 'New lead from ' . $name . ' - ' . $subject . '';
    $body = '<p><strong>Name:</strong> ' . $name . '</p>';
    $body .= '<p><strong>Subject:</strong> ' . $subject . '</p>';
    $body .= '<p><strong>Phone:</strong> ' . $phone . '</p>';
    $body .= '<p><strong>Email:</strong> ' . $email . '</p>';
    $body .= '<p><strong>Message:</strong> ' . $message . '</p>';

    try {
        wp_mail($to, $subject, $body, $headers);
    } catch (Exception $e) {
        return wp_send_json_error('An error occurred while sending the message. Please try again later.');
    }

    wp_send_json_success([
        'redirect' => carbon_get_theme_option('crb_thank_you_page_link'),
    ]);

    exit;
}
