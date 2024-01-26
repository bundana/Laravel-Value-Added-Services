<?php

use Bundana\Services\Messaging\Mnotify;
use PHPUnit\Framework\TestCase;

class MnotifyTest extends TestCase
{
    public function testSendSuccess()
    {
        $mnotify = $this->getMockBuilder(Mnotify::class)
            ->onlyMethods(['smsApiRequest'])
            ->getMock();

        // Mock the API request to return a success response
        $mnotify->expects($this->any())
            ->method('smsApiRequest')
            ->willReturn(json_encode(['success' => true, 'message' => 'Test message', 'code' => 2000]));

        // Set the mocked instance on the actual class
        Mnotify::$instance = $mnotify;

        // Now you can test the send method
        $response = Mnotify::to('0542345921')->message('Test message')->send();

        $this->assertJson($response);
        $this->assertTrue(json_decode($response)->success);
    }

    public function testSendFailure()
    {
        $mnotify = $this->getMockBuilder(Mnotify::class)
            ->onlyMethods(['smsApiRequest'])
            ->getMock();

        // Mock the API request to return an error response
        $mnotify->expects($this->any())
            ->method('smsApiRequest')
            ->willReturn(json_encode(['success' => false, 'message' => 'Test error', 'code' => 4001]));

        // Set the mocked instance on the actual class
        Mnotify::$instance = $mnotify;

        // Now you can test the send method
        $response = Mnotify::to('0542345921')->message('Test message')->send();

        $this->assertJson($response);
        $this->assertFalse(json_decode($response)->success);
    }

    // Add more test cases for other methods and scenarios
}
