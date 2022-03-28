<?php

namespace Msgframework\Lib\Document\Renderer\Html;



use Msgframework\Lib\Document\DocumentRenderer;

/**
 * HTML document renderer for the document `<head>` element
 *
 * @since  1.1.0
 */
class HeadRenderer extends DocumentRenderer
{
	/**
	 * Renders the document head and returns the results as a string
	 *
	 * @param   string  $head     (unused)
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  The script
	 *
	 * @return  string  The output of the script
	 *
	 * @since  1.0.0
	 */
	public function render($head, $params = array(), $content = null)
	{
		$buffer  = '';
		$buffer .= $this->_doc->loadRenderer('metas')->render($head, $params, $content);
		$buffer .= $this->_doc->loadRenderer('styles')->render($head, $params, $content);
		$buffer .= $this->_doc->loadRenderer('scripts')->render($head, $params, $content);

		return $buffer;
	}
}
