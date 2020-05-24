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

namespace App\Repository\Search;

use App\Model\Search\Tweet;
use Codebird\Codebird;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class TweetRepository
 * @package App\Repository\Search
 */
class TweetRepository implements TweetRepositoryInterface
{
    const CACHE_KEY_ALREADY_FOUND = 'tweet_repository_search.already_found';

    /**
     * @var Codebird
     */
    private $codebird;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var array|null
     */
    private $cacheValue;

    /**
     * TweetRepository constructor.
     * @param Codebird $codebird
     * @param AdapterInterface $cache
     */
    public function __construct(Codebird $codebird, AdapterInterface $cache)
    {
        $this->codebird = $codebird;
        $this->cache = $cache;
        $this->cacheValue = [];
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $limit = 15, string $search = ''): array
    {
        $timeline = $this->requestTimeline($limit, $search);

        $tweetList = [];

        foreach ($timeline as $status) {
            [
                'id' /****/ => $id,
                'text' /**/ => $text,
            ] = $status;
            $tweet = (new Tweet($id, $text));

            $author = $status['user']['screen_name'] ?? null;
            $tweet->setAuthor($author);

            $tweetList[] = $tweet;
        }

        return $tweetList;
    }

    /**
     * @param int $limit
     * @param string $search
     * @return array|array[]
     */
    private function requestTimeline(int $limit, string $search): array
    {
        $limit = max(15, min($limit, 100));
        $search = trim($search);

        $timeline = [];

        if (empty($search)) {
            return $timeline;
        }

        $query = http_build_query([
            'count' /*********/ => $limit,
            'q' /*************/ => $search,
            'result_type' /***/ => 'recent',
        ]);

        /** @var array|array[] $timeline */
        $timeline = $this->codebird
            ->search_tweets($query);
        $timeline = $timeline['statuses'];
        $timeline = array_filter($timeline, 'is_int', ARRAY_FILTER_USE_KEY);
        $timeline = array_filter($timeline, function (array $value): bool {
            return null === ($value['in_reply_to_status_id'] ?? null);
        });

        $alreadyFound = $this->getAlreadyFound($search) ?: [];

        // Array of already found ids is edited inside the closure.
        $timeline = array_filter($timeline, function (array $value) use (&$alreadyFound): bool {
            $exist = in_array($id = $value['id'], $alreadyFound, is_int($id));

            if ($exist) {
                $alreadyFound[] = $id;
            }

            return !$exist;
        });

        $this->setAlreadyFound($search, $alreadyFound);

        $timeline = array_values($timeline);

        return $timeline;
    }

    /**
     * @param string $search
     * @return array|null
     */
    public function getAlreadyFound(string $search): ?array
    {
        $alreadyFound = null;

        try {
            $item = $this->cache
                ->getItem(self::CACHE_KEY_ALREADY_FOUND);

            if ($item->isHit()) {
                $this->cacheValue = is_array($value = $item->get())
                    ? $value
                    : [];

                $alreadyFound = $this->cacheValue[$search] ?? null;
            }
        } catch (InvalidArgumentException $exception) {
        }

        return $alreadyFound;
    }

    /**
     * @param string $search
     * @param array|null $alreadyFound
     */
    public function setAlreadyFound(string $search, ?array $alreadyFound): void
    {
        if (null === $alreadyFound) {
            return;
        }

        $this->cacheValue[$search] = $alreadyFound;

        try {
            $item = $this->cache
                ->getItem(self::CACHE_KEY_ALREADY_FOUND);

            $item->set($this->cacheValue);

            $this->cache
                ->save($item);
        } catch (InvalidArgumentException $exception) {
        }
    }
}