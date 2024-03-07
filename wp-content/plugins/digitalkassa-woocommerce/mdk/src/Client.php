<?php

namespace Digitalkassa\MDK;

use Digitalkassa\MDK\Services\PipelineInterface;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Services\AutomaticInterface;
use Digitalkassa\MDK\Services\ConnectorInterface;
use Digitalkassa\MDK\Storage\ReceiptStorageInterface;

/**
 * Клиент основанный на сервисах для использования API Pangaea v2.
 */
class Client
{
    public function __construct(
        SettingsAbstract $settings,
        ReceiptStorageInterface $storage,
        AutomaticInterface $atomatic,
        PipelineInterface $pipeline,
        ConnectorInterface $connector
    ) {
        $this->settings = $settings;
        $this->storage = $storage;

        $this->atomatic = $atomatic;
        $this->pipeline = $pipeline;
        $this->connector = $connector;
    }

    //######################################################################

    /**
     * Получить сервис автоматической фискализации
     *
     * @return AutomaticInterface
     */
    public function serviceAutomatic(): AutomaticInterface
    {
        return $this->atomatic;
    }

    /**
     * Получить сервис обработки очереди чеков
     *
     * @return PipelineInterface
     */
    public function servicePipeline(): PipelineInterface
    {
        return $this->pipeline;
    }

    /**
     * Получить сервис тестирования соединения с сервером фискализации
     *
     * @return ConnectorInterface
     */
    public function serviceConnector(): ConnectorInterface
    {
        return $this->connector;
    }

    //######################################################################

    /**
     * Получить компонент настроек
     *
     * @return SettingsAbstract
     */
    public function componentSettings(): SettingsAbstract
    {
        return $this->settings;
    }

    /**
     * Получить компонент хранилища чеков
     *
     * @return ReceiptStorageInterface
     */
    public function componentStorage(): ReceiptStorageInterface
    {
        return $this->storage;
    }

    //######################################################################
    // PRIVATE
    //######################################################################

    /** @var AutomaticInterface */
    private $atomatic;

    /** @var PipelineInterface */
    private $pipeline;

    /** @var ConnectorInterface */
    private $connector;

    /** @var SettingsAbstract */
    private $settings;

    /** @var ReceiptStorageInterface */
    private $storage;
}
