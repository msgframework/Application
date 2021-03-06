<?php

namespace Msgframework\Lib\Document\Renderer\Html;

use Msgframework\Lib\AssetManager\WebAssetItemInterface;
use Msgframework\Lib\Document\HtmlDocumentRenderer;

/**
 * JDocument head renderer
 *
 * @since  1.0.0
 */
class ScriptsRenderer extends HtmlDocumentRenderer
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
     * Renders the document script tags and returns the results as a string
     *
     * @param string|null $name
     * @param array|null $params Associative array of values
     * @param string|null $content The script
     *
     * @return  string  The output of the script
     *
     * @since  1.0.0
     */
	public function render(?string $name = null, ?array $params = array(), ? string $content = null): string
	{
		// Get line endings
		$lnEnd        = $this->_doc->getLineEnd();
		$tab          = $this->_doc->_getTab();
		$buffer       = '';
		$wam          = $this->_doc->getWebAssetManager();
		$assets       = $wam->getAssets('script', true);
        $this->debug  = $params['debug'] ?? false;

		// Get a list of inline assets and their relation with regular assets
		$inlineAssets   = $wam->filterOutInlineAssets($assets);
		$inlineRelation = $wam->getInlineRelation($inlineAssets);

		// Generate script file links
		foreach ($assets as $key => $asset)
		{
			// Check for inline content "before"
			if ($asset && !empty($inlineRelation[$asset->getName()]['before']))
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
			if ($asset && !empty($inlineRelation[$asset->getName()]['after']))
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

		// Output the custom tags - array_unique makes sure that we don't output the same tags twice
		foreach (array_unique($this->_doc->_custom) as $custom)
		{
			$buffer .= $tab . $custom . $lnEnd;
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
		if (!$src || !empty($this->renderedSrc[$src]) || ($asset->getOption('webcomponent')))
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

        $src = $this->_doc->getBase() . $src;

		// Render the element with attributes
		$buffer .= '<script src="' . htmlspecialchars($src) . '"';
		$buffer .= $this->renderAttributes($attribs);
		$buffer .= '></script>';

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
		$buffer = '';
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

		$buffer .= $tab . '<script';
		$buffer .= $this->renderAttributes($attribs);
		$buffer .= '>';

		// This is for full XHTML support.
		if ($this->_doc->_mime !== 'text/html')
		{
			$buffer .= $tab . $tab . '//<![CDATA[' . $lnEnd;
		}

		$buffer .= $content;

		// See above note
		if ($this->_doc->_mime !== 'text/html')
		{
			$buffer .= $tab . $tab . '//]]>' . $lnEnd;
		}

		$buffer .= '</script>' . $lnEnd;

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

		$defaultJsMimes         = array('text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript');
		$html5NoValueAttributes = array('defer', 'async', 'nomodule');

		foreach ($attributes as $attrib => $value)
		{
			// Don't add the 'options' attribute. This attribute is for internal use (version, conditional, etc).
			if ($attrib === 'options' || $attrib === 'src')
			{
				continue;
			}

			// Don't add type attribute if document is HTML5 and it's a default mime type. 'mime' is for B/C.
			if (\in_array($attrib, array('type', 'mime')) && $this->_doc->isHtml5() && \in_array($value, $defaultJsMimes))
			{
				continue;
			}

			// B/C: If defer and async is false or empty don't render the attribute. Also skip if value is bool:false.
			if (\in_array($attrib, array('defer', 'async')) && !$value || $value === false)
			{
				continue;
			}

			// NoValue attribute, if it have bool:true
			$isNoValueAttrib = $value === true || \in_array($attrib, $html5NoValueAttributes);

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
