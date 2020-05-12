<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2020 GameplayJDK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Repository\Sentiment;

use App\Model\Sentiment\Tweet;
use App\Service\Sentiment\SentimentService;
use Codebird\Codebird;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class TweetRepository
 * @package App\Repository\Sentiment
 */
class TweetRepository implements TweetRepositoryInterface
{
    const CACHE_KEY_LAST_ID = "tweet_repository_sentiment.last_id";
    /**
     * @var Codebird
     */
    private $codebird;

    /**
     * @var SentimentService
     */
    private $sentimentService;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * TweetRepository constructor.
     * @param Codebird $codebird
     * @param SentimentService $sentimentService
     * @param AdapterInterface $cache
     */
    public function __construct(Codebird $codebird, SentimentService $sentimentService, AdapterInterface $cache)
    {
        $this->codebird = $codebird;
        $this->sentimentService = $sentimentService;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $limit = 20): array
    {
        $timeline = $this->requestTimeline($limit);

        $tweetList = [];

        foreach ($timeline as $status) {
            [
                'id' /****/ => $id,
                'text' /**/ => $text,
            ] = $status;
            $tweet = (new Tweet($id, $text));

            $author = $status['user']['screen_name'] ?? null;
            $tweet->setAuthor($author);

            $sentiment = $this->sentimentService
                ->analyze($text);
            $tweet->setSentiment($sentiment);

            $tweetList[] = $tweet;
        }

        return $tweetList;
    }

    /**
     * @param int $limit
     * @return array|array[]
     */
    private function requestTimeline(int $limit): array
    {
        $limit = max(20, min($limit, 200));

        $queryData = [
            'count' /*****/ => $limit,
        ];
        if (null !== ($lastId = $this->getLastId())) {
            $queryData['since_id'] = $lastId;
        }

        $query = http_build_query($queryData);

        /** @var array|array[] $timeline */
        $timeline = $this->codebird
            ->statuses_mentionsTimeline($query);
        $timeline = array_filter($timeline, function ($value, $key): bool {
            return is_int($key) && null === ($value['in_reply_to_status_id'] ?? null);
        }, ARRAY_FILTER_USE_BOTH);

        $status = reset($timeline);
        if (false !== $status && null !== ($lastId = $status['id'] ?? null)) {
            $this->setLastId($lastId);
        }

        return $timeline;
    }

    /**
     * @return int|null
     */
    public function getLastId(): ?int
    {
        $lastId = null;

        try {
            $item = $this->cache
                ->getItem(self::CACHE_KEY_LAST_ID);

            if ($item->isHit()) {
                $lastId = is_int($value = $item->get())
                    ? $value
                    : null;
            }
        } catch (InvalidArgumentException $exception) {
        }

        return $lastId;
    }

    /**
     * @param int|null $lastId
     */
    public function setLastId(?int $lastId): void
    {
        if (null === $lastId) {
            return;
        }

        try {
            $item = $this->cache
                ->getItem(self::CACHE_KEY_LAST_ID);

            $item->set($lastId);

            $this->cache
                ->save($item);
        } catch (InvalidArgumentException $exception) {
        }
    }
}
