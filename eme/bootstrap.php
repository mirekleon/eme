<?php

/**
 * This file is part of Eme.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @file
 * The PHP page that serves all page requests on a Eme installation.
 *
 */
require __DIR__ . '/core/Function/eme.php';
require __DIR__ . '/config.php';
require EME_ROOT_PATH . '/core/Autoload/loader.php';
/**
 * register autoloader
 */
register_autoloader(CLASS_MAP_PATHS);
/**
 * kick off application
 */
$eme = new Eme\Core\EmeKernel;
$eme->handle();
$eme->send();
$eme->end();
