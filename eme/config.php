<?php

/**
 * This file is part of Eme.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
define('EME_ROOT_CMS', realpath(__DIR__ . '/../'));
/**
 *
 */
define('EME_ROOT_PATH', realpath(EME_ROOT_CMS . '/eme/'));
/**
 *
 */
define('SINGLE_CLASS_IDENTIFIER', 'SC_IDENTIFIER');
/**
 *
 */
define('CLASS_MAPPER_PATH', EME_ROOT_CMS . '/storage/classmap/classmap.php');
/**
 *
 */
define('CLASS_MAP_PATHS', [EME_ROOT_PATH, EME_ROOT_PATH . '/../extra']);
