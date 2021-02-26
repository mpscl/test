<?php
declare(strict_types=1);

namespace App\Helpers;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppHelper
{
    public const SALT_RANDOM_CODE_CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    // Languages
    public const LANG_ES = 'es';
    public const LANG_CA = 'ca';

    // File export types
    public const FILE_EXPORT_TYPE_CSV = 'csv';


    /**
     * Gets the application's dependency container.
     *
     * @return ContainerInterface|null
     */
    public static function getContainerInterface(): ?ContainerInterface
    {
        global $kernel;

        return $kernel->getContainer();
    }

    /**
     * Gets the translation container.
     *
     * @return TranslatorInterface
     */
    public static function getTranslatorInterface(): TranslatorInterface
    {
        $kernel = self::getContainerInterface();

        return $kernel->get('translator');
    }

    /**
     * Gets the request stack
     *
     * @return RequestStack
     */
    public static function getRequestStack(): RequestStack
    {
        return self::getContainerInterface()->get('request_stack');
    }

    /**
     * Gets base path of the application.
     *
     * @return string
     */
    public static function getBaseUrl(): string
    {
        $request = self::getContainerInterface()->get('request_stack')->getCurrentRequest();

        return $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath();
    }

    /**
     * Gets complete route using a route name.
     *
     * @param string $routeName
     * @return string
     */
    public static function getFullPathByRouteName(string $routeName): string
    {
        $baseUrl = self::getBaseUrl();
        $router = self::getContainerInterface()->get('router');
        $routesCollection = $router->getRouteCollection();

        return $baseUrl.$routesCollection->get($routeName)->getPath();
    }
}