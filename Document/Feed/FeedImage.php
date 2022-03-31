<?php

namespace Msgframework\Lib\Document\Feed;



/**
 * Data object representing a feed image
 *
 * @since  1.0.0
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
	public string $title = '';

	/**
	 * URL image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $url = '';

	/**
	 * Link image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $link = '';

	/**
	 * Width image attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $width;

	/**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $height;

	/**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public string $description;
}
