<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Redis;
use RedisException;

class ConnectorFacade
{
    public string $host;
    public int $port = 6379;
    public ?string $password = null;
    public ?int $dbIndex = null;

    public mixed $connector;

    public function __construct(
        $host,
        $port,
        $password,
        $dbIndex
    )
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbIndex = $dbIndex;
    }

    /**
     * @return mixed
     */
    public function getConnector(): mixed
    {
        return $this->connector;
    }

    /**
     * @return void
     *
     * @throws RedisException
     */
    protected function build(): void
    {
        $redis = new Redis();

        try {
            $isConnected = $redis->isConnected();
            if (! $isConnected && $redis->ping('Pong')) {
                $isConnected = $redis->connect(
                    $this->host,
                    $this->port,
                );
            }
        } catch (RedisException) {
        }

        if ($isConnected) {
            $redis->auth($this->password);
            $redis->select($this->dbIndex);
            $this->connector = new Connector($redis);
        }
    }
}
