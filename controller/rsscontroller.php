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
        self::authenticateUser();

        $items = $this->service->findAll($id, $type, -1, 0, true, false, $userId);

        $title = 'ownCloud News Feed';
        $description = 'all items of ' . $userId;
        $link = $this->url->linkToRouteAbsolute('feedcentral.rss.index');

        return new RssResponse($items, $title, $description, $link);
    }

    /**
     * @brief Authenticate user by HTTP Basic Authentication with username and password or token
     *
     * Supports login as well as app passwords (tokens).
     * NC: only app passwords are accepted when 2FA is enforced for $user
     *
     * @throws OC\Authentication\Exceptions\PasswordLoginForbiddenException;
     * @throws OC\User\LoginException;
     */
    public static function authenticateUser() {
        $request = \OC::$server->getRequest();

        // force basic auth, enables access through browser
        if (!isset($request->server['PHP_AUTH_USER'])) {
            $defaults = new \OC_Defaults();
            $realm = $defaults->getName();
            header ("HTTP/1.0 401 Unauthorized");
            header ('WWW-Authenticate: Basic realm="' . $realm. '"');
                        exit();
                }

        $user = $request->server['PHP_AUTH_USER'];
        $pass = $request->server['PHP_AUTH_PW'];

        try {
            //if (!\OC::$server->getUserSession()->logClientIn($user, $pass, $request, $throttler)) {
            if (!self::logClientIn($user, $pass, $request)) {
                // unknown user and/or password
                self::changeHttpStatus(401);
                exit();
            }
        } catch (PasswordLoginForbiddenException $ex) {
            // 2FA active and enforced for user so only app passwords are allowed
            self::changeHttpStatus(401);
            exit();
        } catch (LoginException $ex) {
            // login cancelled or user forbidden
            self::changeHttpStatus(403);
            exit();
        }
        }

    /**
     * @brief attempt to login using $user and $pass (password or token)
     *
     * Login using username and password, supports both traditional passwords as well as
     * token-based login ('app passwords').
     *
     * @param string $user
     * @param string $pass
     * @param IRequest $request
     * @throws PasswordLoginForbiddenException
     * @throws LoginException
     * @return boolean
     *
     */
    public static function logClientIn($user, $pass, $request) {
        if (class_exists('OC\Security\Bruteforce\Throttler')) {
            $throttler = \OC::$server->getBruteForceThrottler();
            return \OC::$server->getUserSession()->logClientIn($user, $pass, $request, $throttler);
        } else {
            return \OC::$server->getUserSession()->logClientIn($user, $pass, $request);
        }
    }

        /**
        * @brief Change HTTP response code.
        *
        * @param integer $statusCode The new HTTP status code.
        */
        public static function changeHttpStatus($statusCode) {

                $message = '';
                switch ($statusCode) {
                        case 400: $message = 'Bad Request'; break;
                        case 401: $message = 'Unauthorized'; break;
                        case 403: $message = 'Forbidden'; break;
                        case 404: $message = 'Not Found'; break;
                        case 500: $message = 'Internal Server Error'; break;
                        case 503: $message = 'Service Unavailable'; break;
                }

                // Set status code and status message in HTTP header
                header('HTTP/1.0 ' . $statusCode . ' ' . $message);
        }

}
