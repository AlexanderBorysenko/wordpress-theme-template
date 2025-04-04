<?php
add_action('init', function () {
	$build_dir = __DIR__ . "/build";

	foreach (scandir($build_dir) as $result) {
		$block_location = "{$build_dir}/{$result}";

		if (!is_dir($block_location) || '.' === $result || '..' === $result) {
			continue;
		}

		register_block_type($block_location);
	}
});