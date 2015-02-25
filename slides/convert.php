<?php

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