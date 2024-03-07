<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Settings\SettingsConn;
use Digitalkassa\MDK\Settings\SettingsAbstract;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Settings\SettingsAbstract
 * @uses Digitalkassa\MDK\Settings\SettingsConn
 */
class SettingsAbstractTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Settings\SettingsAbstract::extrudeConn
     */
    public function test()
    {
        $settings = $this->getMockForAbstractClass(SettingsAbstract::class);
        $settings
            ->method('getActorId')
            ->willReturn(TEST_ACTOR_ID);
        $settings
            ->method('getActorToken')
            ->willReturn(TEST_ACTOR_TOKEN);
        $settings
            ->method('getCashbox')
            ->willReturn(TEST_CASHBOX_WITHOUT_AGENT);

        $settingsConn = $settings->extrudeConn();
        $this->assertInstanceOf(SettingsConn::class, $settingsConn);
    }
}
