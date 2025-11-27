<?php

declare(strict_types=1);

namespace App\Enum;

enum ServiceFonction: string
{
    case ACHATS = 'achats';
    case ADMINISTRATIF = 'administratif';
    case ANIMATION_DEV = 'animation_dev';
    case ARCHITECTE = 'architecte';
    case ARTISAN = 'artisan';
    case AVOCAT = 'avocat';
    case BUREAU_DETUDES = 'bureau_detudes';
    case CHANTIER_ATELIER = 'chantier_atelier';
    case CHARGE_MISSIONS = 'charge_missions';
    case CHAUFFEUR_TAXI = 'chauffeur_taxi';
    case CHEF_DE_PROJET = 'chef_de_projet';
    case CLIENTELE = 'clientele';
    case COMMERCE = 'commerce';
    case COMM_MARKET = 'comm_market';
    case COMPTABILITE = 'comptabilite';
    case CONDUCTEUR_TRAVAUX = 'conducteur_travaux';
    case CONTROLE_GESTION = 'controle_gestion';
    case DELEGUE_GENERAL = 'delegue_general';
    case DELEGUE_REGIONAL = 'delegue_regional';
    case DGS = 'DGS';
    case DIGITAL = 'digital';
    case DIRECTION_G_PRESI = 'direction_g_presi';
    case DOCTEUR = 'docteur';
    case ELU = 'elu';
    case EXPLOITATION = 'exploitation';
    case GEOMETRE = 'geometre';
    case GESTIONNAIRE_PARC = 'gestionnaire_parc';
    case GRANDS_COMPTES = 'grands_comptes';
    case GREFFIER = 'greffier';
    case INGENIEUR = 'ingenieur';
    case LOGISTIQUE = 'logistique';
    case MAGASINIER = 'magasinier';
    case MAINTENANCE = 'maintenance';
    case MAIRE = 'maire';
    case NOTAIRE = 'notaire';
    case PHARMACIEN = 'pharmacien';
    case PRODUCTION = 'production';
    case QHSE = 'qhse';
    case RESPONSABLE_AGENCE = 'responsable_agence';
    case RH = 'rh';
    case SAV = 'SAV';
    case SECRETAIRE = 'secretaire';
    case TECHNICIEN = 'technicien';
    case VETERINAIRE = 'veterinaire';

    public static function format(?string $value): ?string
    {
        try {
            return self::from($value)->label();
        } catch (\ValueError) {
            return null;
        }
    }

    public function label(): string
    {
        return match ($this) {
            self::ACHATS => 'Achats',
            self::ADMINISTRATIF => 'Administratif',
            self::ANIMATION_DEV => 'Animation/Développement',
            self::ARCHITECTE => 'Architecte',
            self::ARTISAN => 'Artisan',
            self::AVOCAT => 'Avocat',
            self::BUREAU_DETUDES => "Bureau d'études",
            self::CHANTIER_ATELIER => 'Chantier/Ateliers',
            self::CHARGE_MISSIONS => 'Chargé de missions',
            self::CHAUFFEUR_TAXI => 'Chauffeur taxi',
            self::CHEF_DE_PROJET => 'Chef de projet',
            self::CLIENTELE => 'Clientèle',
            self::COMMERCE => 'Commerce',
            self::COMM_MARKET => 'Communication/Marketing',
            self::COMPTABILITE => 'Comptabilité',
            self::CONDUCTEUR_TRAVAUX => 'Conducteur de travaux',
            self::CONTROLE_GESTION => 'Contrôleur de gestion',
            self::DELEGUE_GENERAL => 'Délégué Général',
            self::DELEGUE_REGIONAL => 'Délégué Régional',
            self::DGS => 'DGS',
            self::DIGITAL => 'Digital',
            self::DIRECTION_G_PRESI => 'Direction générale/Présidence',
            self::DOCTEUR => 'Docteur',
            self::ELU => 'Elu',
            self::EXPLOITATION => 'Exploitation',
            self::GEOMETRE => 'Géomètre expert',
            self::GRANDS_COMPTES => 'Grands comptes',
            self::GREFFIER => 'Greffier',
            self::INGENIEUR => 'Ingénieur',
            self::LOGISTIQUE => 'Logistique',
            self::MAGASINIER => 'Magasinier',
            self::MAINTENANCE => 'Maintenance',
            self::MAIRE => 'Maire',
            self::NOTAIRE => 'Notaire',
            self::PHARMACIEN => 'Pharmacien',
            self::PRODUCTION => 'Production',
            self::QHSE => 'QHSE',
            self::RESPONSABLE_AGENCE => "Responsable d'agence",
            self::RH => 'Ressources humaines',
            self::SAV => 'SAV',
            self::SECRETAIRE => 'Secrétaire',
            self::TECHNICIEN => 'Technicien',
            self::VETERINAIRE => 'Vétérinaire',
            self::GESTIONNAIRE_PARC => 'Gestionnaire de parc',
        };
    }
}
