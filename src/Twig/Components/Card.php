<?php
// src/Twig/Components/Card.php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Card')]
final class Card  // ← Enlevez "Component"
{
    /** soft | glass-white | glass-dark | info-card */
    public string $variant = 'soft';

    /** sm | md | lg */
    public string $size = 'md';

    public ?string $title = null;
    public ?string $subtitle = null;
    public ?string $text = null;

    /** URL de l'image */
    public ?string $image = null;
    public ?string $imageAlt = null;

    /** Badge optionnel */
    public ?string $badge = null;

    /** URL cliquable */
    public ?string $href = null;

    /** Classes supplémentaires */
    public string $class = '';

    /** Bordure activée */
    public bool $border = false;

    /** Couleur du texte (white, dark, primary, etc.) */
    public string $textColor = '';

    public function classes(): string
    {
        $variantMap = [
            'soft'        => 'card soft',
            'glass'       => 'glass-card-white',
            'glass-white' => 'glass-card-white',
            'glass-dark'  => 'glass-card-dark',
            'info-card'   => 'card info-card',
        ];
        
        $sizeMap = [
            'sm' => 'card-sm',
            'md' => '',
            'lg' => 'card-lg',
        ];

        $classes = [
            $variantMap[$this->variant] ?? $variantMap['soft'],
            $sizeMap[$this->size] ?? '',
        ];

        if ($this->border && str_contains($this->variant, 'glass')) {
            $classes[] = 'glass-border';
        }

        // Ajouter la couleur du texte
        if ($this->textColor) {
            $classes[] = "text-{$this->textColor}";
        }

        if ($this->class) {
            $classes[] = $this->class;
        }

        return trim(implode(' ', array_filter($classes)));
    }
}