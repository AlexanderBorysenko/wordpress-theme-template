import { Plugin } from 'vite';
import * as path from 'path';
import * as fgLib from 'fast-glob';
import * as sassLib from 'sass';
import { promises as fs } from 'fs';
import { createHash } from 'crypto';

// Используем либо fgLib.default, либо сам fgLib, если default не задан
const fg = (fgLib as any).default || fgLib;
// Аналогично для sass – пытаемся использовать default экспорт, если он есть
const sassCompiler = (sassLib as any).default || sassLib;

export default function ComponentsStylesChunkBuilder(): Plugin {
    return {
        name: 'vite-plugin-components-css',

        // Хук buildStart: регистрируем все найденные SCSS файлы для наблюдения
        async buildStart() {
            const pattern = 'components/**/*.scss';
            const files: string[] = await fg(pattern, { absolute: true });

            // Регистрируем каждый файл для отслеживания
            for (const filePath of files) {
                this.addWatchFile(filePath);
            }
        },

        async generateBundle(_, bundle) {
            const pattern = 'components/**/*.scss';
            let files: string[] = await fg(pattern, { absolute: true });

            // Оставляем только файлы с одним расширением (без приставок по типу .wp-admin и так далее)
            files = files.filter(file => path.basename(file).split('.').length === 2);


            let combinedCSS = '';

            const variablesPath = path.resolve(process.cwd(), 'styles/preprocessor-variables.scss');
            const normalizedVariablesPath = variablesPath.replace(/\\/g, '/');
            const prelude = `@use 'file:///${normalizedVariablesPath}' as *;`;

            for (const filePath of files) {
                const fileName = path.basename(filePath);
                try {
                    const content = await fs.readFile(filePath, 'utf8');
                    const contentWithPrelude = prelude + '\n' + content;
                    const result = sassCompiler.compileString(contentWithPrelude, {
                        style: 'compressed',
                        url: new URL(`file://${filePath}`),
                    });
                    const componentName = fileName.replace('.scss', '');
                    // Обрамляем CSS каждого файла комментариями с именем компонента
                    combinedCSS += `/*${componentName}*/${result.css}/*${componentName}*/\n`;
                } catch (err) {
                    this.error(`Error processing SCSS file ${filePath}: ${err}`);
                }
            }

            // Эмитируем обычный файл компонентов
            const hash = createHash('md5').update(combinedCSS).digest('hex').slice(0, 8);
            this.emitFile({
                type: 'asset',
                fileName: `components-${hash}.css`,
                source: combinedCSS,
            });

            // Генерируем wp-admin версию: оборачиваем весь CSS в правило
            const wpAdminInput = `.wp-block-post-content [data-type*='carbon-fields/'] .cf-block__preview { ${combinedCSS} }`;
            let wpAdminCSS = '';
            try {
                const result = sassCompiler.compileString(wpAdminInput, {
                    style: 'compressed',
                });
                wpAdminCSS = result.css;
            } catch (err) {
                this.error(`Error processing wp-admin SCSS wrapper: ${err}`);
            }

            const hashWp = createHash('md5').update(wpAdminCSS).digest('hex').slice(0, 8);
            this.emitFile({
                type: 'asset',
                fileName: `wp-admin-components-${hashWp}.css`,
                source: wpAdminCSS,
            });
        }
    };
}