<?php
/**
 * This file defines the Carbon Fields theme options for the Contacts section.
 * It sets up the fields for general contact information, address, contact form settings, and social media links.
 *
 * @package Carbon_Theme_Options
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('theme_options', 'Contacts')
    ->set_icon('dashicons-email')
    ->add_tab('General', [
        Field::make('text', 'crb_email', 'Email')->set_default_value('mail@example.com'),
        Field::make('text', 'crb_phone', 'Phone')->set_default_value('(000) 000-0000'),
    ])
    ->add_tab('Address', [
        Field::make('text', 'crb_address', 'Address')
            ->set_default_value('273104 RANGE ROAD 293'),
    ])
    ->add_tab('Contact Form', [
        Field::make('text', 'crb_contact_page_link', 'Contact Us Page Link')->set_default_value('/contact-us'),
        Field::make('text', 'crb_email_to', 'Send Emails To')->set_default_value('mail@example.com'),
        Field::make('text', 'crb_thank_you_page_link', 'Thank You Page Link')->set_default_value('/thank-you'),
    ])
    ->add_tab('Social Media', [
        Field::make('text', 'crb_facebook_link', 'Facebook Link')->set_default_value(''),
        Field::make('text', 'crb_instagram_link', 'Instagram Link')->set_width(50)->set_default_value(''),
        Field::make('text', 'crb_instagram_name', 'Instagram Name')->set_width(50)->set_default_value('@'),
        Field::make('text', 'crb_linkedin_link', 'LinkedIn Link')->set_default_value(''),
        Field::make('text', 'crb_twitter_link', 'Twitter Link')->set_default_value(''),
        Field::make('text', 'crb_youtube_link', 'YouTube Link')->set_default_value(''),
    ]);