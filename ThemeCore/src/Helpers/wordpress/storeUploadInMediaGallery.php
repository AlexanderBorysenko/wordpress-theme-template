<?php
function storeUploadInMediaGallery($file)
{
    // Validate the uploaded file
    if (!isset($file) || !isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return new WP_Error('file_not_found', 'No valid file was uploaded.');
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    // Use wp_handle_upload to process the uploaded file.
    $uploaded_file = wp_handle_upload($file, ['test_form' => false]);

    if (isset($uploaded_file['error'])) {
        return new WP_Error('upload_failed', $uploaded_file['error']);
    }

    $file_name   = basename($uploaded_file['file']);
    $destination = $uploaded_file['file'];

    $upload_dir = wp_upload_dir();

    $file_type  = wp_check_filetype($file_name, null);
    $attachment = [
        'guid'           => trailingslashit($upload_dir['url']) . $file_name,
        'post_mime_type' => $file_type['type'],
        'post_title'     => sanitize_file_name($file_name),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    $attach_id = wp_insert_attachment($attachment, $destination);

    if (is_wp_error($attach_id)) {
        return $attach_id;
    }

    $attachment_data = wp_generate_attachment_metadata($attach_id, $destination);
    wp_update_attachment_metadata($attach_id, $attachment_data);

    return $attach_id;
}