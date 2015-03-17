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
namespace OCA\FeedCentral\Http;

use OCP\AppFramework\Http\Response;


class RssResponse extends Response {

    public function __construct(array $items, $title, $description, $link) {
        $this->items = $items;
        $this->title = $title;
        $this->description = $description;
        $this->link = $link;
        $this->addHeader('Content-Type', 'application/rss+xml');
    }

    public function render() {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $rss = $document->createElement("rss");
        $rss->setAttribute('version', '2.0');
        $document->appendChild($rss);

        $channel = $rss->appendChild($document->createElement("channel"));

        $linkElement = $document->createElement('link');
        $linkElement->appendChild($document->createTextNode($this->link));
        $channel->appendChild($linkElement);

        $descriptionElement = $document->createElement('description');
        $descriptionElement->appendChild($document->createTextNode($this->description));
        $channel->appendChild($descriptionElement);

        $titleElement = $document->createElement('title');
        $titleElement->appendChild($document->createTextNode($this->title));
        $channel->appendChild($titleElement);

        $this->createItems($document, $channel, $this->items);

        return $document->saveXML();
    }

    private function createItems($document, $channel, $items) {
        foreach ($items as $item) {
            $itemElement = $document->createElement('item');

            $titleElement = $document->createElement('title');
            $titleElement->appendChild($document->createTextNode($item->getTitle()));
            $itemElement->appendChild($titleElement);

            $descriptionElement = $document->createElement('description');
            $descriptionElement->appendChild($document->createTextNode($item->getBody()));
            $itemElement->appendChild($descriptionElement);

            $linkElement = $document->createElement('link');
            $linkElement->appendChild($document->createTextNode($item->getUrl()));
            $itemElement->appendChild($linkElement);

            $guidElement = $document->createElement('guid');
            $guidElement->appendChild($document->createTextNode($item->getGuid()));
            $itemElement->appendChild($guidElement);

            $formatedDate = date('r', $item->getPubDate());
            $pubDateElement = $document->createElement('pubDate');
            $pubDateElement->appendChild($document->createTextNode($formatedDate));
            $itemElement->appendChild($pubDateElement);

            $channel->appendChild($itemElement);
        }
    }

}
