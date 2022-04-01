<?php

namespace Msgframework\Lib\Document\Renderer\Html;

use Msgframework\Lib\Document\HtmlDocumentRenderer;
use Msgframework\Lib\AssetManager\WebAssetItemInterface;

/**
 * JDocument styles renderer
 *
 * @since  1.0.0
 */
class StylesRenderer extends HtmlDocumentRenderer
{
    /**
     * Debug status
     *
     * @var bool
     *
     * @since  1.0.0
     */
    protected bool $debug = false;

	/**
	 * List of already rendered src
	 *
	 * @var array
	 *
	 * @since  1.0.0
	 */
	private array $renderedSrc = [];

    /**
     * Renders the document stylesheets and style tags and returns the results as a string
     *
     * @param string $name (unused)
     * @param array|null $params Associative array of values
     * @param string|null $content The script
     *
     * @return  string  The output of the script
     *
     * @since  1.0.0
     */
	public function render(string $name, ?array $params = array(), ?string $content = null): string
	{
		$tab          = $this->_doc->_getTab();
		$buffer       = '';
		$wam          = $this->_doc->getWebAssetManager();
		$assets       = $wam->getAssets('style', true);
        $this->debug  = $params['debug'] ?? false;

		// Get a list of inline assets and their relation with regular assets
		$inlineAssets   = $wam->filterOutInlineAssets($assets);
		$inlineRelation = $wam->getInlineRelation($inlineAssets);

		// Generate stylesheet links
		foreach ($assets as $key => $asset)
		{
			// Check for inline content "before"
			if (!empty($inlineRelation[$asset->getName()]['before']))
			{
				foreach ($inlineRelation[$asset->getName()]['before'] as $assetBefore)
				{
					$buffer .= $this->renderInlineElement($assetBefore);

					// Remove this item from inline queue
					unset($inlineAssets[$assetBefore->getName()]);
				}
			}

			$buffer .= $this->renderElement($asset);

			// Check for inline content "after"
			if (!empty($inlineRelation[$asset->getName()]['after']))
			{
				foreach ($inlineRelation[$asset->getName()]['after'] as $assetBefore)
				{
					$buffer .= $this->renderInlineElement($assetBefore);

					// Remove this item from inline queue
					unset($inlineAssets[$assetBefore->getName()]);
				}
			}
		}

		// Generate script declarations for assets
		foreach ($inlineAssets as $asset)
		{
			$buffer .= $this->renderInlineElement($asset);
		}

		return ltrim($buffer, $tab);
	}

	/**
	 * Renders the element
	 *
	 * @param   WebAssetItemInterface  $asset  The element
	 *
	 * @return  string  The resulting string
	 *
	 * @since  1.0.0
	 */
	private function renderElement(WebAssetItemInterface $asset) : string
	{
		$buffer = '';
		$src    = $asset->getUri();

		// Make sure we have a src, and it not already rendered
		if (!$src || !empty($this->renderedSrc[$src]))
		{
			return '';
		}

		$lnEnd        = $this->_doc->getLineEnd();
		$tab          = $this->_doc->_getTab();

        $attribs     = $asset->getAttributes();
        $version     = $asset->getVersion();
        $conditional = $asset->getOption('conditional');

        // Add an asset info for debugging
        if ($this->debug)
        {
            $attribs['data-asset-name'] = $asset->getName();

            if ($asset->getDependencies())
            {
                $attribs['data-asset-dependencies'] = implode(',', $asset->getDependencies());
            }
        }

		// To prevent double rendering
		$this->renderedSrc[$src] = true;

		// Check if script uses media version.
		if ($version && strpos($src, '?') === false)
		{
			$src .= '?' . $version;
		}

		$buffer .= $tab;

		// This is for IE conditional statements support.
		if (!\is_null($conditional))
		{
			$buffer .= '<!--[if ' . $conditional . ']>';
		}

		$relation = isset($attribs['rel']) ? $attribs['rel'] : 'stylesheet';

		if (isset($attribs['rel']))
		{
			unset($attribs['rel']);
		}

		// Render the element with attributes
		$buffer .= '<link href="' . htmlspecialchars($src) . '" rel="' . $relation . '"';
		$buffer .= $this->renderAttributes($attribs);
		$buffer .= ' />';

		if ($relation === 'lazy-stylesheet')
		{
			$buffer .= '<noscript><link href="' . htmlspecialchars($src) . '" rel="stylesheet" /></noscript>';
		}

		// This is for IE conditional statements support.
		if (!\is_null($conditional))
		{
			$buffer .= '<![endif]-->';
		}

		$buffer .= $lnEnd;

		return $buffer;
	}

	/**
	 * Renders the inline element
	 *
	 * @param   WebAssetItemInterface  $asset  The element
	 *
	 * @return  string  The resulting string
	 *
	 * @since  1.0.0
	 */
	private function renderInlineElement(WebAssetItemInterface $asset) : string
	{
		$lnEnd  = $this->_doc->getLineEnd();
		$tab    = $this->_doc->_getTab();

        $attribs = $asset->getAttributes();
        $content = $asset->getOption('content');

		// Do not produce empty elements
		if (!$content)
		{
			return '';
		}

		// Add "nonce" attribute if exist
		if ($this->_doc->cspNonce)
		{
			$attribs['nonce'] = $this->_doc->cspNonce;
		}

		$buffer = $tab . '<style';
		$buffer .= $this->renderAttributes($attribs);
		$buffer .= '>';

		// This is for full XHTML support.
		if ($this->_doc->_mime !== 'text/html')
		{
			$buffer .= $tab . $tab . '/*<![CDATA[*/' . $lnEnd;
		}

		$buffer .= $content;

		// See above note
		if ($this->_doc->_mime !== 'text/html')
		{
			$buffer .= $tab . $tab . '/*]]>*/' . $lnEnd;
		}

		$buffer .= '</style>' . $lnEnd;

		return $buffer;
	}

	/**
	 * Renders the element attributes
	 *
	 * @param   array  $attributes  The element attributes
	 *
	 * @return  string  The attributes string
	 *
	 * @since  1.0.0
	 */
	private function renderAttributes(array $attributes) : string
	{
		$buffer = '';

		$defaultCssMimes = array('text/css');

		foreach ($attributes as $attrib => $value)
		{
			// Don't add the 'options' attribute. This attribute is for internal use (version, conditional, etc).
			if ($attrib === 'options' || $attrib === 'href')
			{
				continue;
			}

			// Don't add type attribute if document is HTML5 and it's a default mime type. 'mime' is for B/C.
			if (\in_array($attrib, array('type', 'mime')) && $this->_doc->isHtml5() && \in_array($value, $defaultCssMimes))
			{
				continue;
			}

			// Skip the attribute if value is bool:false.
			if ($value === false)
			{
				continue;
			}

			// NoValue attribute, if it have bool:true
			$isNoValueAttrib = $value === true;

			// Don't add type attribute if document is HTML5 and it's a default mime type. 'mime' is for B/C.
			if ($attrib === 'mime')
			{
				$attrib = 'type';
			}
			// NoValue attribute in non HTML5 should contain a value, set it equal to attribute name.
			elseif ($isNoValueAttrib)
			{
				$value = $attrib;
			}

			// Add attribute to script tag output.
			$buffer .= ' ' . htmlspecialchars($attrib, ENT_COMPAT, 'UTF-8');

			if (!($this->_doc->isHtml5() && $isNoValueAttrib))
			{
				// Json encode value if it's an array.
				$value = !is_scalar($value) ? json_encode($value) : $value;

				$buffer .= '="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"';
			}
		}

		return $buffer;
	}
}
