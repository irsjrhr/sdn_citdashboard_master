@php
    $map = [
        1 => ['label' => 'Submitted', 'class' => 'primary'],
        3 => ['label' => 'Approved',  'class' => 'success'],
        4 => ['label' => 'Rejected',  'class' => 'danger'],
        5 => ['label' => 'Inquiry',   'class' => 'warning'],
    ];

    $badge = $map[$status] ?? ['label' => 'Unknown', 'class' => 'secondary'];
@endphp

<span class="badge bg-{{ $badge['class'] }}">
    {{ $badge['label'] }}
</span>
