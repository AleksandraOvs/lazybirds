<?php

use PHPUnit\Framework\TestCase;
use Digitalkassa\MDK\Settings\SettingsConn;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @uses Digitalkassa\MDK\Settings\SettingsConn
 */
class SettingsConnTest extends TestCase
{
    /**
     * @covers Digitalkassa\MDK\Settings\SettingsConn::__construct
     * @covers Digitalkassa\MDK\Settings\SettingsConn::getActorId
     * @covers Digitalkassa\MDK\Settings\SettingsConn::getActorToken
     * @covers Digitalkassa\MDK\Settings\SettingsConn::getCashbox
     */
    public function test()
    {
        $settingsConn = new SettingsConn('0', '1', '2');

        $this->assertSame('0', $settingsConn->getActorId());
        $this->assertSame('1', $settingsConn->getActorToken());
        $this->assertSame('2', $settingsConn->getCashbox());
    }
}
