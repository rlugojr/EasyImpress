<?php

/**
 * This file is part of the EasyImpress package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orbitale\Bundle\EasyImpressBundle\Impress;

use Orbitale\Bundle\EasyImpressBundle\Model\Presentation;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Finder\Finder;

class Impress
{
    /**
     * @var Presentation[]
     */
    protected $presentations;

    /**
     * @var AdapterInterface
     */
    protected $cacheAdapter;

    /**
     * @var ConfigProcessor
     */
    protected $configProcessor;

    /**
     * @var string
     */
    protected $presentationsDir;

    public function __construct(AdapterInterface $cacheAdapter, ConfigProcessor $configProcessor, $presentationsDir)
    {
        $this->cacheAdapter     = $cacheAdapter;
        $this->configProcessor = $configProcessor;
        $this->presentationsDir = $presentationsDir;
    }

    /**
     * @param string $name
     *
     * @return Presentation
     */
    public function getPresentation($name)
    {
        // Try to get presentation from cache
        $item = $this->cacheAdapter->getItem('presentations.'.$name);

        if ($item->isHit() && $item->get() instanceof Presentation) {
            return $item->get();
        }

        $item->set($this->doGetPresentation($name));
        $this->cacheAdapter->save($item);

        return $item->get();
    }

    /**
     * @return Presentation[]
     */
    public function getAllPresentations()
    {
        // Try to get presentation list from cache
        $item = $this->cacheAdapter->getItem('presentations');

        if ($item->isHit()) {
            $itemValue = $item->get();
            if (count($itemValue) && reset($itemValue) instanceof Presentation) {
                return $item->get();
            }
        }

        $item->set($this->doGetPresentations());
        $this->cacheAdapter->save($item);

        return $item->get();
    }

    /**
     * @param string $name
     *
     * @return null|Presentation
     */
    private function doGetPresentation($name)
    {
        // Get all presentations first
        $presentations = $this->getAllPresentations();

        if (!array_key_exists($name, $presentations)) {
            return null;
        }

        return $presentations[$name];
    }

    /**
     * @return Presentation[]
     */
    private function doGetPresentations()
    {
        $finder = (new Finder())
            ->files()
            ->name('*.yml')
            ->in($this->presentationsDir)
        ;

        $presentations = [];

        foreach ($finder as $file) {
            $presentations[basename($file, '.yml')] = $this->configProcessor->processConfigurationFile($file);
        }

        return $presentations;
    }
}
