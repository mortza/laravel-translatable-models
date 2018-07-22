<?php

namespace Mortza\Translatable;

/**
 * Trait Translatable
 *
 * with this package you can add translation to laravel models
 * to setup translation you should add this trait to your Model.php file
 * then define parameters listed below in class
 * with this package there is no limitation on number of translation for each attribute except database limitations
 *
 * @package Mortza\Translatable
 */
trait Translatable
{
    /**
     * @param $attr
     * @param $lang
     * @param string $fall_back
     */
    public function getTranslationFor($attr, $lang, $fall_back = 'en')
    {

    }

    /**
     * @param $attr
     * @param $lang
     */
    public function setTranslationFor($attr, $lang)
    {

    }
}