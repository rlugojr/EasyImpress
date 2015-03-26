# Impress Portfolio with Silex

This is a simple [Silex](http://silex.sensiolabs.org/) application using the awesome
[Impress.js](https://github.com/bartaz/impress.js) to create sliders based on a single `yml`
configuration file.

# Requirements

* PHP 5.3+
* [Composer](https://getcomposer.org/)
* [ImageMagick](http://www.imagemagick.org/script/binary-releases.php) installed and available
with the PHP `exec()` function (only if you are using image-based slides)
* A web-server like [Apache](http://httpd.apache.org/)

# Install

* Just clone the repository, or download it as a `zip` file, and put it in the directory you want.
* Make your webserver document root point on the application `web` directory.
* Copy the `config/config.php.dist` and paste it in `config/config.php`, and change the few 
constants you need.
* Run `composer install` to install all the needed dependencies.
* (optional) If you want to update Impress.js, you just need to run `bower update`, but you obviously need to install
[Bower](http://bower.io/), which needs [NodeJS](https://nodejs.org/) and [Git](http://git-scm.com/).
  The Impress.js repository is not very active at this moment, this is why the source code is embedded inside this
  repository. You can find it in the `web/components/impress.js` directory

Start your application on your webserver, and you should see the default `home` slider, a black
cube on a gray background!

### App config

`APPDEBUG` config is used to render the backtrace for each error. Set it to `false` on production.
`USE_LOCALE` is used to whether prepend the `locale` on the URI, to allow you to translate your application.
`LOCALES` is a list of locales you want to use in your application. This must be a list of the locales separated 
with a pipe `|`.
`DEFAULT_LOCALE` is used as fallback locale for the translator, and also used as the locale used for the `home` URI.
`CONVERT_PATH` is mandatory for ImageMagick to generate thumbnails for your image-based sliders (like a 
photography portfolio). This must be the absolute path to the `convert` ImageMagick binary, or `convert.exe` on Windows.

# Usage

Every slider is contained in the `slides` directory, and the directory name is used as the slider's
identifier. This identifier will be the name displayed in the base url `/{slideName}` of your
application.

The slider configuration is stored inside the `parameters.yml` file.

An optional `img` directory can be used to store image-based slides (see below for more info
about image-based slides), and a `views` directory to store the Twig views of your slides.

Just look at the `slides/home/parameters.yml` to know how the default slider is made.

## The parameters.yml file

This file is the most important for your slider.

It defines **all your slides**, and cool additional parameters that allow you to customize your slider.

* **config**: The configuration of the slider itself
  * **name** (string)<br>
   The slider name. By default, it is named the same way than the directory. It is recommended that
   you set the `name` ONLY if you want non-alphanumeric characters in it.
  * **transition-duration** (integer)<br />
   This is simply the number of *milliseconds* of time the slide-transition will
   durate.
  * **data** (array)<br>
   This array will be the default `data` parameter for **every** slide in which the corresponding
   `data` attribute is not specified. Any same `data` attribute written under the `slides` parameter will **override**
   the `config` `data` attribute.
  * **increments** (array)<br>
   This special array allows you to dynamically increment some `data` attribute on each slide,
   without having to write the `data` parameter on every slide by incrementing the values yourself.
   This is very useful for simple sliders. The [example above](http://www.helene-rock.com/historique) is a simple example
   of a slider where the `x` data attribute is used with an `increments` config parameter.
     The key will be the same available keys as the slider `data` attribute (see below).
     For each increment data, you can specify these attributes:
     * **base** (integer)<br>
     The base `data` value that will be used.
     * **i** (integer)<br>
     The number added to the previous value for each iteration.
   Example:
     ```yml
     config:
         increments:
             x: { base: 0, i: 100 }
     ```
   Every slide will start with a `0` value for its `data-x` parameter (see below), and will be incremented of 100 units
   for each iteration. Meaning the first slide will have a value of 0, the second will have 100, the next 200, and so on.

* **slides**: An array of slides
  * **id** (string)<br>
   This is the HTML `id` attribute, and also the name that will be displayed on your URL
   bar to identify the slide. If it's not specified, it will be equal to the slide index in the 
  `slides` array, and if the slide index is a string, it will then be equal to `slides.{slideName}.{id}`
  * **text** (string)<br>
   This is the text rendered inside the slide.
   **Note:** this text is translated (see below for translation).
  * **wrapWithTag** (string)<br>
   You can specify an HTML tag name here to wrap the `text` parameter in a specific html
   tag. This is very useful if you want to wrap the whole text in an `<article>` or a `<h1>` without
   writing it inside the `text` parameter.
  * **credits** (string)<br>
   If you fill this parameter, a section will appear at the bottom of the slide with the text
   you specified in this parameter, with an arrow icon to slide up/down the section. Useful when you want
   to credit someone that worked on something. See it in action on [this slider](http://www.helene-rock.com/historique)
   for example.
  * **image** (boolean)<br>
   If you set this parameter to `true`, the application will search for a **JPG image** in the 
   `slides/{sliderName}/img/{slideId}.jpg` path, and use it as background for the slider.
   **Note:** Please remember that, as it is used as a background, you'll have to re-design the slider yourself by using
   CSS to allow viewing photos or specific images. Do it inside the `web/css/slides.css` file.
  * **view** (boolean)<br>
   If you set this parameter to `true`, the application will search for a **Twig view** in the 
  `slides/{sliderName}/views/{slideId}.html.twig` path, and use it as `text` parameter. The `text` parameter is 
  overriden, so using both `view: true` and the `text` parameter will never render the `text` parameter.
  * **data** (array)<br>
   The Impress `data-` attributes to set on the slide. View below for more information.
  * **reset** (array)<br>
   This array is a way to **stop the increment** system before showing this slide.
   It will reset the current iterator value to its `base` attribute and restart the iteration.
   The keys are the same than the `data` attributes, but you can only specify a **boolean** value (default false)


## The "data" attributes

Impress.js uses HTML `data-` attributes on each step to calculate its position and behavior in the canvas.

Inside the `parameters.yml` file, you will **remove the "data-" part** to use it.

So the available datas are the following:

* x
* y
* z
* rotate
* rotate-x
* rotate-y
* rotate-z
* scale

They **all need an integer** as value, and their default one is always 0 (unless you override it in the `config`, or if
you are using `increments`).


### Datas API (forked from the Impress.js repo)

According to the Impress.js API, the datas are the following:

> ## Cartesian Position
> Where in 3D space to position the step frame in Cartesian space.
> 
> ### data-x, data-y, data-z
> Define the origin location in 3D Cartesian space. Specified in pixels (sort-of).
> 
> ### data-rotate
> Rotation of the step frame about its origin in the X-Y plane. This is akin to rotating a piece of paper in front of
> your face while maintaining it's ortho-normality to your image plane (did that explanation help? I didn't think so...).
> It rotates the way a photo viewer rotates, like when changing from portrait to landscape view.
>
> ## Polar Position
> Rotation of the step frame about its origin along the theta (azimuth) and phi (elevation) axes. This effect is similar
> to tilting the frame away from you (elevation) or imaging it standing on a turntable -- and then rotating the turntable
> (azimuth).
>
> ### data-rotate-x
> Rotation along the theta (azimuth) axis
> 
> ### data-rotate-y
> Rotation along the phi (elevation) axis
> 
> ## Size
> 
> ### data-scale
> The multiple of the "normal" size of the step frame. Has no absolute visual impact, but works to create relative size
> differences between frames. Effectively, it is controlling how "close" the camera is placed relative to the step frame.

More information can be found on the [Impress.js Wiki](https://github.com/bartaz/impress.js/wiki) or the
[Impress.js documentation](https://github.com/bartaz/impress.js).
