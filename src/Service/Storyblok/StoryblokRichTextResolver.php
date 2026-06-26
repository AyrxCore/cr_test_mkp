<?php

declare(strict_types=1);

namespace App\Service\Storyblok;

use Storyblok\Tiptap\Extension\Storyblok;
use Tiptap\Editor;

class StoryblokRichTextResolver
{
    private Editor $editor;

    public function __construct()
    {
        $this->editor = new Editor([
            'extensions' => [
                new Storyblok(),
            ],
        ]);
    }

    /**
     * Convertit du contenu Storyblok (RichText, HTML brut, ou texte) en HTML.
     * Gère automatiquement :
     * - RichText structuré (paragraphes, headings, listes, etc.)
     * - Blocs HTML (code_block avec class="language-html")
     * - Strings brutes (HTML ou texte)
     * - Valeurs null.
     */
    public function render(mixed $data): ?string
    {
        // Cas 1 : Valeur vide ou null
        if (!$data) {
            return null;
        }

        // Cas 2 : String brute (HTML ou texte)
        if (\is_string($data)) {
            return $data;
        }

        // Cas 3 : Pas un array (sécurité)
        if (!\is_array($data)) {
            return null;
        }

        // Cas 4 : Détection d'un bloc HTML (code_block)
        // Structure : doc > content[0] > type: code_block > content[0] > text
        if (isset($data['content']) && \is_array($data['content']) && \count($data['content']) === 1) {
            $block = $data['content'][0];
            if (isset($block['type']) && $block['type'] === 'code_block' && isset($block['content'][0]['text'])) {
                // Extraire le HTML brut du code_block
                return $block['content'][0]['text'];
            }
        }

        // Cas 5 : RichText structuré normal
        try {
            return $this->editor->setContent($data)->getHTML();
        } catch (\Throwable $e) {
            // En cas d'erreur de rendu, on retourne une chaîne vide pour ne pas casser l'affichage
            return '';
        }
    }
}
