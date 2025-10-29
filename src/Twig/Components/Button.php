<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

// src/Twig/Components/Button.php
#[AsTwigComponent('Button')]
final class Button
{
    public string $label = 'Action';
    public string $variant = 'glass';
    public ?string $href = null;
    public string $type = 'button';
    public string $size = 'md';
    public bool $disabled = false;
    public bool $loading = false;
    public string $class = '';

    public function classes(): string
    {
        $sizeMap = ['sm' => 'btn-sm', 'md' => '', 'lg' => 'btn-lg'];
        $variantMap = [
            'glass'        => 'btn-glass btn-pill',
            'glass-brand'  => 'btn-glass-orange btn-pill', // 🟠 ton effet verre orangé
            'filled'       => 'bg-corail btn-pill',
            'glass-light'  => 'btn-glass-light btn-pill',
        ];

        return trim(implode(' ', array_filter([
            'btn',
            $variantMap[$this->variant] ?? $variantMap['glass'],
            $sizeMap[$this->size] ?? '',
            $this->class,
        ])));
    }
}
