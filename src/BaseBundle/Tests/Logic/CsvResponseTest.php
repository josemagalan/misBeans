<?php

namespace BaseBundle\Tests\Logic;

use BaseBundle\Controller\Logic\CsvResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CsvResponseTest extends KernelTestCase
{
    public function testCsvResponse()
    {
        $filename = "RankingTest.csv";
        $data = array(array('nombre' => 'test1'), array('nombre' => 'test2'));
        $response = new CsvResponse($data, $filename);

        $this->assertTrue($response->isOk());
        $this->assertEquals($filename, $response->getFilename());
    }

    public function testFilename()
    {
        $filename = "RankingTest.csv";
        $response = new CsvResponse(array(),$filename);
        $filename = "Test.csv";
        $response->setFilename($filename);

        $this->assertEquals($filename, $response->getFilename());
    }
}