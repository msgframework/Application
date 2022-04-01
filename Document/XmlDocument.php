<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplication;
use Symfony\Component\HttpFoundation\Response;

/**
 * XmlDocument class, provides an easy interface to parse and display XML output
 *
 * @since  1.0.0
 */
class XmlDocument extends Document
{
    /**
     * XmlDocument full URL
     *
     * @var    string
     * @since  1.0.0
     */
    public string $link = '';
	/**
	 * Document name
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected string $name = 'XmlDocument';

	/**
	 * Flag indicating the document should be downloaded (Content-Disposition = attachment) versus displayed inline
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected bool $isDownload = false;

	/**
	 * Class constructor
	 *
     * @param FactoryInterface $factory  Factory
     * @param WebApplication $application  WebApplication
     * @param array $options  Associative array of options
     *
     * @since  1.0.0
     */
    public function __construct(FactoryInterface $factory, WebApplication $application, array $options = array())
    {
        parent::__construct($factory, $application, $options);

		// Set mime type
		$this->setMimeEncoding('application/xml');

		// Set document type
		$this->setType('xml');
	}

    /**
     * Render the document.
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
		$response = parent::render($cache, $params);

		$disposition = $this->isDownload() ? 'attachment' : 'inline';

        $response->headers->set('Content-disposition', $disposition . '; filename="' . $this->getName() . '.xml"', true);

		return $response;
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getName(): string
    {
		return $this->name;
	}

	/**
	 * Sets the document name
	 *
	 * @param string $name  Document name
	 *
	 * @return $this instance of $this to allow chaining
	 *
	 * @since  1.0.0
	 */
	public function setName(string $name = 'XmlDocument'): self
    {
		$this->name = $name;

		return $this;
	}

    /**
     * Check if this document is intended for download
     *
     * @return bool
     *
     * @since  1.0.0
     */
	public function isDownload(): bool
	{
		return $this->isDownload;
	}

	/**
	 * Sets the document's download state
	 *
	 * @param boolean $download  If true, this document will be downloaded; if false, this document will be displayed inline
	 *
	 * @return $this instance of $this to allow chaining
	 *
	 * @since  1.0.0
	 */
	public function setDownload(bool $download = false): self
	{
		$this->isDownload = $download;

		return $this;
	}

    /**
     * Sets the document link
     *
     * @param   string  $url  A url
     *
     * @return  self
     *
     * @since  1.0.0
     */
    public function setLink(string $url): self
    {
        $this->link = $url;

        return $this;
    }

    /**
     * Returns the document base url
     *
     * @return string
     *
     * @since  1.0.0
     */
    public function getLink(): string
    {
        return $this->link;
    }
}
