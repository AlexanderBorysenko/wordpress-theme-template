<?php
namespace DevTools\Tools;

use DevTools\Helpers\MediaHelper;

class ImagesDownloader extends Tool
{
    protected $toolName = 'Images Downloader';
    protected $ajaxAction = 'tools_upload_from_url';

    public function view()
    {
        ?>
        <div class="wrap">
            <h1>Upload Media from URLs</h1>
            <form id="media-upload-form" method="post" action="">
                <p>
                    Pass the media URLs separated by commas or linebreaks in the textarea below and click the "Upload" button to
                    download and add them to the media gallery.
                </p>
                <textarea id="media-urls" name="media-urls" rows="10" cols="50"
                    placeholder="Enter URLs separated by commas or linebreaks"></textarea>
                <button type="button" class="button button-primary" id="upload-media-button">Upload</button>
            </form>
            <div id="upload-results"></div>
        </div>
        <script>
            jQuery(document).ready(function ($)
            {
                // on submit
                $('#upload-media-button').on('click', function ()
                {
                    var urls = $('#media-urls').val();
                    var urlsArray = urls.split(/[\s,]+/);
                    urlsArray.forEach(function (url)
                    {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'tools_upload_from_url',
                                url: url
                            },
                            success: function (response)
                            {
                                $('#upload-results').append(response.data.message);
                            },
                            error: function (response)
                            {
                                $('#upload-results').append('<p>' + response.responseJSON.data
                                    .message +
                                    '</p>');
                            }
                        });
                    });
                });
            });
        </script>
        <?php
    }

    public function ajax()
    {
        if (!isset($_POST['url'])) {
            wp_send_json_error('URL is required');
        }

        $url = sanitize_text_field($_POST['url']);

        $mediaHelper = new MediaHelper();

        $result = $mediaHelper->uploadMediaFromUrl($url);

        if ($result['attachmentId']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
}
