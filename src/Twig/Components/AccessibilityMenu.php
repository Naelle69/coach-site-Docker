<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('AccessibilityMenu')]
final class AccessibilityMenu
{
    public string $class = '';
}