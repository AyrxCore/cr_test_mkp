<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231206154301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $appEnv = \getenv('APP_ENV');
        $appDomain = $appEnv === 'prod' ? 'marketplace.qantis.co' : 'marketplace.preprod.qantis.co';

        $this->addSql('INSERT INTO public.channel (id, name, code, hostname) VALUES (\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'QANTIS Marketplace\',\'QANTIS_ACHAT\',?)', [$appDomain]);
        $this->addSql('UPDATE public.adherent SET channel_id = \'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\'');
        $this->addSql('INSERT INTO public.channel_parameter (id, channel_id, logo, favicon, primary_color, secondary_color, text_color, privacy_policy, general_terms_of_use, legal_terms, phone_number, email, white_label, banner, banner_title) VALUES          (\'1ee935b1-50a4-65c6-8210-9dfe7a0ccd80\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'https://qantis-marketplace.s3.eu-west-1.amazonaws.com/assets/logo-qantis.png\',\'https://qantis-marketplace.s3.eu-west-1.amazonaws.com/assets/favicon-qantis.svg\',\'#050056\',\'#9553ff\',\'#6b7280\',\'/politique-de-confidentialite\',\'/conditions-generales-d-utilisation\',\'/mentions-legales\',\'+33437650621\',\'marketplace@qantis.co\',false,null,null)');
        $this->addSql('INSERT INTO public.channel_option (id, channel_id, name, value) VALUES
                    (\'a6af5d2b-c537-44ae-a09a-32ededafbbe3\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'BANNER_FLASH_HOMEPAGE_QANTIS\',\'true\'),
                    (\'69fa0d14-b4c5-469d-b344-7fb15d3bbf09\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'BANNER_SLIDER_HOMEPAGE_QANTIS\',\'true\'),
                    (\'22f57b93-1cf3-43d6-8a6f-73cd63b2f98d\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'RSE_HOMEPAGE_QANTIS\',\'true\'),
                    (\'7baab085-a4d1-4932-baa4-70cd20b449a6\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'SUPPLIER_PARTNERS_HOMEPAGE_QANTIS\',\'true\'),
                    (\'e6b4881b-9db4-470f-83db-32dfaaedd8fd\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'EXPERT_CONTENT_HOMEPAGE_QANTIS\',\'true\'),
                    (\'f471e184-203d-43c6-8ee6-e2476edb0e9f\',\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\',\'FAVORITES\',\'true\')');


    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'a6af5d2b-c537-44ae-a09a-32ededafbbe3\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'69fa0d14-b4c5-469d-b344-7fb15d3bbf09\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'22f57b93-1cf3-43d6-8a6f-73cd63b2f98d\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'7baab085-a4d1-4932-baa4-70cd20b449a6\'');
        $this->addSql('DELETE FROM public.channel_option WHERE id=\'e6b4881b-9db4-470f-83db-32dfaaedd8fd\'');
        $this->addSql('DELETE FROM public.channel_parameter WHERE id=\'1ee935b1-50a4-65c6-8210-9dfe7a0ccd80\'');
        $this->addSql('UPDATE public.adherent SET channel_id = NULL');
        $this->addSql('DELETE FROM public.channel WHERE id=\'1ee935b1-507b-6a54-b3b1-9dfe7a0ccd80\'');
    }
}
