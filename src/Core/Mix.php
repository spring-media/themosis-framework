<?php

namespace Themosis\Core;

class Mix extends \Illuminate\Foundation\Mix
{
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @param  string  $manifestDirectory
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    public function __invoke($path, $manifestDirectory = '')
    {
        // Default to the users theme if available, otherwise the public path
        if (! $manifestDirectory && function_exists('wp_get_theme')) {
            $manifestDirectory = '/content/themes/'.wp_get_theme()->stylesheet.'/dist';
        }

        if ($manifestDirectory == '/') {
            $manifestDirectory = '';
        }

        return parent::__invoke($path, $manifestDirectory);
    }
}
