import type { Plugin } from 'vite';
import * as path from 'path';
import { glob } from 'glob';

const viteScssGlobUsePlugin = (options: { additionalData?: string } = {}): Plugin => {
    return {
        name: 'vite-scss-glob-use-plugin',
        transform(code, id) {
            if (!id.endsWith('.scss')) return;

            // Регулярное выражение для поиска директив @use с glob-паттерном
            const globUseRegex = /@use\s+['"]([^'"]*\*[^'"]*)['"](\s+as\s+\*\s*)?;/g;
            let transformedCode = code;
            let match;

            while ((match = globUseRegex.exec(code)) !== null) {
                const globPattern = match[1];
                // Вычисляем абсолютный путь для glob-паттерна
                const absoluteGlob = path.join(path.dirname(id), globPattern);
                const files = glob.sync(absoluteGlob);

                if (files.length === 0) {
                    this.warn(`Не найдены файлы для glob-паттерна: ${globPattern} в ${id}`);
                }

                // Для каждого найденного файла формируем директиву @use
                const useStatements = files.map(file => {
                    // Вычисляем путь относительно текущего файла
                    const relativePath = path.relative(path.dirname(id), file).replace(/\\/g, '/');
                    const normalizedPath = relativePath.startsWith('.') ? relativePath : './' + relativePath;
                    return `@use '${normalizedPath}' as *;`;
                }).join('\n');

                console.log(useStatements);
                // Заменяем исходную директиву @use с glob на полученные директивы
                transformedCode = transformedCode.replace(match[0], useStatements);
            }

            // Добавляем additionalData (например, глобальные стили) в начало файла, если передано в опциях
            if (options.additionalData) {
                transformedCode = options.additionalData + '\n' + transformedCode;
            }

            return {
                code: transformedCode,
                map: null,
            };
        }
    };
}

export default viteScssGlobUsePlugin;