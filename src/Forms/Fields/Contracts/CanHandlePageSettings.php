<?php

namespace Themosis\Forms\Fields\Contracts;

interface CanHandlePageSettings
{
    /**
     * Save a page setting value.
     *
     * @param  mixed  $value
     */
    public function settingSave($value, string $name);

    /**
     * Return a page setting value.
     *
     * @return mixed
     */
    public function settingGet();
}
