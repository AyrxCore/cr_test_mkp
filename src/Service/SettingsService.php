<?php

namespace App\Service;

use App\Entity\Setting;
use App\Repository\SettingRepository;

class SettingsService
{
    public function __construct(private SettingRepository $settingRepository){}

    public function getSetting(string $settingName): ?Setting
    {
        return $this->settingRepository->findOneBy(['name' => $settingName]);
    }

    public function useExternalScriptsTags(): bool|string|null
    {
        return !$this->getSetting('external_scripts_tags') ? false : $this->getSetting('external_scripts_tags')->getValue();
    }

    public function isMaintenanceMode(): bool|string|null
    {
        return !$this->getSetting('maintenance_mode') ? false : $this->getSetting('maintenance_mode')->getValue();
    }
}
