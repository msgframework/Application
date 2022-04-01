<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplication;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * JsonDocument class, provides an easy interface to parse and display JSON output
 *
 * @link   http://www.json.org/
 * @since  1.0.0
 */
class JsonDocument extends Document
{
	/**
	 * Document name
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected string $_name = 'JsonDocument';

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
		if (isset($_SERVER['HTTP_ACCEPT'])
			&& strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false
			&& strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false)
		{
			// Internet Explorer < 10
			$this->_mime = 'text/plain';
		}
		else
		{
			$this->_mime = 'application/json';
		}

		// Set document type
		$this->setType('json');
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

        $data = $params['data'];

        $response = new JsonResponse($data, $statusCode);

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

	/**
	 * Returns the document name
	 *
	 * @return  string
	 *
	 * @since  1.0.0
	 */
	public function getName(): string
    {
		return $this->_name;
	}

	/**
	 * Sets the document name
	 *
	 * @param string $name  Document name
	 *
	 * @return  $this instance of $this to allow chaining
	 *
	 * @since  1.0.0
	 */
	public function setName(string $name = 'JsonDocument'): self
    {
		$this->_name = $name;

		return $this;
	}
}
