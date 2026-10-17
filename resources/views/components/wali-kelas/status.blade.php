@props(['status' => 'incomplete', 'label' => 'Data belum lengkap'])
<span {{ $attributes->class(['wc-badge', 'wc-badge--'.$status]) }}>{{ $label }}</span>
