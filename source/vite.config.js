import { defineConfig } from 'vite';
import sassGlobImports from 'vite-plugin-sass-glob-import';
import ViteRestart from './ViteRestart';
import postCssSortMediaQueries from 'postcss-sort-media-queries';
import postcssCombineMediaQuery from 'postcss-combine-media-query';
import { cssUrlImagesLoader } from './vite/cssUrlImagesLoader';
import path from 'path';

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
				postCssSortMediaQueries({
					sort: 'desktop-first'
				}),
				postcssCombineMediaQuery()
			]
		}
	}
});
