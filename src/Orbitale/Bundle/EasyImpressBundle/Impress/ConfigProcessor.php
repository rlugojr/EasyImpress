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

use Orbitale\Bundle\EasyImpressBundle\Configuration\PresentationConfiguration;
use Orbitale\Bundle\EasyImpressBundle\Configuration\SlideConfiguration;
use Orbitale\Bundle\EasyImpressBundle\Model\Presentation;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class ConfigProcessor
{
    /**
     * @param string $file
     *
     * @return Presentation
     */
    public function processConfigurationFile($file)
    {
        $yamlArray = Yaml::parse(file_get_contents($file));

        // Presentation name based on the file name.
        $yamlArray['name'] = basename($file, '.yml');

        $processor = new Processor();

        $presentationConfig = $processor->processConfiguration(new PresentationConfiguration(), [$yamlArray]);
        foreach ($presentationConfig['slides'] as $k => $slide) {
            if (!array_key_exists('id', $slide)) {
                $slide['id'] = $presentationConfig['name'].'_'.$k;
            }
            $presentationConfig['slides'][$k] = $processor->processConfiguration(new SlideConfiguration(), [$slide]);
        }

        return new Presentation($presentationConfig);
    }
}
