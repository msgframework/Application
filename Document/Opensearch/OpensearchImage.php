<?php

namespace Msgframework\Lib\Document\Opensearch;



/**
 * Data object representing an OpenSearch image
 *
 * @since  1.1.0
 */
class OpensearchImage
{
	/**
	 * The images MIME type
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $type = '';

	/**
	 * URL of the image or the image as base64 encoded value
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $data = '';

	/**
	 * The image's width
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $width;

	/**
	 * The image's height
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $height;
}
