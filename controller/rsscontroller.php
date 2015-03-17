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

namespace OCA\FeedCentral\Controller;

use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\AppFramework\ApiController;

use OCA\News\Service\ItemService;
use OCA\News\Db\FeedType;

use OCA\FeedCentral\Http\RssResponse;

class RssController extends ApiController {

    private $service;
    private $url;

    public function __construct($AppName, IRequest $request,
                                ItemService $service, IURLGenerator $url){
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->url = $url;
    }

    /**
     * @CORS
     * @PublicPage
     * @NoCSRFRequired
     *
     * @param string $userId
     * @param int $type
     * @param int $id
     */
    public function index($userId='', $type=FeedType::SUBSCRIPTIONS, $id=0) {
        $items = $this->service->findAll($id, $type, -1, 0, true, false, $userId);

        $title = 'ownCloud News Feed';
        $description = 'all items of ' . $userId;
        $link = $this->url->linkToRouteAbsolute('feedcentral.rss.index');

        return new RssResponse($items, $title, $description, $link);
    }


}
