<?php

namespace Msgframework\Lib\Document\Feed;

/**
 * Data object representing a feed item
 *
 * @since  1.1.0
 */
class FeedItem
{
	/**
	 * Title item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $title;

	/**
	 * Link item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $link;

	/**
	 * Description item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $description;

	/**
	 * Author item element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $author;

	/**
	 * Author email element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $authorEmail;

	/**
	 * Category element
	 *
	 * optional
	 *
	 * @var    array or string
	 * @since  1.0.0
	 */
	public array $category;

	/**
	 * Comments element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $comments;

	/**
	 * Enclosure element
	 *
	 * @var    FeedEnclosure
	 * @since  1.0.0
	 */
	public $enclosure = null;

	/**
	 * Guid element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $guid;

	/**
	 * Published date
	 *
	 * optional
	 *
	 * May be in one of the following formats:
	 *
	 * RFC 822:
	 * "Mon, 20 Jan 03 18:05:41 +0400"
	 * "20 Jan 03 18:05:41 +0000"
	 *
	 * ISO 8601:
	 * "2003-01-20T18:05:41+04:00"
	 *
	 * Unix:
	 * 1043082341
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $date;

	/**
	 * Source element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $source;

	/**
	 * Set the FeedEnclosure for this item
	 *
	 * @param   FeedEnclosure  $enclosure  The FeedEnclosure to add to the feed.
	 *
	 * @return  $this instance of $this to allow chaining
	 *
	 * @since  1.0.0
	 */
	public function setEnclosure(FeedEnclosure $enclosure): self
	{
		$this->enclosure = $enclosure;

		return $this;
	}
}
