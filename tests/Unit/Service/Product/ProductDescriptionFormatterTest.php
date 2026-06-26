<?php

declare(strict_types=1);

use App\Service\Product\ProductDescriptionFormatter;

\uses()->group('UnitProductDescriptionFormatter');

\beforeEach(function () {
    $this->formatter = new ProductDescriptionFormatter();
});

\it('returns null for null input', function () {
    \expect($this->formatter->format(null))->toBeNull();
});

\it('returns empty string for empty string input', function () {
    \expect($this->formatter->format(''))->toBe('');
});

\it('inserts line break after period + space before capital letter', function () {
    $result = $this->formatter->format('Première phrase. Deuxième phrase.');
    \expect($result)->toContain('.<br><br>D');
});

\it('inserts line break after period without space before capital letter', function () {
    $result = $this->formatter->format('Première phrase.Deuxième phrase.');
    \expect($result)->toContain('.<br><br>D');
});

\it('inserts line break between lowercase and uppercase glued together', function () {
    $result = $this->formatter->format('Format A4Livraison gratuite');
    \expect($result)->toContain('A4<br><br>Livraison');
});

\it('inserts line break between digit and uppercase+lowercase', function () {
    $result = $this->formatter->format('Quantité10Pièces disponibles');
    \expect($result)->toContain('10<br><br>Pièces');
});

\it('decodes HTML entities', function () {
    $result = $this->formatter->format('Chemise &agrave; rabats&nbsp;Format A4');
    \expect($result)->toContain('à rabats');
    \expect($result)->toContain(' Format');
});

\it('collapses multiple whitespace into single space', function () {
    $result = $this->formatter->format('Mot1  Mot2   Mot3');
    \expect($result)->toBe('Mot1 Mot2 Mot3');
});

\it('does not insert line break when next sentence starts with an abbreviation like Mr', function () {
    $result = $this->formatter->format('Première phrase. Mr Dupont est là.');
    \expect($result)->not()->toContain('phrase.<br><br>Mr');
});

\it('does not insert line break when next sentence starts with abbreviation Dr', function () {
    $result = $this->formatter->format('Première phrase. Dr Smith est disponible.');
    \expect($result)->not()->toContain('phrase.<br><br>Dr');
});
