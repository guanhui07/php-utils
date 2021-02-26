<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2020 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

namespace Raylin666\Utils;

use SplQueue;
use Raylin666\Utils\Coroutine\Coroutine;
use Raylin666\Utils\Coroutine\Channel;

/**
 * Class Queue
 * @package Raylin666\Utils
 */
class Queue
{
    /**
     * 总容量(总数量)
     * @var int
     */
    protected $capacity = 1;

    /**
     * 通道对象
     * @var Channel
     */
    protected $channel;

    /**
     * 队列
     * @var SplQueue
     */
    protected $queue;

    /**
     * Queue constructor.
     * @param int $capacity
     */
    public function __construct(int $capacity = 1)
    {
        $this->capacity = $capacity;
        $this->queue = new SplQueue();
        $this->channel = new Channel($capacity);
    }

    /**
     * @param float $timeout
     * @return mixed|void
     */
    public function pop(float $timeout)
    {
        if ($this->isCoroutine()) {
            return $this->channel->pop($timeout);
        }

        return $this->queue->shift();
    }

    /**
     * @param $data
     * @return bool
     */
    public function push($data)
    {
        if ($this->isCoroutine()) {
            return $this->channel->push($data);
        }

        $this->queue->push($data);
        return true;
    }

    /**
     * @return int
     */
    public function length(): int
    {
        if ($this->isCoroutine()) {
            return $this->channel->length();
        }

        return $this->queue->count();
    }

    /**
     * @return bool
     */
    protected function isCoroutine(): bool
    {
        return Coroutine::id() > 0;
    }
}