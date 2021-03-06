<?php
namespace Kanti\Test;

use Kanti\CacheOneFile;

class CacheOneFileTest extends \PHPUnit_Framework_TestCase
{
    private $invalidContent = array(
        true,
        false,
        array('content'),
    );

    private $validContent = array(
        '',
        'a',
        'ab',
        'abc',
        1,
        12,
        123,
    );

    /**
     * @group fast
     */
    public function testIs()
    {
        $fileName = __DIR__ . "/asserts/testFile.txt";
        $time = 60 * 60;//1h
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        $cache = new CacheOneFile($fileName, $time);
        if ($cache->has()) {
            $this->fail("is not set");
        }

        touch($fileName);
        if (!$cache->has()) {
            $this->fail("could not set");
        }
        touch($fileName, 1);//1 because of hhvm bug
        if ($cache->has()) {
            $this->fail("does not reset");
        }
    }

    /**
     * @group fast
     */
    public function testGet()
    {
        $fileName = __DIR__ . "/asserts/testFile.txt";
        $time = 60 * 60;//1h
        $cache = new CacheOneFile($fileName, $time);

        if (is_dir(dirname($fileName))) {
            rmdir(dirname($fileName));
        }
        foreach ($this->validContent as $value) {
            $cache->set($value);
            if ($cache->get() !== (string)$value) {
                $this->fail("get set dosen't match for value{" . print_r($value, true) . "}");
            }
        }
        unlink($fileName);
        foreach ($this->invalidContent as $value) {
            $cache->set($value);
            if ($cache->get() === $value) {
                $this->fail("get set does match for value{" . print_r($value, true) . "}");
            }
        }
    }
}
