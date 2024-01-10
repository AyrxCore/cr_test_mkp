<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240110103420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $textFooter = "Depuis 2001, QANTIS accompagne les entreprises françaises dans leur performance et leur croissance durable en s'appuyant sur 3 moteurs : la centrale d'achat, l'expertise humaine et la marketplace.";
        $subtitle = "Depuis 2001, QANTIS optimise et facilite les achats des entreprises et réseaux d'entreprises";

        $this->addSql('UPDATE public.channel_parameter SET secondary_color = \'#404FE6\'');
        $this->addSql('INSERT INTO public.channel_option (id, channel_id, name, value) VALUES
                    (\'9a96ae87-7284-4f8d-a5d8-a333900c825d\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'BANNER_FLASH_HOMEPAGE\',\'true\'),
                    (\'7617ba33-09f4-435d-ad6c-6acc078a9567\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'BANNER_SLIDER_HOMEPAGE\',\'true\'),
                    (\'c5d4c22f-ea6b-4d2b-8b87-641f7312fe45\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'RSE_HOMEPAGE\',\'true\'),
                    (\'8efc9690-e9c1-4e3f-9e2a-a147a5d37428\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'SUPPLIER_PARTNERS_HOMEPAGE\',\'true\'),
                    (\'33d40cfc-decf-4ed2-81a6-2cdf48cfea0f\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'EXPERT_CONTENT_HOMEPAGE\',\'true\'),
                    (\'1e26cd5d-d3d8-4027-a69f-53ecd0435882\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'TEXT_FOOTER\',?),
                    (\'ed723af9-a9ea-409b-88c7-9ab3f32c63aa\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'PRE_HOME_IMAGE\',\'https://qantis-marketplace.s3.eu-west-1.amazonaws.com/assets/pre-home/qantis-prehome-image.png\'),
                    (\'e6dc0b67-5082-4ade-b0cd-63816524e55d\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'PRE_HOME_SUBTITLE\',?),
                    (\'5f5df5be-4b18-459c-93b1-18495cbaba39\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'PRE_HOME_NOTATION\',null),
                    (\'a07d467c-fd8f-424b-a139-d555f6db31d4\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'PRE_HOME_LIST\',\'Des conditions avantageuses déjà négociées;Un catalogue de plus de 200 partenaires référencés;Des produits et services du quotidien et pour vos achats métier\'),
                    (\'681746ba-889c-487e-8e4a-efb1825c8868\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'PRE_HOME_TITLE\',\'Pour tous vos achats, ayez le réflexe QANTIS !\'),
                    (\'ccd75027-a223-4932-8c4a-0c2ca0a66d92\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'SAVED_CARTS\',\'true\'),
                    (\'e7289a47-fd3f-444c-b469-a0c9ec74ae2c\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'PROMOTIONAL_FAT\',\'true\'),
                    (\'843a15c3-ae93-4a1c-ba45-0e6ce26bb831\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'BANNER_HOMEPAGE\',null),
                    (\'70e35ec4-de26-4c75-aa18-677086b88dbf\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'BANNER_TITLE_HOMEPAGE\',null)', [$textFooter, $subtitle]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'70e35ec4-de26-4c75-aa18-677086b88dbf\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'843a15c3-ae93-4a1c-ba45-0e6ce26bb831\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'e7289a47-fd3f-444c-b469-a0c9ec74ae2c\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'ccd75027-a223-4932-8c4a-0c2ca0a66d92\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'681746ba-889c-487e-8e4a-efb1825c8868\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'a07d467c-fd8f-424b-a139-d555f6db31d4\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'5f5df5be-4b18-459c-93b1-18495cbaba39\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'e6dc0b67-5082-4ade-b0cd-63816524e55d\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'ed723af9-a9ea-409b-88c7-9ab3f32c63aa\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'1e26cd5d-d3d8-4027-a69f-53ecd0435882\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'33d40cfc-decf-4ed2-81a6-2cdf48cfea0f\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'8efc9690-e9c1-4e3f-9e2a-a147a5d37428\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'c5d4c22f-ea6b-4d2b-8b87-641f7312fe45\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'7617ba33-09f4-435d-ad6c-6acc078a9567\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'9a96ae87-7284-4f8d-a5d8-a333900c825d\'');
        $this->addSql('UPDATE public.channel_parameter SET secondary_color = \'#9553ff\'');
    }
}
