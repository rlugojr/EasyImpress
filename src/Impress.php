<?php

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Impress
{

    /** @var Slide[] */
    protected $slides;

    /** @var array */
    protected $values = array();

    /** @var array */
    protected $config = array();

    /** @var string */
    protected $name;

    /**
     * @param string $name
     *
     * @return Impress
     */
    public static function create($name)
    {
        $self = new static($name);
        return $self;
    }

    public function __call($method, $args)
    {
        if (strpos($method, 'get') === 0) {
            $method = lcfirst(preg_replace('~^get~', '', $method));
            if (isset($this->values[$method])) {
                return $this->values[$method];
            }
        }
        return null;
    }

    function __construct($name)
    {
        if (!file_exists(SLIDESDIR.$name.'/parameters.yml')) {
            throw new NotFoundHttpException('slider_not_found');
        }
        $slides     = Yaml::parse(SLIDESDIR.$name.'/parameters.yml');
        $this->name = $name;

        $this->computeConfigDatas($slides);
    }

    protected function computeConfigDatas(array $slides)
    {
        $slides['config']['data'] = array_merge(array(
            'x'                   => 0,
            'y'                   => 0,
            'z'                   => 0,
            'rotate'              => 0,
            'rotate-x'            => 0,
            'rotate-y'            => 0,
            'rotate-z'            => 0,
            'transition-duration' => 1000,
            'name'                => $this->name,
        ), isset($slides['config']['data']) ? $slides['config']['data'] : array());

        if (!isset($slides['config']['attr']['class'])) {
            $slides['config']['attr']['class'] = 'impress_slides_container';
        }
        if (strpos($slides['config']['attr']['class'], 'impress_slides_container') === false) {
            $slides['config']['attr']['class'] = 'impress_slides_container '.$slides['config']['attr']['class'];
        }

        $slides['config']['inactive_opacity'] = isset($slides['config']['inactive_opacity']) ? (int) $slides['config']['inactive_opacity'] : 1;

        $slides['config']['attr']['class'] .= ' impress_slide_'.$this->name;
        $slides['config']['attr']['class'] = trim($slides['config']['attr']['class']);

        foreach ($slides['slides'] as $k => $slide) {
            $slide = array_merge(array(
                'id'              => $k,
                'createParagraph' => true,
                'attr'            => '',
            ), $slide ? : array());

            $slide['attr']['class'] = trim('step '.(isset($slide['attr']['class']) ? $slide['attr']['class'] : ''));

            if (!isset($slide['text']) && $k !== 'overview') {
                $slide['text'] = 'slides.'.$slides['config']['data']['name'].'.'.$k;
            }

            $slides['slides'][$k] = new Slide($slide, $this);
        }

        $this->config = $slides['config'];
        $this->values = $slides;
        $this->slides = $slides['slides'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function toArray()
    {
        return $this->values;
    }

    public function getSlides()
    {
        return $this->slides;
    }

    public function getSlide($id)
    {
        if (!isset($this->slides[$id])) {
            return new \Exception('Slide "'.$id.'" does not exist in current slider.');
        }
        return $this->slides[$id];
    }

}