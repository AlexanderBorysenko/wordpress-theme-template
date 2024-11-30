import { defineConfig } from 'vite';
import sassGlobImports from 'vite-plugin-sass-glob-import';
import ViteRestart from './ViteRestart';
import postCssSortMediaQueries from 'postcss-sort-media-queries';
import postcssCombineMediaQuery from 'postcss-combine-media-query';

// Helper function to get all .scss files in a folder
function getScssEntries(dir) {
	const files = fs.readdirSync(dir);
	const entries = {};
	files.forEach(file => {
		if (file.endsWith('.scss') && (file.match(/\./g) || []).length === 1) {
			const name = file.replace('.scss', ''); // Filename without extension
			entries[name] = path.resolve(dir, file);
		}
	});
	return entries;
}

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
						if (match.includes('data:image')) return;

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
				app: './entrypoints/app.ts',
				'wp-admin': './entrypoints/wp-admin.ts'
			}
		}
	},
	plugins: [
		cssUrlImagesLoader(),
		sassGlobImports(),
		ViteRestart({
			restart: ['./styles/**/*', './components/**/*', '../**/*.jsx']
		})
	],
	css: {
		postcss: {
			plugins: [
				{
					name: 'scss-multiple-entry',
					config(config, env) {
						const scssDir = path.resolve(__dirname, 'src/styles');
						const scssEntries = getScssEntries(scssDir);

						config.build = {
							rollupOptions: {
								input: scssEntries // Add .scss files as entry points
							},
							assetsDir: '', // Places all CSS files in the root output directory
							cssCodeSplit: true, // Generate separate CSS files
							outDir: 'dist/styles' // Customize output directory
						};

						return config;
					}
				},
				postCssSortMediaQueries({
					sort: 'desktop-first'
				}),
				postcssCombineMediaQuery()
			]
		}
	}
});
