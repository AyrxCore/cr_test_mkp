<?php

namespace App\Service;

use App\Repository\SettingRepository;

class SettingsService
{
    public function __construct(private SettingRepository $settingRepository){}

    public function getSetting($settingName)
    {
        return $this->settingRepository->findOneBy(['name' => $settingName]);
    }

    public function useExternalScriptsTags()
    {
        return !$this->getSetting('external_scripts_tags') ? false : $this->getSetting('external_scripts_tags')->getValue();
    }

    public function isMaintenanceMode()
    {
        return !$this->getSetting('maintenance_mode') ? false : $this->getSetting('maintenance_mode')->getValue();
    }
}
