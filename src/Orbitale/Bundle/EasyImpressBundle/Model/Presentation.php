<?php

/**
 * This file is part of the EasyImpress package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orbitale\Bundle\EasyImpressBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Presentation
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $inactiveOpacity;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $attr;

    /**
     * @var Slide[]|ArrayCollection
     */
    protected $slides;

    /**
     * @var SlideData
     */
    protected $increments;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->inactiveOpacity = $config['inactive_opacity'];
        $this->attr = $config['attr'];
        $this->data = $config['data'];
        $this->name = $config['name'];

        $this->slides = new ArrayCollection();
        $this->increments = new SlideData($config['increments']);

        // We need to compute incrementation of the different coordinates.
        $baseIncrements = $this->increments->toArray();
        $currentIncrementsArray = array_filter($baseIncrements);
        $incrementsToProcess = array_keys($currentIncrementsArray);

        foreach ($config['slides'] as $slideArray) {
            foreach ($incrementsToProcess as $dataKey) {
                if (array_key_exists($dataKey, $slideArray['data'])) {
                    throw new \InvalidArgumentException(sprintf(
                        'As you have increments in the "%s" presentation, ' .
                        'you cannot use value for the "%s" data property '.
                        'in slide "%s"',
                        $this->name, $dataKey, $slideArray['id']
                    ));
                }

                // Check if we have to reset value before calculating incrementation values.
                if (array_key_exists($dataKey, $slideArray['reset']) && true === $slideArray['reset'][$dataKey]) {
                    $currentIncrementsArray[$dataKey] = $baseIncrements[$dataKey];
                }

                // Update slide values.
                $slideArray['data'][$dataKey] = $currentIncrementsArray[$dataKey];

                // Update incrementation values.
                $currentIncrementsArray[$dataKey] += $baseIncrements[$dataKey];
            }

            $slide = new Slide($slideArray, $this);
            $this->slides->add($slide);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getInactiveOpacity()
    {
        return $this->inactiveOpacity;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * @return ArrayCollection|Slide[]
     */
    public function getSlides()
    {
        return $this->slides;
    }

    /**
     * @return SlideData
     */
    public function getIncrements()
    {
        return $this->increments;
    }
}
