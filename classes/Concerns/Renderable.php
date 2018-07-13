<?php

namespace CRMConnector\Concerns;

use CRMConnector\Utils\CRMCFunctions;



trait Renderable
{
    public function render($view, $params = [])
    {
        $path = $this->getRenderablePartialPath($view);

        try
        {
            ob_start();

            if ($path === false) {
                throw new \Exception(sprintf("Failed to locate the requested partial: <strong>%s</strong>",
                    $view
                ));
            }

            if (!empty($params))
                extract($params);

            include($path);

            $output = ob_get_clean();

            return $output;

        }
        catch(\Exception $e)
        {
            ob_end_clean();

            if (CRMCFunctions::is_local()) {
                wp_die(sprintf('%s<pre style="max-width: 800px; overflow: scroll">%s</pre>', $e->getMessage(), $e->getTraceAsString()), 'CRMC Render Error');
            } else {
                // log this error or do something else with the error in production
            }
        }
    }

    private function getRenderablePartialPath($name)
    {
        $path = sprintf('%s/views/%s.php', CRMCFunctions::plugin_dir(), $name);

        return file_exists($path) ? $path : false;
    }

}