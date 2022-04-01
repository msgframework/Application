<?php

namespace Msgframework\Lib\Document\Renderer\Html;

use Msgframework\Lib\Document\HtmlDocumentRenderer;

/**
 * HTML document renderer for the document `<head>` element
 *
 * @since  1.0.0
 */
class HeadRenderer extends HtmlDocumentRenderer
{
    /**
     * Renders the document head and returns the results as a string
     *
     * @param string $name
     * @param array|null $params Associative array of values
     * @param string|null $content The script
     *
     * @return  string  The output of the script
     *
     * @since  1.0.0
     */
	public function render(string $name, ?array $params = array(), ?string $content = null): string
	{
        $buffer = $this->_doc->loadRenderer('metas')->render($name, $params, $content);
		$buffer .= $this->_doc->loadRenderer('styles')->render($name, $params, $content);
		$buffer .= $this->_doc->loadRenderer('scripts')->render($name, $params, $content);

		return $buffer;
	}
}
