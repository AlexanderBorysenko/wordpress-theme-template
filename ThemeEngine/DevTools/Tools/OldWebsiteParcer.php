<?php
namespace DevTools\Tools;

use DevTools\Handlers\OldPageMigrationHandler;
use DevTools\Tools\Tool;

class OldWebsiteParcer extends Tool
{
    protected $toolName = 'Old Website Parcer';
    protected $ajaxAction = 'tools_old_website_parcer';

    public function view()
    {
        ?>
        <div class="wrap">
            <h1>Auto parce pages structure from old website</h1>
            <form id="pages-parce-form" method="post" action="">
                <p>
                    Pass the old website URL and click the "Parce" button to get the pages structure.
                </p>
                <textarea id="page-urls" name="page-urls" rows="10" cols="50"
                    placeholder="Enter URLs separated by commas or linebreaks"></textarea>
                <button type="button" class="button button-primary" id="parce-button">Parce</button>
            </form>
            <p id="progress-disaplay">0/0</p>
            <table id="upload-results" class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Original</th>
                        <th>New</th>
                        <th>Redirect</th>
                        <th>Type</th>
                        <th>Logs</th>
                    </tr>
                </thead>
                <tbody id="upload-results-body"></tbody>
            </table>
        </div>
        <script>
            jQuery(document).ready(function ($)
            {
                // on submit
                $('#parce-button').on('click', function ()
                {
                    var urls = $('#page-urls').val();
                    var urlsArray = urls.split(/[\s,]+/);
                    var total = urlsArray.length;
                    var current = 0;
                    $('#progress-disaplay').text(current + '/' + total);
                    urlsArray.forEach(function (url)
                    {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'tools_old_website_parcer',
                                url: url
                            },
                            success: function (response)
                            {
                                $('#upload-results-body').append(
                                    '<tr><td>' + response.data.source_url + '</td><td>' + response.data.new_url +
                                    '</td><td>' + response.data.redirect + '</td><td>' + response.data.type +
                                    '</td><td><details>' + response.data.logs + '</details></td></tr>'
                                );
                                $('#progress-disaplay').text(current++ + '/' + total);
                            },
                            error: function (response)
                            {
                                let error = ''
                                if (response.responseJSON)
                                {
                                    error = response.responseJSON.data.message;
                                } else
                                {
                                    error = response.responseText;
                                }

                                $('#upload-results-body').append(
                                    '<tr><td>' + url + '</td><td></td><td></td><td></td><td><details>' + error + '</details></td></tr>'
                                );
                                $('#progress-disaplay').text(current++ + '/' + total);
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
        if (!isset($_POST['url']) || !current_user_can('manage_options')) {
            return wp_send_json_error('Invalid request');
        }

        $url = $_POST['url'];

        $handler = new OldPageMigrationHandler($url);

        $result = $handler->handle();
        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error("Error while handling the '$url' page.");
        }
    }
}