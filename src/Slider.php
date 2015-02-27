<?php

class Slider implements \Countable
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
     * @param array  $slides
     *
     * @return $this
     */
    public static function create($name, array $slides)
    {
        $self = new static($name, $slides);
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

    function __construct($name, array $slides)
    {
        $this->name = $name;

        if (!$slides) {
            throw new \InvalidArgumentException('Slider is empty.');
        }

        $this->computeConfigDatas($slides);
    }

    protected function computeConfigDatas(array $slides = array())
    {
        if (!isset($slides['config'])) { $slides['config'] = array(); }
        if (!isset($slides['config']['data'])) { $slides['config']['data'] = array(); }
        if (!isset($slides['slides'])) { $slides['slides'] = array(); }

        if (!count($slides['slides'])) {
            throw new \InvalidArgumentException('You must define at least one slide.');
        }

        $slides['config']['data'] = array_merge(array(
            'transition-duration' => 1000,
            'name'                => $this->name,
        ), $slides['config']['data']);

        $defaultImpressData = array(
            'x'        => null,
            'y'        => null,
            'z'        => null,
            'rotate'   => null,
            'rotate-x' => null,
            'rotate-y' => null,
            'rotate-z' => null,
        );

        $slides['config']['data'] = array_merge($defaultImpressData, isset($slides['config']['data']) ? $slides['config']['data'] : array());

        if (!isset($slides['config']['increments'])) {
            $slides['config']['increments'] = array();
        }

        foreach ($defaultImpressData as $key => $v) {
            if (!isset($slides['config']['increments'][$key])) {
                $slides['config']['increments'][$key] = array();
            }
            $slides['config']['increments'][$key] = array_merge(array(
                'current' => 0,
                'base' => null,
                'i' => null,
            ), $slides['config']['increments'][$key]);
        }

        if (!isset($slides['config']['attr']['class'])) {
            $slides['config']['attr']['class'] = 'impress_slides_container';
        }
        if (strpos($slides['config']['attr']['class'], 'impress_slides_container') === false) {
            $slides['config']['attr']['class'] = 'impress_slides_container '.$slides['config']['attr']['class'];
        }

        $slides['config']['inactive_opacity'] = isset($slides['config']['inactive_opacity']) ? (float) $slides['config']['inactive_opacity'] : 1;

        $slides['config']['attr']['class'] .= ' impress_slide_'.$this->name;
        $slides['config']['attr']['class'] = trim($slides['config']['attr']['class']);

        $calculateIncrements = $slides['config']['increments'];

        foreach ($slides['slides'] as $k => $slide) {
            $slide = array_merge(array(
                'id'              => $k,
                'createParagraph' => true,
                'attr'            => array(),
                'reset'           => array(),
                'view'            => false,
                'image'           => false,
                'wrapWithTag'     => '',
                'text'            => '',
                'credits'         => '',
            ), $slide ?: array());

            $slide['reset'] = array_merge(array_fill_keys(array_keys($defaultImpressData), false), $slide['reset']);

            $slide['attr']['class'] = trim('step '.(isset($slide['attr']['class']) ? $slide['attr']['class'] : ''));

            if (!$slide['text'] && $k !== 'overview') {
                $slide['text'] = 'slides.'.$slides['config']['data']['name'].'.'.$k;
            }

            $d = array_merge($defaultImpressData, isset($slide['data']) && count((array)$slide['data']) ? $slide['data'] : $defaultImpressData);

            foreach ($calculateIncrements as $incType => $incData) {
                if (null !== $incData['base'] && isset($slide['reset'][$incType]) && $slide['reset'][$incType] === true ) {
                    $calculateIncrements[$incType]['current'] = $incData['base'];
                }
                if (null === $d[$incType]) {
                    $d[$incType] = $calculateIncrements[$incType]['current'];
                }
                if (null !== $incData['base'] && null !== $incData['i']) {
                    $calculateIncrements[$incType]['current'] += $incData['i'];
                }
            }

            $d = array_merge(array_fill_keys(array_keys($defaultImpressData), 0), array_filter($d));

            $slide['data'] = $d;

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

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->slides);
    }
}