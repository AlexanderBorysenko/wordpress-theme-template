<?php

namespace ThemeCore\Contracts;

/**
 * Interface ThemeModuleInterface
 *
 * Определяет контракт для всех модулей фреймворка. Каждый модуль должен реализовывать этот интерфейс, чтобы обеспечить единообразную инициализацию.
 */
interface ThemeModuleInterface
{
    public static function getInstance();

    public static function initModule(array $config = []): void;

}
