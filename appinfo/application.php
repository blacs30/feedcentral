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


namespace OCA\FeedCentral\AppInfo;

use OCP\AppFramework\App;
use OCA\News\AppInfo\Application as News;

class Application extends App {

    /**
     * Define your dependencies in here
     */
    public function __construct (array $urlParams=[]) {
        parent::__construct('feedcentral', $urlParams);

        $container = $this->getContainer();

        $container->registerService('NewsContainer', function($c) {
            $app = new News();
            return $app->getContainer();
        });

        $container->registerService('OCA\News\Service\ItemService', function($c) {
            return $c->query('NewsContainer')->query('OCA\News\Service\ItemService');
        });
    }

}