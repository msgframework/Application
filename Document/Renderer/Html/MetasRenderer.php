<?php

namespace Msgframework\Lib\Document\Renderer\Html;

use Msgframework\Lib\Document\HtmlDocumentRenderer;
use Joomla\Utilities\ArrayHelper;

/**
 * JDocument metas renderer
 *
 * @since  1.0.0
 */
class MetasRenderer extends HtmlDocumentRenderer
{
    /**
     * Renders the document metas and returns the results as a string
     *
     * @param string|null $name
     * @param array|null $params Associative array of values
     * @param string|null $content The script
     *
     * @return  string  The output of the script
     *
     * @since  1.0.0
     */
	public function render(?string $name = null, ?array $params = null, ?string $content = null): string
	{
		// Trigger the onBeforeCompileHead event
		//$this->_doc->getApplication()->triggerEvent('onBeforeCompileHead');

		// Get line endings
		$lnEnd        = $this->_doc->getLineEnd();
		$tab          = $this->_doc->_getTab();
		$buffer       = '';

		// Generate charset when using HTML5 (should happen first)
		if ($this->_doc->isHtml5())
		{
			$buffer .= $tab . '<meta charset="' . $this->_doc->getCharset() . '">' . $lnEnd;
		}

		// Generate base tag (need to happen early)
		$base = $this->_doc->getBase();

		if (!empty($base))
		{
			$buffer .= $tab . '<base href="' . $base . '">' . $lnEnd;
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($this->_doc->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $contents)
			{
				if ($type === 'http-equiv' && !($this->_doc->isHtml5() && $name === 'content-type'))
				{
					$buffer .= $tab . '<meta http-equiv="' . $name . '" content="'
						. htmlspecialchars($contents, ENT_COMPAT, 'UTF-8') . '">' . $lnEnd;
				}
				elseif ($type !== 'http-equiv' && !empty($contents))
				{
					$buffer .= $tab . '<meta ' . $type . '="' . $name . '" content="'
						. htmlspecialchars($contents, ENT_COMPAT, 'UTF-8') . '">' . $lnEnd;
				}
			}
		}

		// Don't add empty descriptions
		$documentDescription = $this->_doc->getDescription();

		if ($documentDescription)
		{
			$buffer .= $tab . '<meta name="description" content="' . htmlspecialchars($documentDescription, ENT_COMPAT, 'UTF-8') . '">' . $lnEnd;
		}

		$buffer .= $tab . '<title>' . htmlspecialchars($this->_doc->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>' . $lnEnd;

		// Generate link declarations
		foreach ($this->_doc->_links as $link => $linkAtrr)
		{
			$buffer .= $tab . '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';

			if (\is_array($linkAtrr['attribs']))
			{
				if ($temp = ArrayHelper::toString($linkAtrr['attribs']))
				{
					$buffer .= ' ' . $temp;
				}
			}

			$buffer .= '>' . $lnEnd;
		}

		return ltrim($buffer, $tab);
	}
}
