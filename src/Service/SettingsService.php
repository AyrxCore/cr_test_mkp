<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Setting;
use App\Repository\SettingRepository;

class SettingsService
{
    public function __construct(private SettingRepository $settingRepository)
    {
    }

    public function getSetting(string $settingName): ?Setting
    {
        return $this->settingRepository->findOneBy(['name' => $settingName]);
    }

    public function useExternalScriptsTags(): bool|string|null
    {
        return !$this->getSetting(Setting::EXTERNAL_SCRIPTS_TAGS) ? false : $this->getSetting(Setting::EXTERNAL_SCRIPTS_TAGS)->getValue();
    }

    public function isMaintenanceMode(): bool|string|null
    {
        return !$this->getSetting(Setting::MAINTENANCE_MODE) ? false : $this->getSetting(Setting::MAINTENANCE_MODE)->getValue();
    }

    public function getChatbot(): bool|string|null
    {
        return !$this->getSetting(Setting::CHATBOT) ? false : $this->getSetting(Setting::CHATBOT)->getValue();
    }
}
