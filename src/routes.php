<?php

use Slim\Http\Request;
use Slim\Http\Response;

use FeedWriter\ATOM;

const SOURCE_JSON_URL = 'https://www.shibukei.com/headline.php?mode=async&category=headline&limit=50&from=1';
const FEED_TITLE = 'シブヤ経済新聞 最新ヘッドライン';
const SITE_URL = 'https: //www.shibukei.com/';
const ARTICLE_BASE_URL = 'https://www.shibukei.com/headline/%d/';
const ARTICLE_AUTHOR = 'シブヤ経済新聞';
const ARTICLE_IMG_PATH = 'https://images.keizai.biz/shibukei/headline/';

// Routes
$app->get('/atom', function (Request $request, Response $response, array $args) {

    $source = json_decode(file_get_contents(SOURCE_JSON_URL));
    $feed = new ATOM;
    $feed->setTitle(FEED_TITLE);
    $feed->setLink(SITE_URL);
    $feed->setDate(new \DateTime());

    foreach ($source->items as $item) {
        $feedItem = $feed->createNewItem();
        $feedItem->setTitle($item->title);
        $feedItem->setLink(sprintf(ARTICLE_BASE_URL, $item->id));
        $feedItem->setDate(date_create_from_format('Y-m-d H:i:s', $item->start));
        $feedItem->setAuthor(ARTICLE_AUTHOR);
        $feedItem->setContent('<p><img src="'.ARTICLE_IMG_PATH.$item->image.'" alt="'.$item->title.'"></p>');
        $feed->addItem($feedItem);
    }

    $xml = $feed->generateFeed();

    $response = $response->withHeader('Content-type', 'application/xml');

    $body = $response->getBody();
    $body->write($xml);

    return $response;
});
