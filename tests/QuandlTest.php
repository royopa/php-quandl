<?php

use Royopa\Quandl\Quandl;

class QuandlTest extends PHPUnit_Framework_TestCase
{
    private $api_key  = "DEBUG_KEY";

    private $symbol     = 'WIKI/AAPL';

    private $symbols    = [
        'WIKI/CSCO',
        'WIKI/AAPL'
    ];

    private $dates      = [
        'trim_start' => '2014-01-01',
        'trim_end'   => '2014-02-02'
    ];

    protected function setUp()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__.'/../');
        $dotenv->load();
        $this->api_key = getenv('QUANDL_API_KEY');
    }

    private $cache_file = false;

    public function tearDown()
    {
        $this->cache_file and unlink($this->cache_file);
    }

    public function testCsv()
    {
        $this->_testGetSymbol("csv", 2800);
        $this->_testGetSymbol("csv", 2800, true);
    }

    public function testXml()
    {
        $this->_testGetSymbol("xml", 14000);
        $this->_testGetSymbol("xml", 14000, true);
    }

    public function testJson()
    {
        $this->_testGetSymbol("json", 4200);
        $this->_testGetSymbol("json", 4200, true);
    }

    public function testObject()
    {
        $this->_testGetSymbol("object", 7400);
        $this->_testGetSymbol("object", 7400, true);
    }

    public function testInvalidUrl()
    {
        $this->_testInvalidUrl();
        $this->_testInvalidUrl(true);
    }

    public function testCache()
    {
        $this->_testCache();
        $this->cache_file and unlink($this->cache_file);
        $this->_testCache(true);
    }

    public function cacheHandler($action, $url, $data=null)
    {
        $cache_key  = md5("quandl:$url");
        $cache_file = __DIR__ . "/$cache_key";

        if($action == "get" and file_exists($cache_file)) 
            return file_get_contents($cache_file);
        else if($action == "set") 
            file_put_contents($cache_file, $data);

        $this->cache_file = $cache_file;
        
        return false;
    }

    private function _testInvalidUrl($force_curl = false)
    {
        $quandl = new Quandl($this->api_key, "json");
        $quandl->force_curl = $quandl->no_ssl_verify = $force_curl;
        $result = $quandl->getSymbol("INVALID/SYMBOL", $this->dates);
        
        $this->assertEquals(
            $quandl->error,
            "URL not found or invalid URL", 
            "TEST invalidUrl response"
        );
    }

    private function _testGetList($force_curl = false)
    {
        $quandl = new Quandl($this->api_key);
        $quandl->force_curl = $quandl->no_ssl_verify = $force_curl;
        $result = $quandl->getList("WIKI", 1, 223);
        
        $this->assertEquals(
            223,
            count($result->docs),
            "TEST getList count"
        );
    }

    private function _testGetSearch($force_curl = false)
    {
        $quandl = new Quandl($this->api_key);
        $quandl->force_curl = $quandl->no_ssl_verify = $force_curl;
        $result = $quandl->getSearch("crud oil", 1, 220);
        
        $this->assertEquals(220, count($result->docs), "TEST getSearch count");
    }

    private function _testCache($force_curl = false)
    {
        $quandl = new Quandl($this->api_key);
        $quandl->force_curl = $quandl->no_ssl_verify = $force_curl;
        $quandl->cache_handler = array($this, "cacheHandler");
        $result = $quandl->getSymbol($this->symbol, $this->dates);
        $count = count($result->data);
        
        $this->assertFalse($quandl->was_cached, "TEST was_cache should be false");

        $result = $quandl->getSymbol($this->symbol, $this->dates);
        
        $this->assertEquals(
        	$count,
        	count($result->data), 
            "TEST count before and after cache should match"
		);

        $this->assertTrue($quandl->was_cached, "TEST was_cache should be true");
    }

    private function _testGetSymbol($format, $length, $force_curl = false)
    {
        $quandl = new Quandl($this->api_key, $format);
        $quandl->force_curl = $quandl->no_ssl_verify = $force_curl;
        $result = $quandl->getSymbol($this->symbol, $this->dates);
        $quandl_format = $format;
        
        if(is_object($result)) {
            $result = serialize($result);
            $quandl_format = "json";
        }

        $this->assertGreaterThan(
            $length,
            strlen($result), 
            "TEST $format length");

        $url_expected = "https://www.quandl.com/api/v1/datasets/{$this->symbol}.{$quandl_format}?trim_start={$this->dates['trim_start']}&trim_end={$this->dates['trim_end']}&auth_token={$this->api_key}";
        
        $this->assertEquals(
            $url_expected,
            $quandl->last_url,
            "TEST $format url");
    }
}
