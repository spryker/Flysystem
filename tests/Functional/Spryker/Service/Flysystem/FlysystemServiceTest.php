<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Service\Flysystem;

use Codeception\Configuration;
use Flysystem\Stub\FlysystemConfigStub;
use League\Flysystem\Filesystem;
use PHPUnit_Framework_TestCase;
use Spryker\Service\Flysystem\FlysystemService;
use Spryker\Service\Flysystem\FlysystemServiceFactory;

/**
 * @group Functional
 * @group Spryker
 * @group Service
 * @group Flysystem
 * @group FlysystemServiceTest
 */
class FlysystemServiceTest extends PHPUnit_Framework_TestCase
{

    const RESOURCE_FILE_NAME = 'fileName.jpg';

    const STORAGE_DOCUMENT = 'customerStorage';
    const STORAGE_PRODUCT_IMAGE = 'productStorage';

    const ROOT_DIRECTORY = 'fileSystemRoot/uploads/';
    const PATH_STORAGE_DOCUMENT = 'documents/';
    const PATH_STORAGE_PRODUCT_IMAGE = 'images/product/';

    const FILE_STORAGE_DOCUMENT = 'customer.txt';
    const FILE_STORAGE_PRODUCT_IMAGE = 'image.png';

    const FILE_CONTENT = 'Hello World';

    /**
     * @var \Spryker\Service\Flysystem\FlysystemServiceInterface
     */
    protected $fileSystemService;

    /**
     * @var string
     */
    protected $testDataFlysystemRootDirectory;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $config = new FlysystemConfigStub();

        $this->testDataFlysystemRootDirectory = Configuration::dataDir() . static::ROOT_DIRECTORY;

        $factory = new FlysystemServiceFactory();
        $factory->setConfig($config);

        $this->fileSystemService = new FlysystemService();
        $this->fileSystemService->setFactory($factory);
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        $this->directoryCleanup();
    }

    /**
     * @return void
     */
    public function testGetStorageByNameWithProduct()
    {
        $flysystem = $this->fileSystemService->getFilesystemByName(static::STORAGE_PRODUCT_IMAGE);

        $this->assertInstanceOf(Filesystem::class, $flysystem);
    }

    /**
     * @return void
     */
    public function testGetStorageByNameWithCustomer()
    {
        $flysystem = $this->fileSystemService->getFilesystemByName(static::STORAGE_DOCUMENT);

        $this->assertInstanceOf(Filesystem::class, $flysystem);
    }

    /**
     * @return void
     */
    public function testFlysystemImplementationCreateDir()
    {
        $fileSystem = $this->fileSystemService->getFilesystemByName(static::STORAGE_DOCUMENT);

        $fileSystem->createDir('/foo');

        $hasFoo = $fileSystem->has('/foo');
        $hasBar = $fileSystem->has('/bar');

        $this->assertTrue($hasFoo);
        $this->assertFalse($hasBar);

        $storageDirectory = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_DOCUMENT . 'foo/';
        $rootDirectoryExists = is_dir($storageDirectory);
        $this->assertTrue($rootDirectoryExists);
    }

    /**
     * @return void
     */
    public function testFlysystemImplementationRename()
    {
        $fileSystem = $this->fileSystemService->getFilesystemByName(static::STORAGE_DOCUMENT);

        $fileSystem->createDir('/foo');
        $fileSystem->rename('/foo', '/bar');

        $hasBar = $fileSystem->has('/bar');
        $hasFoo = $fileSystem->has('/foo');

        $this->assertTrue($hasBar);
        $this->assertFalse($hasFoo);

        $storageDirectory = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_DOCUMENT . 'bar/';
        $rootDirectoryExists = is_dir($storageDirectory);
        $this->assertTrue($rootDirectoryExists);
    }

    /**
     * @return void
     */
    public function testFlysystemImplementationUpload()
    {
        $fileSystem = $this->fileSystemService->getFilesystemByName(static::STORAGE_DOCUMENT);

        $uploadedFilename = $this->testDataFlysystemRootDirectory . static::FILE_STORAGE_DOCUMENT;
        $storageFilename = '/foo/' . static::FILE_STORAGE_DOCUMENT;

        $h = fopen($uploadedFilename, 'w');
        fwrite($h, static::FILE_CONTENT);
        fclose($h);

        $stream = fopen($uploadedFilename, 'r+');
        try {
            if ($fileSystem->has($storageFilename)) {
                $fileSystem->updateStream($storageFilename, $stream);
            } else {
                $fileSystem->writeStream($storageFilename, $stream);
            }
            fclose($stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }

            unlink($uploadedFilename);
        }

        $content = $fileSystem->read($storageFilename);

        $this->assertSame(static::FILE_CONTENT, $content);
    }

    /**
     * @return void
     */
    protected function directoryCleanup()
    {
        try {
            $file = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_DOCUMENT . 'foo/' . static::FILE_STORAGE_DOCUMENT;
            if (is_file($file)) {
                unlink($file);
            }

            $dir = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_DOCUMENT . 'bar';
            if (is_dir($dir)) {
                rmdir($dir);
            }

            $dir = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_DOCUMENT . 'foo';
            if (is_dir($dir)) {
                rmdir($dir);
            }

            $dir = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_DOCUMENT;
            if (is_dir($dir)) {
                rmdir($dir);
            }

            $file = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_PRODUCT_IMAGE . static::FILE_STORAGE_PRODUCT_IMAGE;
            if (is_file($file)) {
                unlink($file);
            }

            $dir = $this->testDataFlysystemRootDirectory . static::PATH_STORAGE_PRODUCT_IMAGE;
            if (is_dir($dir)) {
                rmdir($dir);
            }

            $dir = $this->testDataFlysystemRootDirectory . 'images/';
            if (is_dir($dir)) {
                rmdir($dir);
            }

        } catch (\Exception $e) {

        } catch (\Throwable $e) {

        }
    }

}
