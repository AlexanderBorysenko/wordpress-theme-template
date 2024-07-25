<?php
namespace DevTools\Helpers;

class MediaHelper
{
    /**
     * Upload media from a URL.
     *
     * @param string $url The URL of the source image.
     * @return array Result message and attachment ID.
     */
    public static function uploadMediaFromUrl($url)
    {
        $attachmentName = sanitize_file_name(basename($url));

        // Check if the file already exists
        $existingAttachmentId = self::getAttachmentIdByFileName($attachmentName);
        if ($existingAttachmentId) {
            return [
                "message" => "<div class='notice notice-warning is-dismissible'><p><strong>File {$attachmentName} already exists. Id: {$existingAttachmentId}</strong></p></div>",
                "attachmentId" => $existingAttachmentId
            ];
        }

        // Upload the file
        $attachmentId = self::uploadFile($url);
        if (is_wp_error($attachmentId)) {
            return [
                "message" => "<div class='notice notice-error is-dismissible'><p><strong>{$attachmentId->get_error_message()}</strong></p></div>",
                "attachmentId" => null
            ];
        }

        return [
            "message" => "<div class='notice notice-success is-dismissible'><p><strong>File {$attachmentName} uploaded successfully. ID: {$attachmentId}</strong></p></div>",
            "attachmentId" => $attachmentId
        ];
    }

    /**
     * Get attachment ID by file name.
     *
     * @param string $fileName The file name.
     * @return int|false The attachment ID or false if not found.
     */
    private static function getAttachmentIdByFileName($fileName)
    {
        $query = new \WP_Query([
            'post_type' => 'attachment',
            'meta_query' => [
                [
                    'key' => '_wp_attached_file',
                    'value' => $fileName,
                    'compare' => 'LIKE'
                ]
            ]
        ]);

        if ($query->have_posts()) {
            return $query->posts[0]->ID;
        }

        return false;
    }

    /**
     * Upload a file from a URL.
     *
     * @param string $url The URL of the source image.
     * @return array|\WP_Error The upload result or WP_Error on failure.
     */
    private static function uploadFile($url)
    {
        // Download file to temporary location
        $tempFile = download_url($url);

        if (is_wp_error($tempFile)) {
            return $tempFile;
        }

        $fileName = sanitize_file_name(basename($url));

        // Move the temporary file to the uploads directory
        $fileArray = [
            'name' => $fileName,
            'tmp_name' => $tempFile
        ];

        $uploadResult = media_handle_sideload($fileArray, 0);

        @unlink($fileArray['tmp_name']);

        return $uploadResult;
    }
}
