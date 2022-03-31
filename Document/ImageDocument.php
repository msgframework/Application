<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplication;
use Symfony\Component\HttpFoundation\Response;

/**
 * ImageDocument class, provides an easy interface to output image data
 *
 * @since  1.0.0
 * @todo Need create Image parameter for use in render and refactoring render method
 */
class ImageDocument extends Document
{
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
		$this->setMimeEncoding('image/png');

		// Set document type
		$this->setType('image');
	}

    /**
     * Render the document.
     *
     * @param boolean $cache If true, cache the output
     * @param array $params Associative array of attributes
     *
     * @return  string  The rendered data
     *
     * @throws \Exception
     * @since  1.0.0
     */
	public function render(bool $cache = false, array $params = array()): Response
	{
		// Get the image type
        $app = $this->getApplication();
        $config = $app->getConfig();
		$type = $config->get('type', 'png');

		switch ($type)
		{
			case 'jpg':
			case 'jpeg':
				$this->setMimeEncoding('image/jpeg');
				break;
			case 'gif':
                $this->setMimeEncoding('image/gif');
				break;
			case 'png':
			default:
                $this->setMimeEncoding('image/png');
				break;
		}

		$this->_charset = null;

		$response = parent::render($cache, $params);

        $response->setContent('');

		return $response;
	}
}
