<?php
declare(strict_types=1);
namespace Bitmotion\Auth0\Tests\Functional\Api;

use Bitmotion\Auth0\Api\Management\LogApi;
use Bitmotion\Auth0\Domain\Model\Auth0\Management\Log;
use Bitmotion\Auth0\Scope;
use Bitmotion\Auth0\Tests\Functional\Auth0TestCase;

class LogTest extends Auth0TestCase
{
    protected $scopes = [
        Scope::LOG_READ,
    ];

    /**
     * Tries to instantiate the LogApi
     *
     * @test
     * @covers \Bitmotion\Auth0\Utility\ApiUtility::getLogApi
     */
    public function instantiateApi(): LogApi
    {
        $logApi = $this->getApiUtility()->getLogApi(...$this->scopes);
        $this->assertInstanceOf(LogApi::class, $logApi);

        return $logApi;
    }

    /**
     * @test
     * @depends instantiateApi
     * @covers \Bitmotion\Auth0\Api\Management\LogApi::search
     */
    public function search(LogApi $logApi)
    {
        $logs = $logApi->search('');
        $this->assertIsArray($logs);
        $this->assertCount(50, $logs);

        $entry = $logs[10];
        $this->assertInstanceOf(Log::class, $entry);

        return $entry;
    }

    /**
     * @test
     * @depends instantiateApi
     * @depends search
     * @covers \Bitmotion\Auth0\Api\Management\LogApi::searchByCheckpoint
     */
    public function searchByCheckpoint(LogApi $logApi, Log $entry)
    {
        $logs = $logApi->searchByCheckpoint($entry->getLogid(), 5);
        $this->assertIsArray($logs);
        $this->assertCount(5, $logs);
    }

    /**
     * @test
     * @depends instantiateApi
     * @depends search
     * @covers \Bitmotion\Auth0\Api\Management\LogApi::get
     */
    public function get(LogApi $logApi, Log $entry)
    {
        $log = $logApi->get($entry->getLogId());
        $this->assertInstanceOf(Log::class, $log);
        $this->assertEquals($entry->getLogId(), $log->getLogId());
    }
}
