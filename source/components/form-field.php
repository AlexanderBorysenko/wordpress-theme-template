<?php
/**
 * Form Field Component
 */

if (!$controlAttributes ?? null) {
    throw new Exception('`controlAttributes` are not set for `form-field`');
}
if (!$controlAttributes['name'] ?? null) {
    throw new Exception('`name` is required for `controlAttributes` in `form-field`');
}

// Default values
$tag ??= 'input';
$options ??= null;
$baseClass ??= 'form-field';
$label ??= null;
$required  = $controlAttributes['required'] ?? false;

// Generate unique identifier for form control
$fieldUuid = uniqid('field_');

// Set component container attributes
$fieldAttributes = $htmlAttributesString([
    'class'                => [
        $baseClass,
        '_required' => $required ?? false,
        "_$tag",
    ],
    'data-form-field-name' => $controlAttributes['name'],
]);

// Initialize control attributes
$controlAttributes['id'] = $fieldUuid;
$inputType               = $controlAttributes['type'] ?? 'text';
$value                   = $controlAttributes['value'] ?? '';

// Check if this is a single checkbox toggle
$isCheckboxToggle = $inputType === 'checkbox' && $label && !is_array($options);

/**
 * Render functions for each field type
 */
// Define render functions using anonymous functions
$renderLabel = function ($fieldUuid, $label, $baseClass) {
    ?>
    <label <?= assembleHtmlAttributes([
        'for'   => $fieldUuid,
        'class' => "{$baseClass}__label",
    ]); ?>>
        <?= $label; ?>
    </label>
    <?php
};

$renderSelect = function ($controlAttributes, $baseClass, $options) {
    ?>
    <select <?= assembleHtmlAttributes($controlAttributes, [
        "class" => "{$baseClass}__select-control",
    ]); ?>>
        <?php if ($controlAttributes['placeholder'] ?? '') : ?>
            <option value="" selected disabled><?= $controlAttributes['placeholder'] ?></option>
        <?php endif; ?>
        <?php foreach ($options as $value => $label) : ?>
            <option value="<?= $value; ?>"><?= $label; ?></option>
        <?php endforeach; ?>
    </select>
    <?= component(
        'svg-icon',
        ['class' => "{$baseClass}__select-arrow"],
        ['icon' => 'select-arrow']
    ); ?>
<?php
};

$renderTextarea = function ($controlAttributes, $baseClass, $value) {
    ?>
    <textarea <?= assembleHtmlAttributes($controlAttributes, [
        "class" => "{$baseClass}__textarea-control",
    ]); ?>><?= $value; ?></textarea>
    <?php
};

$renderCheckboxToggle = function ($controlAttributes, $baseClass, $fieldUuid, $label) {
    ?>
    <div class="<?= $baseClass; ?>__checkbox">
        <input <?= assembleHtmlAttributes($controlAttributes, [
            "class" => "{$baseClass}__checkbox-control",
        ]); ?>>
        <label <?= assembleHtmlAttributes([
            'for'   => $fieldUuid,
            'class' => "{$baseClass}__checkbox-label",
        ]); ?>>
            <?= $label ?? '' ?>
        </label>
    </div>
    <?php
};

$renderCheckboxGroup = function ($controlAttributes, $baseClass, $fieldUuid, $options) {
    ?>
    <div class="<?= $baseClass; ?>__checkbox-group">
        <?php
        $index = 0;
        foreach ($options as $value => $label) :
            $index++;
            $currentAttributes          = $controlAttributes;
            $currentAttributes['value'] = $value;
            $currentAttributes['id'] = "{$fieldUuid}_{$index}";
            ?>
            <div class="<?= $baseClass; ?>__checkbox">
                <input <?= assembleHtmlAttributes($currentAttributes, [
                    "class" => "{$baseClass}__checkbox-control",
                ]); ?>>
                <label <?= assembleHtmlAttributes([
                    'for'   => $currentAttributes['id'],
                    'class' => "{$baseClass}__checkbox-label",
                ]); ?>>
                    <?= $label ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
};

$renderInput = function ($controlAttributes, $baseClass) {
    ?>
    <input <?= assembleHtmlAttributes($controlAttributes, [
        "class" => "{$baseClass}__input-control",
    ]); ?>>
    <?php
};

?>

<div <?= $fieldAttributes; ?>>
    <?php if (!$isCheckboxToggle && $label) :
        $renderLabel($fieldUuid, $label, $baseClass);
    endif;

    // Render appropriate form control based on type
    if ($tag === 'select') {
        $renderSelect($controlAttributes, $baseClass, $options);
    } elseif ($tag === 'textarea') {
        $renderTextarea($controlAttributes, $baseClass, $value);
    } else {
        // Handle input fields
        if ($inputType === 'checkbox') {
            if ($isCheckboxToggle) {
                $renderCheckboxToggle($controlAttributes, $baseClass, $fieldUuid, $label);
            }
            if (is_array($options)) {
                $renderCheckboxGroup($controlAttributes, $baseClass, $fieldUuid, $options);
            }
        } else {
            $renderInput($controlAttributes, $baseClass);
        }
    }
    ?>
    <span class="<?= $baseClass ?>__error-message"></span>
</div>