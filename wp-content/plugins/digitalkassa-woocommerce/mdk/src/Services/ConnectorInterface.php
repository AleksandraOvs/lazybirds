<?php

namespace Digitalkassa\MDK\Services;

use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Exceptions\SettingsException;

/**
 * Интерфейс сервиса тестирования соединения и настроек
 */
interface ConnectorInterface
{
    /**
     * Тестирование соединения и настроек модуля на соответствие данным кассы
     *
     * @throws SettingsException
     *
     * @param SettingsAbstract $settings
     * @param string $siteId
     * @return bool true в случае успеха
     */
    public function testSettings(SettingsAbstract $settings, string $siteId = ''): bool;
}
