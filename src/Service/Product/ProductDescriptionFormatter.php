<?php

declare(strict_types=1);

namespace App\Service\Product;

class ProductDescriptionFormatter
{
    private const ABBREVIATIONS = 'Gr|Mr|Mme|Dr|cm|mm';

    public function format(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }

        $text = \html_entity_decode($text, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');

        $text = \preg_replace('/\s+/u', ' ', $text);

        // Point + espace + majuscule (hors abréviations connues)
        $text = \preg_replace('/\.(?!\s?('.self::ABBREVIATIONS.'))\s+(?=[A-Z])/', '.<br><br>', $text);

        // Point + majuscule sans espace
        $text = \preg_replace('/\.([A-Z])/', '.<br><br>$1', $text);

        // Minuscule + majuscule collées (hors espace/tiret/slash précédent)
        $text = \preg_replace('/(?<![\/\s-])([a-zàâäéèêëïîôöùûüÿæœç])([A-Z])/', '$1<br><br>$2', $text);

        // Chiffre + majuscule+minuscule collés
        $text = \preg_replace('/(\d)([A-Z][a-z])/', '$1<br><br>$2', $text);

        return $text;
    }
}
