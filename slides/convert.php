<?php
/*
* This file is part of the Orbitale EasyImpress package.
*
* (c) Alexandre Rock Ancelet <alex@orbitale.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

// Simple batch script to automatically resize images with ImageMagick

$dirs = glob('*');

foreach  ($dirs as $dir) {

    if (is_dir($dir)) {

        $files = glob($dir.'/img/*.jpg');
        
        $str = 'convert "%s" -resize 1024x768 -quality 70 "%s"';

        foreach ($files as $file) {
        
            $optimized = str_replace('.jpg', '_mobile.jpg', $file);
                    
            if (strpos($file, 'thumb_') === false && strpos($file, '_mobile') === false) {
                $cmd = sprintf($str, $file, $optimized);
                system($cmd);
                echo $cmd."\n";
            }
        }
    }
}
