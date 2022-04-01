<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplication;
use Symfony\Component\HttpFoundation\Response;

/**
 * RawDocument class, provides an easy interface to parse and display raw output
 *
 * @since  1.0.0
 */
class RawDocument extends Document
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
		$this->setMimeEncoding('text/html');

		// Set document type
		$this->setType('raw');
	}

    /**
     * Render the document.
     *
     * @param boolean $cache If true, cache the output
     * @param array $params Associative array of attributes
     *
     * @return Response The rendered data
     *
     * @throws \Exception
     * @since  1.0.0
     */
	public function render(bool $cache = false, array $params = array()): Response
	{
        if (\array_key_exists('statusCode', $params)) {
            $statusCode = $params['statusCode'];
        } else {
            $statusCode = 200;
        }

        $response = parent::render($cache, $params);

        $response->setContent($params['data']);
        $response->setStatusCode($statusCode);

        if (isset($params['maxAge']) && \array_key_exists('maxAge', $params)) {
            $response->setMaxAge($params['maxAge']);
        }

        if (isset($params['sharedAge']) && \array_key_exists('sharedAge', $params)) {
            $response->setSharedMaxAge($params['sharedAge']);
        }

        if (isset($params['isPrivate']) && \array_key_exists('isPrivate', $params) && $params['isPrivate'] == true) {
            $response->setPrivate();
        } elseif (!isset($params['isPrivate']) || false === $params['isPrivate'] || (null === $params['isPrivate'] && (null !== $params['maxAge'] || null !== $params['sharedAge']))) {
            $response->setPublic();
        }

        return $response;
	}
}
