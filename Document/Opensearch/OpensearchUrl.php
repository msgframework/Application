<?php

namespace Msgframework\Lib\Document\Opensearch;



/**
 * Data object representing an OpenSearch URL
 *
 * @since  1.1.0
 */
class OpensearchUrl
{
	/**
	 * Type item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $type = 'text/html';

	/**
	 * Rel item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $rel = 'results';

	/**
	 * Template item element. Has to contain the {searchTerms} parameter to work.
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $template;
}
