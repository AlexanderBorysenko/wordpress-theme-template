import { createInterface } from 'readline';
import {
	mkdirSync,
	existsSync,
	readdirSync,
	readFileSync,
	writeFileSync
} from 'node:fs';
import { join, resolve } from 'node:path';

const __dirname = resolve();

const slugify = str =>
	str
		.toLowerCase()
		.trim()
		.replace(/[^\w\s-]/g, '')
		.replace(/[\s_-]+/g, '-')
		.replace(/^-+|-+$/g, '');

const readline = createInterface({
	input: process.stdin,
	output: process.stdout
});

const readLineAsync = msg => {
	return new Promise(resolve => {
		readline.question(msg, userRes => {
			resolve(userRes);
		});
	});
};

function walk(dir) {
	return readdirSync(dir, { withFileTypes: true }).flatMap(file =>
		file.isDirectory() ? walk(join(dir, file.name)) : join(dir, file.name)
	);
}

const exit = msg => {
	console.error('ERROR: ' + msg);
	process.exit(1);
};

const startApp = async () => {
	let company = await readLineAsync(
		'What NPM package namespace should be used? '
	);
	if (company) company = '@' + company + '/';
	else company = '';

	const namespace = await readLineAsync(
		'What library namespace would you like to use? '
	);
	const blockName = await readLineAsync(
		'What is the name of the new block? '
	);
	readline.close();
	const slug = slugify(blockName);
	const dir = join(__dirname, `packages/${slug}`);

	console.log(
		`Creating a new block as ${namespace}/${slug} with the name of "${blockName}"`
	);
	mkdirSync(dir);
	mkdirSync(`${dir}/src`);

	const stubs = walk(resolve('./stubs'));
	for (const stub of stubs) {
		if (/\.stub$/i.test(stub) === false) continue;
		let contents = readFileSync(stub, 'utf8');
		// e.g. "src/save.jsx"
		const outputPath = `${dir}/${stub
			.replace(join(__dirname, `/stubs/`), '')
			.replace(/\.stub$/, '')}`;
		contents = contents
			.replace(/##company##/gi, company)
			.replace(/##namespace##/gi, namespace)
			.replace(/##block##/g, slug)
			.replace(/##name##/g, blockName);
		writeFileSync(outputPath, contents);
	}
	console.log(`Complete`);
};

startApp();
