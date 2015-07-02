<?php

use Royopa\Quandl\Quandl;

class QuandlSymbolTest extends PHPUnit_Framework_TestCase
{
    private $api_key    = "DEBUG_KEY";
    private $cache_file = false;

    protected function setUp()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__.'/../');
        $dotenv->load();
        $this->api_key = getenv('QUANDL_API_KEY');
    }

    public function testGetSymbol()
    {
        $quandl = new Quandl($this->api_key);
        $quandl->force_curl    = false;
        $quandl->no_ssl_verify = true;;

        $symbol = 'GOOG/BVMF_PETR4';
        $dates  = [
            'trim_start' => '2015-06-08',
            'trim_end'   => '2015-06-12'
        ];
        $format = 'json';
        
        $result = $quandl->getSymbol($symbol, $dates);

        // tests to columns names
        $columnsNames = $result->column_names;
        $this->assertEquals('Date', $columnsNames[0]);
        $this->assertEquals('Open', $columnsNames[1]);
        $this->assertEquals('High', $columnsNames[2]);
        $this->assertEquals('Low', $columnsNames[3]);
        $this->assertEquals('Close', $columnsNames[4]);
        $this->assertEquals('Volume', $columnsNames[5]);

        // test to check count results returned
        $data = $result->data;
        $this->assertEquals(5, count($data));

        // test to check url generated
        $urlExpected = "https://www.quandl.com/api/v1/datasets/{$symbol}.{$format}?trim_start={$dates['trim_start']}&trim_end={$dates['trim_end']}&auth_token={$this->api_key}";
        $this->assertEquals($urlExpected, $quandl->last_url);
    }

    public function testGetBacenCdi()
    {
        $quandl = new Quandl($this->api_key);
        $symbol = 'BCB/4389'; # CDI

        $result = $quandl->getSymbol(
            $symbol,
            array(
                'trim_start' => '2015-06-22',
                'trim_end'   => '2015-06-22',
                "sort_order"      => "desc",
                "exclude_headers" => true,
                "rows"            => 1,
            )
        );

        $date  = new \DateTime($result->data[0][0]);
        $value = $result->data[0][1];

        $this->assertEquals(13.64, $value);
        $this->assertEquals(new \DateTime('2015-06-22'), $date);
    }
}
