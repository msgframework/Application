<?php

namespace Msgframework\Lib\Document;

use Symfony\Component\HttpFoundation\Response;

/**
 * ImageDocument class, provides an easy interface to output image data
 *
 * @since  1.1.0
 */
class ImageDocument extends Document
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
		$this->_mime = 'image/png';

		// Set document type
		$this->_type = 'image';
	}

	/**
	 * Render the document.
	 *
	 * @param boolean $cache   If true, cache the output
	 * @param array $params  Associative array of attributes
	 *
	 * @return  string  The rendered data
	 *
	 * @since  1.0.0
	 */
	public function render(bool $cache = false, array $params = array()): Response
	{
		// Get the image type
		$type = $this->factory->getApplication()->input->get('type', 'png');

		switch ($type)
		{
			case 'jpg':
			case 'jpeg':
				$this->_mime = 'image/jpeg';
				break;
			case 'gif':
				$this->_mime = 'image/gif';
				break;
			case 'png':
			default:
				$this->_mime = 'image/png';
				break;
		}

		$this->_charset = null;

		parent::render($cache, $params);

		return $this->getBuffer();
	}
}
