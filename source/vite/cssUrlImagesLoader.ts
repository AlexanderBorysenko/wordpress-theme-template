// plugin which finds file paths in scss, moves them to the build folder and replaces the url() with the new path
export const cssUrlImagesLoader = () => {
    return {
        name: 'css-url-images-loader',
        transform(code: string, id: string) {
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