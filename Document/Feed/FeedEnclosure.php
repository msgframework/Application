<?php

namespace Msgframework\Lib\Document\Feed;



/**
 * Data object representing a feed enclosure
 *
 * @since  1.1.0
 */
class FeedEnclosure
{
	/**
	 * URL enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $url = '';

	/**
	 * Length enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $length = '';

	/**
	 * Type enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $type = '';
}
