import * as path from 'path';
import type { Plugin } from 'vite';
import * as micromatch from 'micromatch';

interface Options {
	/**
	 * Enable glob support for watcher (it's disabled by Vite, but add this plugin will turn it on by default)
	 *
	 * @default true
	 */
	glob?: boolean;
	/**
	 * @default 500
	 */
	delay?: number;
	/**
	 * Array of files to watch, changes to those file will trigger a server restart
	 */
	restart?: string | string[];
	/**
	 * Array of files to watch, changes to those file will trigger a client full page reload
	 */
	reload?: string | string[];
}

let i = 0;

function toArray<T>(arr: T | T[] | undefined): T[] {
	if (!arr) return [];
	if (Array.isArray(arr)) return arr;
	return [arr];
}

function VitePluginRestart(options: Options = {}): Plugin {
	const { delay = 500, glob: enableGlob = true } = options;

	let root = process.cwd();
	let reloadGlobs: string[] = [];
	let restartGlobs: string[] = [];

	let timerState = 'reload';
	let timer: ReturnType<typeof setTimeout> | undefined;

	function clear() {
		clearTimeout(timer);
	}
	function schedule(fn: () => void) {
		clear();
		timer = setTimeout(fn, delay);
	}

	return {
		name: `vite-plugin-restart:${i++}`,
		apply: 'serve',
		config(c) {
			if (!enableGlob) return;
			if (!c.server) c.server = {};
			if (!c.server.watch) c.server.watch = {};
			c.server.watch.disableGlobbing = false;
		},
		configResolved(config) {
			root = config.root;

			restartGlobs = toArray(options.restart).map(i =>
				path.posix.join(root, i)
			);
			reloadGlobs = toArray(options.reload).map(i =>
				path.posix.join(root, i)
			);
		},
		configureServer(server) {
			server.watcher.add([...restartGlobs, ...reloadGlobs]);
			server.watcher.on('add', handleFileChange);
			// server.watcher.on('change', handleFileChange);
			// server.watcher.on('unlink', handleFileChange);

			function handleFileChange(file: string) {
				if (micromatch.isMatch(file, restartGlobs)) {
					timerState = 'restart';
					server.restart();
				} else if (
					micromatch.isMatch(file, reloadGlobs) &&
					timerState !== 'restart'
				) {
					timerState = 'reload';
					schedule(() => {
						server.ws.send({ type: 'full-reload' });
						timerState = '';
					});
				}
			}
		}
	};
}

export default VitePluginRestart;
