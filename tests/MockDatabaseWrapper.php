<?php


class MockDatabaseWrapper implements \Migration\DataBase
{
    public array $expectedOutcomes;
    public int $step = 0;
    public array $credentials;
    function __construct(array $expectedOutcomes = [])
    {
        $this->expectedOutcomes = $expectedOutcomes;
    }
    function query($table, $condition = [], $extra = []): ?array
    {
        $outcome = $this->expectedOutcomes[$this->step];
        $this->step++;
        if($outcome instanceof Exception){
            throw $outcome;
        }
        return $outcome;
    }
    function connect($credentials): void
    {
        $this->credentials = $credentials;
    }
}