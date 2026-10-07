@props([
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
])

@php
    $selectId = $selectId ?? $name;
    $otherWrapperId = $otherWrapperId ?? ($otherName ? ($selectId . 'OtherGroup') : null);
    $otherInputId = $otherInputId ?? ($otherName ? ($selectId . 'OtherInput') : null);
@endphp

<div class="{{ $wrapperClass }} others-field">
    <label @if($labelId) id="{{ $labelId }}" @endif class="form-label text-white small fw-bold text-uppercase">{{ $label }}</label>
    <select
        {{ $attributes->merge(['class' => trim('form-select others-select ' . $selectClass)]) }}
        name="{{ $name }}"
        id="{{ $selectId }}"
        aria-controls="{{ $otherWrapperId }}"
        @if($otherWrapperId) data-other-wrapper-id="{{ $otherWrapperId }}" @endif
    >
        {{ $slot }}
    </select>

    @if($otherName)
        <div class="other-wrapper {{ $showOther ? 'is-visible' : '' }}" id="{{ $otherWrapperId }}" aria-hidden="{{ $showOther ? 'false' : 'true' }}">
            <input
                type="text"
                class="form-control other-input {{ $otherClass }}"
                name="{{ $otherName }}"
                id="{{ $otherInputId }}"
                value="{{ old($otherName, $otherValue) }}"
                placeholder="{{ $otherPlaceholder }}"
                aria-label="{{ $otherPlaceholder }}"
                @if(!$showOther) disabled @endif
            >
        </div>
    @endif
</div>