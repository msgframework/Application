<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplication;
use Symfony\Component\HttpFoundation\Response;
use Msgframework\Lib\Date\Date;

/**
 * Document class, provides an easy interface to parse and display a document
 *
 * @since  1.0.0
 */
class Document
{
    /**
     * Document title
     *
     * @var    string
     * @since  1.0.0
     */
    public string $title = '';

	/**
	 * Contains the document language setting
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $language = 'ru';

	/**
	 * Contains the document direction setting
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $direction = 'ltr';

	/**
	 * Document modified date
	 *
	 * @var    Date
	 * @since  1.0.0
	 */
	public Date $_mdate;

	/**
	 * Contains the line end string
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $_lineEnd = "\12";

	/**
	 * Contains the character encoding string
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $_charset = 'utf-8';

	/**
	 * Document mime type
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $_mime = '';

	/**
	 * The document type
	 *
	 * @var    string|null
     * @since  1.0.0
	 */
	public ?string $_type = null;

	/**
	 * Array of buffered output
	 *
	 * @var    mixed (depends on the renderer)
	 * @since  1.0.0
	 */
	public static $_buffer = null;

	/**
	 * Factory for creating Document API objects
	 *
	 * @var    FactoryInterface
	 * @since  1.0.0
	 */
	protected FactoryInterface $factory;

	/**
	 * Link to WebApplication
	 *
	 * @var    WebApplication
	 * @since  1.0.0
	 */
	protected WebApplication $application;

	/**
	 * Class constructor.
	 *
	 * @param FactoryInterface $factory  Factory
	 * @param WebApplication $application  WebApplication
	 * @param array $options  Associative array of options
	 *
	 * @since  1.0.0
	 */
	public function __construct(FactoryInterface $factory, WebApplication $application, array $options = array())
	{
        $this->factory = $factory;
        $this->application = $application;

		if (\array_key_exists('lineend', $options))
		{
			$this->setLineEnd($options['lineend']);
		}

		if (\array_key_exists('charset', $options))
		{
			$this->setCharset($options['charset']);
		}

		if (\array_key_exists('language', $options))
		{
			$this->setLanguage($options['language']);
		}

		if (\array_key_exists('direction', $options))
		{
			$this->setDirection($options['direction']);
		}
	}

	/**
	 * Set the document type
	 *
	 * @param   string  $type  Type document is to set to
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 */
	public function setType(string $type): self
	{
		$this->_type = $type;

		return $this;
	}

	/**
	 * Returns the document type
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getType(): string
	{
		return $this->_type;
	}

	/**
	 * Get the contents of the document buffer
	 *
	 * @return  mixed
	 *
	 * @since  1.0.0
	 */
	public function getBuffer()
	{
		return self::$_buffer;
	}

	/**
	 * Set the contents of the document buffer
	 *
	 * @param   string  $content  The content to be set in the buffer.
	 * @param   array   $options  Array of optional elements.
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 */
	public function setBuffer(string $content, array $options = array()): self
	{
		self::$_buffer = $content;

		return $this;
	}

    public function getApplication(): WebApplication
    {
        return $this->application;
    }

	/**
	 * Sets the document charset
	 *
	 * @param   string  $type  Charset encoding string
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 */
	public function setCharset(string $type = 'utf-8'): self
	{
		$this->_charset = $type;

		return $this;
	}

	/**
	 * Returns the document charset encoding.
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getCharset(): string
	{
		return $this->_charset;
	}

	/**
	 * Sets the global document language declaration. Default is English (en-gb).
	 *
	 * @param   string  $lang  The language to be set
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 */
	public function setLanguage(string $lang = 'en-gb'): self
	{
		$this->language = strtolower($lang);

		return $this;
	}

	/**
	 * Returns the document language.
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getLanguage(): string
	{
		return $this->language;
	}

	/**
	 * Sets the global document direction declaration. Default is left-to-right (ltr).
	 *
	 * @param   string  $dir  The language direction to be set
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 */
	public function setDirection(string $dir = 'ltr'): self
	{
		$this->direction = strtolower($dir);

		return $this;
	}

	/**
	 * Returns the document direction declaration.
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getDirection(): string
	{
		return $this->direction;
	}

    /**
     * Set the title of the document.
     *
     * @param string $title
     *
     * @return  $this
     *
     * @since   1.0.0
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the title of the document.
     *
     * @return  string
     *
     * @since   1.0.0
     */
    public function getTitle(): string
    {
        return $this->title;
    }

	/**
	 * Sets the document modified date
	 *
	 * @param   Date  $date  The date to be set
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 * @throws  \InvalidArgumentException
	 */
	public function setModifiedDate(Date $date): self
	{
		$this->_mdate = $date;

		return $this;
	}

	/**
	 * Returns the document modified date
	 *
	 * @return  Date
	 *
	 * @since  1.0.0
	 */
	public function getModifiedDate(): Date
	{
		return $this->_mdate ?? new Date();
	}

	/**
	 * Sets the document MIME encoding that is sent to the browser.
	 *
	 * This usually will be text/html because most browsers cannot yet
	 * accept the proper mime settings for XHTML: application/xhtml+xml
	 * and to a lesser extent application/xml and text/xml. See the W3C note
	 * ({@link https://www.w3.org/TR/xhtml-media-types/
	 * https://www.w3.org/TR/xhtml-media-types/}) for more details.
	 *
	 * @param   string   $type  The document type to be sent
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 *
	 * @link    https://www.w3.org/TR/xhtml-media-types/
	 */
	public function setMimeEncoding(string $type = 'text/html'): self
	{
		$this->_mime = strtolower($type);

		return $this;
	}

	/**
	 * Return the document MIME encoding that is sent to the browser.
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getMimeEncoding(): string
	{
		return $this->_mime;
	}

	/**
	 * Sets the line end style to Windows, Mac, Unix or a custom string.
	 *
	 * @param   string  $style  "win", "mac", "unix" or custom string.
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 */
	public function setLineEnd(string $style): self
	{
		switch ($style)
		{
			case 'win':
				$this->_lineEnd = "\15\12";
				break;
			case 'unix':
				$this->_lineEnd = "\12";
				break;
			case 'mac':
				$this->_lineEnd = "\15";
				break;
			default:
				$this->_lineEnd = $style;
		}

		return $this;
	}

	/**
	 * Returns the lineEnd
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getLineEnd(): string
	{
		return $this->_lineEnd;
	}

	/**
	 * Load a renderer
	 *
	 * @param   string  $type  The renderer type
	 *
	 * @return  RendererInterface
	 *
	 * @since  1.0.0
	 * @throws  \RuntimeException
	 */
	public function loadRenderer(string $type): RendererInterface
	{
		return $this->factory->createRenderer($this, $type);
	}

    /**
     * Outputs the document
     *
     * @param boolean $cache If true, cache the output
     * @param array $params Associative array of attributes
     *
     * @return Response The rendered data
     *
     * @throws \Exception
     * @since  1.0.0
     */
	public function render(bool $cache = false, array $params = array()): Response
	{
        $response = new Response();

		if ($mdate = $this->getModifiedDate())
		{
			if (!($mdate instanceof Date))
			{
				$mdate = new Date($mdate);
			}

			$response->setLastModified($mdate);
		}

		$response->setCharset($this->getCharset());

		return $response;
	}
}
