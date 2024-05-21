import { existsSync } from 'fs';
import fs from 'fs-extra';
import inquirer from 'inquirer';

async function promptUser() {
	const answers = await inquirer.prompt([
		{
			type: 'input',
			name: 'componentName',
			message: 'Enter the component name:',
			validate: input => (input ? true : 'Component name cannot be empty')
		},
		{
			type: 'confirm',
			name: 'addScriptsFile',
			message: 'Do you need .ts file for component? (default - false)',
			default: false
		},
		{
			type: 'confirm',
			name: 'carbonFieldsTemplate',
			message: 'Do you need a Carbon Fields template? (default - false)',
			default: false
		}
	]);

	return answers;
}

function fileExists(filePath) {
	return existsSync(filePath);
}

function createFile(filePath, content = '') {
	fs.outputFileSync(filePath, content);
	console.log(`Created: ${filePath}`);
}

async function main() {
	const componentsFolder = 'components';
	if (!existsSync(componentsFolder)) {
		throw new Error(`Error: Folder not found - ${componentsFolder}`);
	}

	const { componentName, carbonFieldsTemplate, addScriptsFile } =
		await promptUser();

	const componentPhpPath = `${componentsFolder}/${componentName}.php`;
	const componentScssPath = `${componentsFolder}/${componentName}.scss`;
	const componentTsPath = `${componentsFolder}/${componentName}.ts`;
	const componentCarbonPhpPath = `${componentsFolder}/${componentName}-carbon.php`;

	const filesToCheck = [
		componentPhpPath,
		componentScssPath,
		componentTsPath,
		componentCarbonPhpPath
	];
	if (carbonFieldsTemplate) {
		filesToCheck.push(componentCarbonPhpPath);
	}

	for (const filePath of filesToCheck) {
		if (fileExists(filePath)) {
			console.error(`Error: File already exists - ${filePath}`);
			process.exit(1);
		}
	}

	createFile(componentPhpPath, `.${componentName}\n`);
	createFile(componentScssPath, `.${componentName}{\n\n}`);

	if (addScriptsFile) {
		const camelCaseName = componentName.replace(/-([a-z])/g, g =>
			g[1].toUpperCase()
		);
		if (camelCaseName[0]) {
			camelCaseName[0] = camelCaseName[0].toUpperCase();
		}
		createFile(
			componentTsPath,
			`export const init${camelCaseName} = () => {}`
		);
	}

	if (carbonFieldsTemplate) {
		createFile(
			componentCarbonPhpPath,
			`<?php\n//${componentName}\ncrbBlock`
		);
	}
}

main().catch(error => {
	console.error('An unexpected error occurred:', error);
	process.exit(1);
});
