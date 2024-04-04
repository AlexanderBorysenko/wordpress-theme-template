import { defineConfig } from 'vite';
import sassGlobImports from 'vite-plugin-sass-glob-import';
import ViteRestart from './ViteRestart';
import cssPurge from 'vite-plugin-purgecss';

// plugin which finds file paths in scss, moves them to the build folder and replaces the url() with the new path
const cssUrlImagesLoader = () => {
	return {
		name: 'css-url-images-loader',
		transform(code, id) {
			if (id.endsWith('.scss')) {
				const regex = /url\((.*?)\)/g;
				const matches = code.match(regex);
				if (matches) {
					matches.forEach(match => {
						const url = match
							.replace('url(', '')
							.replace(')', '')
							.replace(/['"]+/g, '');
						const filename = url.split('/').pop();
						const fileExtension = filename.split('.').pop();

						if (
							['svg', 'png', 'jpg', 'jpeg'].includes(
								fileExtension
							)
						) {
							const newUrl = `url(../asset-images/${filename})`;
							code = code.replace(match, newUrl);
						} else {
							const newUrl = `url(.${filename})`;
							code = code.replace(match, newUrl);
						}
					});
				}
			}
			return code;
		}
	};
};

export default defineConfig({
	build: {
		outDir: 'build',
		assetsDir: './',
		rollupOptions: {
			input: {
				app: './app.ts',
				'wp-admin': './wp-admin.ts'
			}
		}
	},
	plugins: [
		// cssUrlImagesLoader(),
		sassGlobImports(),
		ViteRestart({
			restart: ['./styles/**/*', './components/**/*', '../**/*.jsx']
		}),
		cssPurge({
			content: ['../**/*.php', '../**/*.jsx', '../**/*.html']
		})
	]
});
