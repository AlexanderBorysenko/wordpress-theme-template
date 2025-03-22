<?php
use Carbon_Fields\Field;
use ThemeCore\ThemeModules\CarbonFields\Managers\ThemeOptionsManager;

ThemeOptionsManager::createChildContainer('Contacts', 'Contact Forms')
    ->add_fields([
        Field::make('text', 'crb_telegram_bot_key', 'Telegram Bot Key')->set_width(50)
            ->set_help_text('Enter the Telegram bot key for sending messages.'),
        Field::make('text', 'crb_telegram_chat_id', 'Telegram Chat ID')->set_width(50)
            ->set_help_text('Enter the Telegram chat ID for sending messages.'),

        Field::make('complex', 'crb_contact_emails', 'Email Addresses for Form Submissions')
            ->add_fields('email', [
                Field::make('text', 'email', 'Email Address')
                    ->set_help_text('Enter an email address to receive form submissions.'),
            ])
            ->set_layout('tabbed-horizontal')
            ->set_header_template('<%- email %>'),

        Field::make('text', 'crb_thank_you_page_link', 'Thank You Page Link')
            ->set_help_text('Enter the link to the page that the user will be redirected to after submitting the form.'),
    ]);