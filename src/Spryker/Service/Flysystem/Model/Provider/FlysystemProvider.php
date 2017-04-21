<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\Flysystem\Model\Provider;

use Spryker\Service\Flysystem\Exception\FlysystemNotFoundException;

class FlysystemProvider implements FlysystemProviderInterface
{

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $storageCollection;

    /**
     * @param \League\Flysystem\Filesystem[] $storageCollection
     */
    public function __construct(array $storageCollection)
    {
        $this->storageCollection = $storageCollection;
    }

    /**
     * @param string $name
     *
     * @throws \Spryker\Service\Flysystem\Exception\FlysystemNotFoundException
     *
     * @return \League\Flysystem\Filesystem
     */
    public function getFilesystemByName($name)
    {
        if (!array_key_exists($name, $this->storageCollection)) {
            throw new FlysystemNotFoundException(
                sprintf('Flysystem "%s" was not found', $name)
            );
        }

        return $this->storageCollection[$name];
    }

    /**
     * @return \League\Flysystem\Filesystem[]
     */
    public function getFilesystemCollection()
    {
        return $this->storageCollection;
    }

}
