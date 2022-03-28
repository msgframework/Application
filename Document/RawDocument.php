<?php

namespace Msgframework\Lib\Document;

/**
 * RawDocument class, provides an easy interface to parse and display raw output
 *
 * @since  1.1.0
 */
class RawDocument extends Document
{
	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @since  1.0.0
	 */
    public function __construct(FactoryInterface $factory, array $options = array())
    {
        parent::__construct($factory, $options);

		// Set mime type
		$this->_mime = 'text/html';

		// Set document type
		$this->_type = 'raw';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  string  The rendered data
	 *
	 * @since  1.0.0
	 */
	public function render($cache = false, $params = array())
	{
		parent::render($cache, $params);

		return $this->getBuffer();
	}
}
