<?php

namespace Digitalkassa\MDK\Services;

use Digitalkassa\MDK\Net\TransferInterface;
use Digitalkassa\MDK\Entities\Atoms\Taxation;
use Digitalkassa\MDK\Settings\SettingsAbstract;
use Digitalkassa\MDK\Exceptions\SettingsException;
use Digitalkassa\MDK\Exceptions\TransferException;
use Digitalkassa\MDK\Exceptions\NetConnectException;

/**
 * Базовая реализация ConnectorInterface
 */
class ConnectorBase implements ConnectorInterface
{
    /**
     * @param TransferInterface $transfer
     */
    public function __construct(TransferInterface $transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * @inheritDoc
     */
    public function testSettings(SettingsAbstract $settings, string $siteId = ''): bool
    {
        try {
            $response = $this->transfer->getCashBox($settings->extrudeConn($siteId));
        } catch (NetConnectException $e) {
            throw new SettingsException(
                sprintf('Сервер временно недоступен (%d), попробуйте позже', $e->getCode()),
                $e->getCode()
            );
        } catch (TransferException $e) {
            if ($e->getCode() >= 500) {
                throw new SettingsException(
                    sprintf('Сервер временно недоступен (%d), попробуйте позже', $e->getCode()),
                    $e->getCode()
                );
            } else {
                throw new SettingsException(
                    sprintf('Неверные авторизационные данные (%d)', $e->getCode()),
                    $e->getCode()
                );
            }
        }

        if (!($response->taxation & $settings->getTaxation($siteId))) {
            $taxations = Taxation::all();
            $included = [];
            foreach ($taxations as $taxation) {
                if ($response->taxation & $taxation->getCode()) {
                    $included[] = $taxation->getName();
                }
            }

            $sListTaxations = implode(", ", $included);
            $error = "Указанный налог не может быть применен, доступные налогообложения: $sListTaxations";
            throw new SettingsException($error);
        } elseif (array_search($settings->getLocation($siteId), $response->billing_place_list) === false) {
            $sListPlaces = implode(", ", $response->billing_place_list);
            $error = "Указанное место расчетов не может быть использовано, доступные: $sListPlaces";
            throw new SettingsException($error);
        }

        return true;
    }

    //######################################################################
    // PRIVATE
    //######################################################################

    /** @var TransferInterface */
    private $transfer = null;
}
