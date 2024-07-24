# Component

Component is a reusable piece of code that can be used in multiple places in the application.

## Component Creation

Component name should be in **kebab-case**.

### Main php file

`{component-name}.php`

### Carbonfields Block Implementation

`{component-name}.carbon.php`

### Main Styles

`{component-name}.scss`

### Style Mixin for external and internal usage

`{component-name}.mixin.scss`

### Main Script

`{component-name}.ts`

### wp-admin script

`{component-name}.wp-admin.ts`

### wp-admin style

`{component-name}.wp-admin.scss`

## Components usage

To use a component you can use the simple php function which automatically includes the component file and passes the **atttributes** to the component.

```php
component('component-name', [
    'data' => 'value', ...
]);
```

## Nesting Components

To pass any html or other component -> use the `slot` attribute.

`component-name.php`

```html
<div class="component-name">
	<?= $slot ?>
</div>
```

`any-file.php`

```php
component('component-name', [
    'slot' => 'html string'
]);
```

Or for complex logic it is usefull to use `ob_start` and `ob_get_clean` functions.

`any-file.php`

```php
ob_start();
?>
`... some compex logic and html inputs`
<?php
$slot = ob_get_clean();
component('component-name', [
    'slot' => $slot
]);
```
