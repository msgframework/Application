<?php

namespace Msgframework\Lib\Document\Feed;



/**
 * Data object representing a feed image
 *
 * @since  1.1.0
 */
class FeedImage
{
	/**
	 * Title image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $title = '';

	/**
	 * URL image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $url = '';

	/**
	 * Link image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $link = '';

	/**
	 * Width image attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $width;

	/**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $height;

	/**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $description;
}
