<?php

use ThemeCore\Handlers\FormAjaxHandler\FormAjaxHandler;

$mainContactFormHandler = new FormAjaxHandler('main_contact_form_submit');

$mainContactFormHandler
    ->addField('name', ['required'])
    ->addField('service', ['required'])
    ->addField('email', rules: [
        'required',
        'email',
    ])
    ->addField('phone', [
        'required',
        'tel',
    ])
    ->addField('message', [])
    // ->addField('file', ['required'])
    ->setFormTitle(function (array $fields) {
        return "{$fields['name']} Message";
    })
    ->disableRecaptcha()
    ->init();

add_action('carbon_fields_boot_completed', function () use ($mainContactFormHandler) {
    // Set the receiver emails
    $mainContactFormHandler->setReceiverEmails(carbon_get_theme_option('crb_contact_form_emails'));

    // Redirect to the thank you page
    $mainContactFormHandler->setRedirect(carbon_get_theme_option(addLangSuffix('crb_thank_you_page_link')));
});