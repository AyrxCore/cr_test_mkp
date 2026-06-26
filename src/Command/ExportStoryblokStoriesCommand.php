<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\Storyblok\StoryblokHttpClient;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'storyblok:export-stories',
    description: 'Exporte toutes les stories Storyblok (accords-cadres) dans une matrice Excel',
)]
class ExportStoryblokStoriesCommand extends Command
{
    /** Les clés servent à la fois de headers Excel et d'identifiants dans le tableau retourné par mapRow(). */
    private const array COLUMNS = [
        'Statut publication',
        'Date de publication',
        'Dernière modification',
        "Nom de l'accord-cadre",
        'Tarif ID',
        "Type d'accord-cadre",
        'Label bouton de rattachement',
        'URL bouton de rattachement',
        'Statuts de rattachement',
        'Description du layer de confirmation de rattachement',
        'Description du layer de réussite du rattachement',
        'URL de la bannière (Desktop)',
        'URL de la bannière (Mobile)',
        'URL du logo',
        'Texte badge haut',
        'Texte badge bas',
        'Note RSE',
        'Présentation du partenaire',
        'Liste à puces partenaire',
        'Titre présentation partenaire',
        'Layer "en savoir plus"',
        'Title (bloc conditions négociées)',
        'Description (bloc conditions négociées)',
        'Détails et engagements (titre)',
        'Contenu "Détails et engagements"',
        'Bouton "Consulter les conditions négociées"',
        'Boutons fichiers / liens',
        'Titre (bloc comment en bénéficier)',
        'Étapes',
    ];

    private const string EXPORT_DIR = 'storyblok-exports';

    public function __construct(
        private readonly StoryblokHttpClient $storyblokHttpClient,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Chemin du fichier xlsx de sortie (par défaut : var/storyblok-exports/)',
                null
            )
            ->addOption(
                'sb-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'Version Storyblok à récupérer (draft ou published)',
                'published'
            )
            ->addOption(
                'starts-with',
                null,
                InputOption::VALUE_OPTIONAL,
                'Filtrer par dossier/slug Storyblok (starts_with)',
                StoryblokEndpoint::ACCORD_CADRE->value
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $outputPath = $input->getOption('output') ?? $this->resolveDefaultOutputPath();
        $version = $input->getOption('sb-version');
        $startsWith = $input->getOption('starts-with');

        if (!\in_array($version, ['draft', 'published'], true)) {
            $io->error(\sprintf(
                'Valeur invalide pour --sb-version : "%s". Valeurs acceptées : "draft", "published".',
                $version
            ));

            return Command::INVALID;
        }

        $io->title('Export Storyblok Stories');
        $io->text(\sprintf('Version : <info>%s</info>', $version));
        $io->text(\sprintf('Fichier de sortie : <info>%s</info>', $outputPath));

        $io->section('Récupération des stories...');

        $filters = [];
        if ($startsWith !== null) {
            $filters['starts_with'] = $startsWith;
        }

        try {
            $response = $this->storyblokHttpClient->getStories($filters, null, $version);
        } catch (\Throwable $e) {
            $io->error('Erreur lors de la récupération des stories : '.$e->getMessage());

            return Command::FAILURE;
        }

        $stories = $response['stories'] ?? [];
        $io->text(\sprintf('<info>%d</info> stories récupérées', \count($stories)));

        \usort($stories, fn (array $a, array $b) => \strcasecmp(
            $a['content']['accordCadreName'] ?? $a['name'] ?? '',
            $b['content']['accordCadreName'] ?? $b['name'] ?? ''
        ));

        $io->section('Génération du fichier Excel...');

        $spreadsheet = $this->buildSpreadsheet($stories);

        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($outputPath);
        } catch (\Throwable $e) {
            $io->error('Erreur lors de la sauvegarde du fichier : '.$e->getMessage());

            return Command::FAILURE;
        }

        $io->success(\sprintf(
            "Export terminé ! %d stories exportées.\nFichier : %s",
            \count($stories),
            $outputPath
        ));

        return Command::SUCCESS;
    }

    private function resolveDefaultOutputPath(): string
    {
        $dir = $this->projectDir.'/var/'.self::EXPORT_DIR;

        if (!\is_dir($dir)) {
            \mkdir($dir, 0755, true);
        }

        return \sprintf('%s/storyblok-export-%s.xlsx', $dir, \date('Y-m-d_His'));
    }

    private function buildSpreadsheet(array $stories): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Export Storyblok');

        $this->writeHeaders($sheet);

        $row = 2;
        foreach ($stories as $story) {
            $data = $this->mapRow($story);
            foreach (self::COLUMNS as $colIndex => $header) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($colLetter.$row, $data[$header] ?? '');
            }
            ++$row;
        }

        foreach (\array_keys(self::COLUMNS) as $colIndex) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(30);

        return $spreadsheet;
    }

    private function writeHeaders(Worksheet $sheet): void
    {
        foreach (self::COLUMNS as $index => $header) {
            $colLetter = Coordinate::stringFromColumnIndex($index + 1);
            $cellRef = $colLetter.'1';

            $sheet->setCellValue($cellRef, $header);

            $style = $sheet->getStyle($cellRef);
            $style->getFont()->setBold(true);
            $style->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('1F4E79');
            $style->getFont()->getColor()->setRGB('FFFFFF');
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    private function mapRow(array $story): array
    {
        $content = $story['content'] ?? [];
        $body = $content['body'] ?? [];

        $banner = $this->findBlock($body, 'bannerBlock');
        $presentation = $this->findBlock($body, 'presentationBlock');
        $negociated = $this->findBlock($body, 'negociatedTermsBlock');
        $steps = $this->findBlock($body, 'stepsBlock');

        $statut = $content['statutsRattachement'][0] ?? [];

        $publishedAt = $story['published_at'] ?? null;

        return [
            'Statut publication'                                   => $publishedAt !== null ? 'Publié' : 'Non publié',
            'Date de publication'                                   => $publishedAt !== null ? (new \DateTimeImmutable($publishedAt))->format('d/m/Y H:i') : '',
            'Dernière modification'                                 => isset($story['updated_at']) ? (new \DateTimeImmutable($story['updated_at']))->format('d/m/Y H:i') : '',
            "Nom de l'accord-cadre"                                => $content['accordCadreName'] ?? $story['name'] ?? '',
            'Tarif ID'                                             => $content['tarifId'] ?? '',
            "Type d'accord-cadre"                                  => $content['accordCadreType'] ?? '',
            'Label bouton de rattachement'                         => $content['labelCtaRattachement'] ?? '',
            'URL bouton de rattachement'                           => $content['urlCtaRattachement'] ?? '',
            'Statuts de rattachement'                              => $this->formatStatut($statut),
            'Description du layer de confirmation de rattachement' => $this->richTextToPlain($content['confirmationLayerDescription'] ?? null),
            'Description du layer de réussite du rattachement'    => $this->richTextToPlain($content['successLayerDescription'] ?? null),
            'URL de la bannière (Desktop)'                         => $banner['imgBannerUrlDesktop'] ?? '',
            'URL de la bannière (Mobile)'                          => $banner['imgBannerUrlMobile'] ?? '',
            'URL du logo'                                          => $banner['logoUrl'] ?? '',
            'Texte badge haut'                                     => $banner['badgeTextTop'] ?? '',
            'Texte badge bas'                                      => $banner['badgeTextBottom'] ?? '',
            'Note RSE'                                             => $presentation['rseScore'] ?? '',
            'Présentation du partenaire'                           => $presentation['description'] ?? '',
            'Liste à puces partenaire'                             => $this->richTextToPlain($presentation['bulletpoints'] ?? null),
            'Titre présentation partenaire'                        => $presentation['title'] ?? '',
            'Layer "en savoir plus"'                               => $this->formatLayerMoreInformations($presentation['layerMoreInformations'][0] ?? null),
            'Title (bloc conditions négociées)'                    => $negociated['title'] ?? '',
            'Description (bloc conditions négociées)'              => $this->richTextToPlain($negociated['description'] ?? null),
            'Détails et engagements (titre)'                       => $negociated['detailsTitle'] ?? '',
            'Contenu "Détails et engagements"'                     => $this->richTextToPlain($negociated['detailsContent'] ?? null),
            'Bouton "Consulter les conditions négociées"'          => $this->formatNegociatedTermsButton($negociated),
            'Boutons fichiers / liens'                             => $this->formatAssetButtons($negociated['assetButtons'] ?? []),
            'Titre (bloc comment en bénéficier)'                   => $steps['title'] ?? '',
            'Étapes'                                               => $this->formatStepItems($steps['stepItems'] ?? []),
        ];
    }

    private function findBlock(array $body, string $component): array
    {
        foreach ($body as $block) {
            if (($block['component'] ?? '') === $component) {
                return $block;
            }
        }

        return [];
    }

    private function richTextToPlain(mixed $data): string
    {
        if ($data === null || $data === '') {
            return '';
        }

        if (\is_string($data)) {
            return \strip_tags($data);
        }

        if (!\is_array($data)) {
            return '';
        }

        return \trim($this->extractTextNodes($data));
    }

    private function extractTextNodes(array $node): string
    {
        if (isset($node['text'])) {
            return $node['text'];
        }

        if (!isset($node['content']) || !\is_array($node['content'])) {
            return '';
        }

        $blockTypes = ['paragraph', 'heading', 'list_item', 'bullet_list', 'ordered_list', 'blockquote'];
        $text = '';

        foreach ($node['content'] as $child) {
            $childText = $this->extractTextNodes($child);
            if ($childText === '') {
                continue;
            }
            $text .= $childText;
            if (\in_array($child['type'] ?? '', $blockTypes, true)) {
                $text .= "\n";
            }
        }

        return $text;
    }

    private function formatStatut(array $statuts): string
    {
        if (empty($statuts)) {
            return '';
        }

        $parts = [];
        if (!empty($statuts['labelNotActivated'])) {
            $parts[] = 'À activer : '.$statuts['labelNotActivated'];
        }
        if (!empty($statuts['labelPending'])) {
            $parts[] = 'En cours : '.$statuts['labelPending'];
        }
        if (!empty($statuts['labelActivated'])) {
            $parts[] = 'Activé : '.$statuts['labelActivated'];
        }

        return \implode(' | ', $parts);
    }

    private function formatLayerMoreInformations(?array $layer): string
    {
        if ($layer === null) {
            return '';
        }

        $parts = [];

        $description = $this->richTextToPlain($layer['description'] ?? null);
        if ($description !== '') {
            $parts[] = 'Description : '.$description;
        }

        if (!empty($layer['phone'])) {
            $parts[] = 'Téléphone : '.$layer['phone'];
        }

        $buttons = $this->formatAssetButtons($layer['assetButtons'] ?? []);
        if ($buttons !== '') {
            $parts[] = 'Boutons : '.$buttons;
        }

        return \implode("\n", $parts);
    }

    private function formatNegociatedTermsButton(array $negociated): string
    {
        $button = $negociated['negociatedTermsButton'][0] ?? null;

        if ($button !== null) {
            $label = $button['label'] ?? '';
            $items = [];
            foreach ($button['negociatedTermsLayerItems'] ?? [] as $item) {
                if (!empty($item['imgLink'])) {
                    $items[] = $item['imgLink'];
                }
            }

            return $label.(!empty($items) ? ' → '.\implode(', ', $items) : '');
        }

        $firstAssetButton = $negociated['assetButtons'][0] ?? null;
        if ($firstAssetButton !== null) {
            return ($firstAssetButton['buttonLabel'] ?? '').' → '.($firstAssetButton['assetLink'] ?? '');
        }

        return '';
    }

    private function formatAssetButtons(array $buttons): string
    {
        $lines = [];
        foreach ($buttons as $button) {
            $label = $button['buttonLabel'] ?? '';
            $link = $button['assetLink'] ?? '';
            if ($label !== '' || $link !== '') {
                $lines[] = $label.' → '.$link;
            }
        }

        return \implode("\n", $lines);
    }

    private function formatStepItems(array $stepItems): string
    {
        $lines = [];
        foreach ($stepItems as $index => $step) {
            $num = $index + 1;
            $titleQ = $step['title_qantis'] ?? '';
            $descQ = $step['description_qantis'] ?? '';
            $titleWL = $step['title_whitelabel'] ?? '';
            $descWL = $step['description_whitelabel'] ?? '';

            $line = "Étape {$num} :";
            $line .= "\n  [QANTIS] {$titleQ}";
            if ($descQ !== '') {
                $line .= " — {$descQ}";
            }
            if ($titleWL !== $titleQ || $descWL !== $descQ) {
                $line .= "\n  [WHITELABEL] {$titleWL}";
                if ($descWL !== '') {
                    $line .= " — {$descWL}";
                }
            }

            $lines[] = $line;
        }

        return \implode("\n", $lines);
    }
}
