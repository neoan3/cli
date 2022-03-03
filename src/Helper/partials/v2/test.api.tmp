
    function test{{method.pascal}}()
    {
        $response = $this->instance->{{method.camel}}(['some' => 'value']);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('some', $response);
        $this->assertSame('value', $response['some']);
    }
