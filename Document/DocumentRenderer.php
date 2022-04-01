<?php


namespace Msgframework\Lib\Document;

/**
 * Abstract class for a renderer
 *
 * @since  1.0.0
 */
abstract class DocumentRenderer implements RendererInterface
{
	/**
	 * Reference to the Document object that instantiated the renderer
	 *
	 * @var    Document
	 * @since  1.0.0
	 */
	protected Document $_doc;

	/**
	 * Renderer mime type
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected string $_mime = 'text/html';

	/**
	 * Class constructor
	 *
	 * @param   Document  $doc  A reference to the Document object that instantiated the renderer
	 *
	 * @since  1.0.0
	 */
	public function __construct(Document $doc)
	{
		$this->_doc = $doc;
	}

	/**
	 * Return the content type of the renderer
	 *
	 * @return  string  The contentType
	 *
	 * @since  1.0.0
	 */
	public function getContentType(): string
    {
		return $this->_mime;
	}

	/**
	 * Convert links in a text from relative to absolute
	 *
	 * @param   string  $text  The text processed
	 *
	 * @return  string   Text with converted links
	 *
	 * @since  1.0.0
	 */
	protected function _relToAbs($text)
	{
		$base = Uri::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto|data|\/\/)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}
