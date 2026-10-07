<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'name',
    'wrapperClass' => 'col-md-6',
    'labelId' => null,
    'selectId' => null,
    'otherName' => null,
    'otherValue' => null,
    'otherPlaceholder' => 'Type here',
    'otherWrapperId' => null,
    'otherInputId' => null,
    'selectClass' => '',
    'otherClass' => '',
    'showOther' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'label',
    'name',
    'wrapperClass' => 'col-md-6',
    'labelId' => null,
    'selectId' => null,
    'otherName' => null,
    'otherValue' => null,
    'otherPlaceholder' => 'Type here',
    'otherWrapperId' => null,
    'otherInputId' => null,
    'selectClass' => '',
    'otherClass' => '',
    'showOther' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $selectId = $selectId ?? $name;
    $otherWrapperId = $otherWrapperId ?? ($otherName ? ($selectId . 'OtherGroup') : null);
    $otherInputId = $otherInputId ?? ($otherName ? ($selectId . 'OtherInput') : null);
?>

<div class="<?php echo e($wrapperClass); ?> others-field">
    <label <?php if($labelId): ?> id="<?php echo e($labelId); ?>" <?php endif; ?> class="form-label text-white small fw-bold text-uppercase"><?php echo e($label); ?></label>
    <select
        <?php echo e($attributes->merge(['class' => trim('form-select others-select ' . $selectClass)])); ?>

        name="<?php echo e($name); ?>"
        id="<?php echo e($selectId); ?>"
        aria-controls="<?php echo e($otherWrapperId); ?>"
        <?php if($otherWrapperId): ?> data-other-wrapper-id="<?php echo e($otherWrapperId); ?>" <?php endif; ?>
    >
        <?php echo e($slot); ?>

    </select>

    <?php if($otherName): ?>
        <div class="other-wrapper <?php echo e($showOther ? 'is-visible' : ''); ?>" id="<?php echo e($otherWrapperId); ?>" aria-hidden="<?php echo e($showOther ? 'false' : 'true'); ?>">
            <input
                type="text"
                class="form-control other-input <?php echo e($otherClass); ?>"
                name="<?php echo e($otherName); ?>"
                id="<?php echo e($otherInputId); ?>"
                value="<?php echo e(old($otherName, $otherValue)); ?>"
                placeholder="<?php echo e($otherPlaceholder); ?>"
                aria-label="<?php echo e($otherPlaceholder); ?>"
                <?php if(!$showOther): ?> disabled <?php endif; ?>
            >
        </div>
    <?php endif; ?>
</div><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\components\others-select.blade.php ENDPATH**/ ?>