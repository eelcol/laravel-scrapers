<?php

namespace Eelcol\LaravelScrapers\Support;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

class Lock
{
    /**
     * @throws LockTimeoutException
     */
    public static function create(string $key, ?int $numberOfLocks, int $secondsToLock, $callback): mixed
    {
        if (!$numberOfLocks) {
            return $callback();
        }

        $numberOfLocks = max(1, $numberOfLocks);
        $cacheId = "lock::" . $key;
        $numSleeps = 0;

        while (true) {
            $current = Cache::get($cacheId, []);
            foreach ($current as $k => $v) {
                if (microtime(true) - $v > $secondsToLock) {
                    // remove from current
                    unset($current[$k]);
                }
            }

            $current = array_values($current);
            if (count($current) < $numberOfLocks) {
                break;
            }

            $numSleeps++;
            if ($numSleeps > 60) {
                throw new LockTimeoutException($key);
            }

            sleep (1);
        }

        // add to cache
        $cacheItem = microtime(true);
        $current[] = $cacheItem;

        Cache::put($cacheId, $current);

        $return = $callback();

        // remove item from array
        $lockValue = Cache::get($cacheId, []);
        foreach ($lockValue as $k => $v) {
            if ($v == $cacheItem) {
                unset($lockValue[$k]);
                break;
            }
        }

        // save new array to cache
        Cache::put($cacheId, $lockValue);

        return $return;
    }
}