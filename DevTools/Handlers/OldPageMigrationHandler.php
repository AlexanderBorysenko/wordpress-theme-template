<?php
namespace DevTools\Handlers;

use DevTools\Helpers\DOMHelper;
use DevTools\Helpers\MediaHelper;

class OldPageMigrationHandler
{
    private \DOMDocument $sourceDom;
    private \DOMXPath $sourceDomXpath;
    public $output = [
        'source_url' => '',
        'new_url' => '',
        'redirect' => '',
        'type' => '',
        'logs' => ''
    ];

    public function __construct(string $url)
    {
        $this->output['source_url'] = $url;

        $sourceHtml = file_get_contents($url);
        if (!$sourceHtml) {
            $this->output['logs'] .= "<div class='notice notice-error is-dismissible'><p>Could not fetch the page content</p></div>";
        }
        $sourceDom = new \DOMDocument();
        @$sourceDom->loadHTML($sourceHtml);

        $this->sourceDom = $sourceDom;
        $this->sourceDomXpath = new \DOMXPath($sourceDom);
    }

    public function handle()
    {
        $type = $this->getPageType();
        $this->output['type'] = $type;

        if ($type === 'blog') {
            $newPostId = $this->handlePage([
                'post_type' => 'blog',
                'content_container' => '//div[contains(@class, "entry-content")]'
            ]);
            if ($newPostId) {
                $this->create301RedirectionRule(parse_url($this->output['source_url'], PHP_URL_PATH), $this->output['new_url']);
            }
        } elseif ($type === 'page') {
            $this->handlePage([
                'post_type' => 'page',
            ]);
        } else {
            $this->output['logs'] .= "<div class='notice notice-error is-dismissible'><p>Could not handle this page type</p></div>";
        }

        return $this->output;
    }

    private function handlePage(array $args = []): ?int
    {
        // get slug
        $slug = basename(parse_url($this->output['source_url'], PHP_URL_PATH));

        // check if the post already exists
        $existingPost = get_page_by_path($slug, OBJECT, 'blog');
        if ($existingPost) {
            $this->output['logs'] .= "<div class='notice notice-warning is-dismissible'><p>Post already exists: {$existingPost->ID}</p></div>";
            return null;
        }

        // get post title
        $metaTitle = $this->sourceDomXpath->query('//title')->item(0)->textContent;
        $pageTitle = $this->sourceDomXpath->query('//h1')->item(0)->textContent;
        $title = $pageTitle ?: $metaTitle;

        // get post content part

        $post_content = '';

        if (isset($args['content_container'])):
            $postContent = $this->sourceDomXpath->query($args['content_container'])->item(0);
            if (!$postContent) {
                $this->output['logs'] .= "<div class='notice notice-error is-dismissible'><p>Could not find post content</p></div>";
                return null;
            }

            // make a copy of the post content without the parent div
            $postContentDom = new \DOMDocument();
            foreach ($postContent->childNodes as $childNode) {
                $postContentDom->appendChild($postContentDom->importNode($childNode, true));
            }

            // upload related files
            $this->handlePostContentImages($postContentDom);

            foreach ($postContentDom->childNodes as $childNode) {
                $post_content .= $childNode->ownerDocument->saveHTML($childNode);
            }
        endif;

        // create post 
        $postId = wp_insert_post([
            'post_title' => $title,
            'post_content' => $post_content,
            'post_status' => 'publish',
            'post_type' => $args['post_type'] ?? 'page',
            'post_name' => $slug,
        ]);
        if (is_wp_error($postId)) {
            $this->output['logs'] .= "<div class='notice notice-error is-dismissible'><p>{$postId->get_error_message()}</p></div>";
            return null;
        }

        // set excerpt
        $excerpt = $this->sourceDomXpath->query('//meta[@name="description"]')->item(0)->getAttribute('content');
        if ($excerpt) {
            update_post_meta($postId, '_excerpt', $excerpt);
        }

        // set featured image
        $ogImage = $this->sourceDomXpath->query('//meta[@property="og:image"]')->item(0);
        if ($ogImage) {
            $attachment = MediaHelper::uploadMediaFromUrl($ogImage->getAttribute('content'));
            $this->output['logs'] .= $attachment['message'];
            if ($attachment['attachmentId']) {
                set_post_thumbnail($postId, $attachment['attachmentId']);
            }
        }

        // set post meta time
        $modifiedTimeMeta = $this->sourceDomXpath->query('//meta[@property="article:modified_time"]')->item(0);
        if ($modifiedTimeMeta) {
            $modifiedTime = $modifiedTimeMeta->getAttribute('content');
            wp_update_post([
                'ID' => $postId,
                'post_modified' => $modifiedTime,
                'post_modified_gmt' => $modifiedTime,
            ]);
        }

        // set yoast meta title
        $yoastTitle = $this->sourceDomXpath->query('//meta[@property="og:title"]')->item(0);
        if ($yoastTitle) {
            update_post_meta($postId, '_yoast_wpseo_title', $yoastTitle->getAttribute('content'));
        }

        // set yoast meta description
        $yoastDescription = $this->sourceDomXpath->query('//meta[@property="og:description"]')->item(0);
        if ($yoastDescription) {
            update_post_meta($postId, '_yoast_wpseo_metadesc', $yoastDescription->getAttribute('content'));
        }

        $this->output['new_url'] = get_permalink($postId);

        return $postId;
    }

    private function handlerOgImage(int $postId): void
    {
        $ogImage = $this->sourceDomXpath->query('//meta[@property="og:image"]')->item(0);
        if ($ogImage) {
            $attachment = MediaHelper::uploadMediaFromUrl($ogImage->getAttribute('content'));
            $this->output['logs'] .= $attachment['message'];
            if ($attachment['attachmentId']) {
                set_post_thumbnail($postId, $attachment['attachmentId']);
            }
        }
    }

    private function handlePostContentImages(\DOMDocument &$postContentDom): array
    {
        $imagesSrc = DOMHelper::getImagesSrc($postContentDom);

        $attachments = [];
        foreach ($imagesSrc as $originalImageSrc) {
            $attachment = MediaHelper::uploadMediaFromUrl($originalImageSrc);

            $this->output['logs'] .= $attachment['message'];

            if (!$attachment['attachmentId'])
                continue;

            $attachments[] = $attachment['attachmentId'];

            $newImageSrc = wp_get_attachment_image_url($attachment['attachmentId'], 'full');

            $postContentDom = DOMHelper::updateImagesSrc($postContentDom, $originalImageSrc, $newImageSrc);
        }

        return $attachments;
    }

    private function getPageType(): string
    {
        $bodyClass = DOMHelper::getBodyClass($this->sourceDom);

        if (str_contains($bodyClass, 'single-post')) {
            return 'blog';
        } elseif (str_contains($bodyClass, 'page')) {
            return 'page';
        } elseif (str_contains($bodyClass, 'single-project')) {
            return 'project';
        } else {
            $this->output['logs'] .= "<div class='notice notice-error is-dismissible'><p>Could not resolve valid page type</p></div>";
            return 'other';
        }
    }

    private function create301RedirectionRule($source_path, $destination_path)
    {
        include_once WP_PLUGIN_DIR . '/redirection/models/group.php';
        \Red_Item::create([
            'url' => $source_path,
            'match_type' => 'url',
            'action_code' => 301,
            'action_type' => 'url',
            'action_data' => ['url' => $destination_path],
            'group_id' => 1,
        ]);

        $this->output['redirect'] = "301 $source_path -> $destination_path";
    }
}