#!/usr/bin/env node

import inquirer from 'inquirer';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Функція для конвертації рядка у kebab-case
function toKebabCase(str) {
	return str
		.replace(/([a-z])([A-Z])/g, '$1-$2')
		.replace(/[\s_]+/g, '-')
		.toLowerCase();
}
function kebabToCamelCaseComponentName(str) {
	return str
		.split('-')
		.map((word, index) => {
			return word.charAt(0).toUpperCase() + word.slice(1);
		})
		.join('');
}

function kebabToReadable(str) {
	return str.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

const handleTemplate = (filename, args) => {
	try {
		const stubPath = path.join(__dirname, `${filename}.stub`);
		let template = fs.readFileSync(stubPath, 'utf-8');
		for (const key in args) {
			template = template.replace(
				new RegExp(`{{${key}}}`, 'g'),
				args[key]
			);
		}
		return template;
	} catch (error) {
		console.error(`Error processing template for ${filename}:`, error);
		return '';
	}
};

/**
 * Template generator interface.
 * Кожна функція приймає ім'я файлу та повертає рядок з шаблонним контентом.
 */
const templateGenerators = {
	// Generates PHP file template content
	php: filename =>
		handleTemplate('component.php', {
			componentName: filename,
			componentPrettyName: kebabToReadable(filename)
		}),
	// Generates SCSS file template content
	scss: filename =>
		handleTemplate('component.scss', { componentName: filename }),
	// Generates CarbonFields Block template content
	carbon: filename =>
		handleTemplate('component.carbon.php', {
			componentName: filename,
			componentPrettyName: kebabToReadable(filename)
		}),
	// Generates TypeScript template content using the tsType option
	ts: (filename, tsType = 'reusable') => {
		if (tsType === 'reusable') {
			return handleTemplate('component-reusable.ts', {
				componentName: filename,
				camelComponentName: kebabToCamelCaseComponentName(filename)
			});
		}
		return handleTemplate('component-singleton.ts', {
			componentName: filename,
			camelComponentName: kebabToCamelCaseComponentName(filename)
		});
	}
};

(async function () {
	try {
		// Prompt user for component name and file selections
		const answers = await inquirer.prompt([
			{
				type: 'input',
				name: 'componentName',
				message: 'Enter component name:',
				validate(input) {
					return input ? true : 'Component name cannot be empty!';
				}
			},
			{
				type: 'checkbox',
				name: 'files',
				message: 'Select the files to create:',
				choices: [
					{ name: 'PHP component file', value: 'php', checked: true },
					{ name: 'SCSS file', value: 'scss', checked: true },
					{ name: 'CarbonFields Block', value: 'carbon' },
					{ name: 'TypeScript template', value: 'ts' }
				]
			}
		]);

		const { componentName, files } = answers;
		const kebabName = toKebabCase(componentName);
		const componentDir = path.join(
			__dirname,
			'../../components'
			// , kebabName
		);

		// If user selected the TypeScript template, ask for component type:
		let tsType = 'Reusable';
		if (files.includes('ts')) {
			const tsAnswer = await inquirer.prompt([
				{
					type: 'list',
					name: 'tsType',
					message: 'Select TypeScript component type:',
					choices: [
						{ name: 'Reusable', value: 'reusable' },
						{ name: 'Singleton', value: 'singleton' }
					],
					default: 'Reusable'
				}
			]);
			tsType = tsAnswer.tsType;
		}

		// Create component directory (if it doesn't exist)
		fs.mkdirSync(componentDir, { recursive: true });
		console.log(`Directory created: ${componentDir}`);

		// Define file names for each selected file type
		const fileTemplates = {
			php: `${kebabName}.php`,
			scss: `${kebabName}.scss`,
			carbon: `${kebabName}.carbon.php`,
			ts: `${kebabName}.ts`
		};

		// Create files with content generated via templateGenerators interface
		files.forEach(type => {
			const filename = fileTemplates[type];
			const filePath = path.join(componentDir, filename);
			let content = '';
			const kebabComponentName = toKebabCase(componentName);
			if (type === 'ts') {
				content = templateGenerators[type](kebabComponentName, tsType);
			} else {
				content = templateGenerators[type]
					? templateGenerators[type](kebabComponentName)
					: '';
			}
			fs.writeFileSync(filePath, content);
			console.log(`File created: ${filePath}`);
		});

		console.log('Component created successfully!');
	} catch (error) {
		console.error('Error:', error);
	}
})();
