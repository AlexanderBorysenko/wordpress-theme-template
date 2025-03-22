import { defineConfig } from 'vite';
import sassGlobImports from 'vite-plugin-sass-glob-import';
import ViteRestart from './library/dev/vite/ViteRestart';
import viteScssGlobUsePlugin from './library/dev/vite/viteScssGlobUsePlugin';
import postCssSortMediaQueries from 'postcss-sort-media-queries';
import postcssCombineMediaQuery from 'postcss-combine-media-query';
import * as path from 'path';

export default defineConfig({
	resolve: {
		alias: {
			'~': path.resolve(__dirname, './'),
			'~theme': path.resolve(__dirname, '../')
		},
	},
	base: './',
	build: {
		outDir: 'build',
		assetsDir: './',
		rollupOptions: {
			input:
				[
					'asset.app.ts',
					'asset.wp-admin.ts',
					'asset.app.scss',
					'asset.wp-admin.scss',
				]
		},
		minify: 'terser',
		terserOptions: {
			mangle: false,
			keep_classnames: true,
			keep_fnames: true
		}
	},
	plugins: [
		sassGlobImports(),
		viteScssGlobUsePlugin(),
		ViteRestart({
			restart: ['./styles/**/*', './components/**/*', '../**/*.jsx']
		})
	],
	css: {
		preprocessorOptions: {
			scss: {
				additionalData: `@use '~/styles/library-provider' as *;`
			},
			postcss: {
				plugins: [
					postCssSortMediaQueries({ sort: 'desktop-first' }),
					postcssCombineMediaQuery()
				]
			}
		}
	}
});
