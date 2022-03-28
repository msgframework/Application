<?php

namespace Msgframework\Lib\Document\Renderer\Html;

use Msgframework\Lib\Document\DocumentRenderer;

/**
 * HTML document renderer for the component output
 *
 * @since  1.0.0
 */
class ComponentRenderer extends DocumentRenderer
{
    /**
     * Renders a component script and returns the results as a string
     *
     * @param string|null $name The name of the component to render
     * @param array|null $params Associative array of values
     * @param string|null $content Content script
     *
     * @return  string  The output of the script
     *
     * @since  1.0.0
     */
	public function render(string $name, ?array $params = array(), ?string $content = null): string
	{
        $this->_doc->setBuffer($content, array('type' => 'component', 'name' => $name));

        return $content;
	}
}
