<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Document\Feed\FeedImage;
use Msgframework\Lib\Document\Feed\FeedItem;
use Msgframework\Lib\Language\Text;
use Msgframework\Lib\Application\WebApplication;

/**
 * FeedDocument class, provides an easy interface to parse and display any feed document
 *
 * @since  1.0.0
 */
class FeedDocument extends Document
{
    /**
     * FeedDocument full URL
     *
     * @var    string
     * @since  1.0.0
     */
    public string $link = '';

	/**
	 * Syndication URL feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $syndicationURL = '';

	/**
	 * Image feed element
	 *
	 * optional
	 *
	 * @var    FeedImage|null
	 * @since  1.0.0
	 */
	public ?FeedImage $image = null;

	/**
	 * Copyright feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $copyright = '';

	/**
	 * Published date feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $pubDate = '';

	/**
	 * Lastbuild date feed element
	 *
	 * @var    Date
	 * @since  1.0.0
	 */
	public Date $lastBuildDate;

	/**
	 * Editor feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $editor = '';

	/**
	 * Docs feed element
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $docs = '';

	/**
	 * Editor email feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $editorEmail = '';

	/**
	 * Webmaster email feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $webmaster = '';

	/**
	 * Category feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $category = '';

	/**
	 * TTL feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $ttl = '';

	/**
	 * Rating feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $rating = '';

	/**
	 * Skiphours feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $skipHours = '';

	/**
	 * Skipdays feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $skipDays = '';

	/**
	 * The feed items collection
	 *
	 * @var    FeedItem[]
	 * @since  1.0.0
	 */
	public array $items = array();

    /**
     * Class constructor
     *
     * @param FactoryInterface $factory Factory
     * @param WebApplication $application WebApplication
     * @param array $options Associative array of options
     *
     * @throws \Exception
     * @since  1.0.0
     */
    public function __construct(FactoryInterface $factory, WebApplication $application, array $options = array())
    {
        parent::__construct($factory, $application, $options);

		// Set document type
        $this->setType('feed');

		// Gets and sets timezone offset from site configuration
        $config = $this->getApplication()->getConfig();
        $tz = new \DateTimeZone($config->get('offset', 'UTC'));
		$this->lastBuildDate = new Date('now', $tz);
	}

    /**
     * Render the document
     *
     * @param boolean $cache If true, cache the output
     * @param array $params Associative array of attributes
     *
     * @return Response The rendered data
     *
     * @throws \Exception
     * @since  1.0.0
     * @todo    Make this cacheable
     * @todo    Refactoring render stylesheets attachments
     */
	public function render(bool $cache = false, array $params = array()): Response
	{
		// Get the feed type
		$type = Cms::getApplication()->input->get('type', 'rss');

		// Instantiate feed renderer and set the mime encoding
		$renderer = $this->loadRenderer(($type) ? $type : 'rss');

		if (!($renderer instanceof DocumentRenderer))
		{
			throw new \Exception(Text::_('JGLOBAL_RESOURCE_NOT_FOUND'), 404);
		}

		$this->setMimeEncoding($renderer->getContentType());

		// Output
		// Generate prolog
		$data = "<?xml version=\"1.0\" encoding=\"" . $this->_charset . "\"?>\n";
		$data .= "<!-- generator=\"" . $this->getGenerator() . "\" -->\n";

		// Generate stylesheet links
		foreach ($this->_styleSheets as $src => $attr)
		{
			$data .= "<?xml-stylesheet href=\"$src\" type=\"" . $attr['type'] . "\"?>\n";
		}

		// Render the feed
		$data .= $renderer->render();

		parent::render($cache, $params);

		return $data;
	}

	/**
	 * Adds a FeedItem to the feed.
	 *
	 * @param   FeedItem  $item  The feeditem to add to the feed.
	 *
	 * @return  $this  instance of $this to allow chaining
	 *
	 * @since  1.0.0
	 */
	public function addItem(FeedItem $item): self
	{
		$item->source = $this->link;
		$this->items[] = $item;

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
}
