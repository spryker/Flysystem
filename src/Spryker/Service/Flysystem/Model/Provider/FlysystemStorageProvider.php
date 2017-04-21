<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\Flysystem\Model\Provider;

use Spryker\Service\Flysystem\Exception\FlysystemStorageNotFoundException;

class FlysystemStorageProvider implements FlysystemStorageProviderInterface
{

    /**
     * @var \Spryker\Service\Flysystem\Model\FlysystemStorageInterface[]
     */
    protected $storageCollection;

    /**
     * @param \Spryker\Service\Flysystem\Model\FlysystemStorageInterface[] $storageCollection
     */
    public function __construct(array $storageCollection)
    {
        $this->storageCollection = $storageCollection;
    }

    /**
     * @param string $name
     *
     * @throws \Spryker\Service\Flysystem\Exception\FlysystemStorageNotFoundException
     *
     * @return \Spryker\Service\Flysystem\Model\FlysystemStorageInterface
     */
    public function getStorageByName($name)
    {
        if (!array_key_exists($name, $this->storageCollection)) {
            throw new FlysystemStorageNotFoundException(
                sprintf('FlysystemStorage "%s" was not found', $name)
            );
        }

        return $this->storageCollection[$name];
    }

    /**
     * @return \Spryker\Service\Flysystem\Model\FlysystemStorageInterface[]
     */
    public function getStorageCollection()
    {
        return $this->storageCollection;
    }

}
