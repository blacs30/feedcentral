<?php
/**
 * ownCloud - feedcentral
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Bernhard Posselt <dev@bernhard-posselt.com>
 * @copyright Bernhard Posselt 2015
 */

use OCP\App;

if (App::isEnabled('news')) {
    return [
        'routes' => [
           ['name' => 'rss#index', 'url' => '/rss', 'verb' => 'GET'],
           ['name' => 'rss#preflighted_cors', 'url' => '/rss/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
        ]
    ];
}