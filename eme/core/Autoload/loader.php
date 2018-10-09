<?php

/**
 * @file
 * Auto class loader script.
 *
 * Dump autoloader
 */
if (!function_exists('dump_autoloader')) {
    function dump_autoloader($paths)
    {
        include_once EME_ROOT_PATH . '/core/Finder/FileInfo.php';
        include_once EME_ROOT_PATH . '/core/Finder/Finder.php';
        include_once EME_ROOT_PATH . '/core/Autoload/Exception/AutoLoadException.php';
        include_once EME_ROOT_PATH . '/core/Autoload/ClassMapGenarator.php';
        include_once EME_ROOT_PATH . '/core/Autoload/ClassMapCreator.php';
        $classMapGenarator = new \Eme\Core\Autoload\ClassMapGenarator();
        $classMapGenarator->setPaths($paths)->generateClassMap();
        $classes = $classMapGenarator->getClassMap();
        \Eme\Core\Autoload\ClassMapCreator::create(CLASS_MAPPER_PATH, $classes);
        register_autoloader($paths);
        return $classes;
    }
}
/**
 *
 */
if (!function_exists('register_autoloader')) {
    function register_autoloader(array $paths)
    {
        if (!file_exists(CLASS_MAPPER_PATH)) {
            dump_autoloader($paths);
        }
        if (file_exists(CLASS_MAPPER_PATH)) {
            include_once EME_ROOT_PATH . '/core/Autoload/AutoClassLoader.php';
            $classMap = include CLASS_MAPPER_PATH;
            $loader = new \Eme\Core\Autoload\AutoClassLoader();
            $loader->addClassMap($classMap);
            $loader->register();
        }
    }
}
