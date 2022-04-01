<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplicationInterface;

/**
 * Default factory for creating Document objects
 *
 * @since  1.0.0
 */
class Factory implements FactoryInterface
{

    /**
     * Creates a new Document object for WebApplication in the requested format.
     *
     * @param WebApplicationInterface $application
     * @param string $type The document type to instantiate
     * @param array $attributes Array of attributes
     *
     * @return  Document
     *
     * @since  1.0.0
     */
	public function createDocument(WebApplicationInterface $application, string $type = 'html', array $attributes = []): Document
	{
		$type  = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$ntype = null;

		$class = __NAMESPACE__ . '\\' . ucfirst($type) . 'Document';

		if (!class_exists($class))
		{
			$ntype = $type;
			$class = RawDocument::class;
		}

		/** @var Document $document */
		$document = new $class($this, $application, $attributes);

		if (!\is_null($ntype))
		{
			// Set the type to the Document type originally requested
            $document->setType($ntype);
		}

		return $document;
	}

	/**
	 * Creates a new renderer object.
	 *
	 * @param   Document  $document  The Document instance to attach to the renderer
	 * @param   string    $type      The renderer type to instantiate
	 * @param   string    $docType   The document type the renderer is part of
	 *
	 * @return  RendererInterface
	 *
	 * @since  1.0.0
	 */
	public function createRenderer(Document $document, string $type, string $docType = ''): RendererInterface
	{
		$docType = $docType ? ucfirst($docType) : ucfirst($document->getType());

		// Determine the path and class
		$class = __NAMESPACE__ . '\\Renderer\\' . $docType . '\\' . ucfirst($type) . 'Renderer';

		if (!class_exists($class))
		{
            throw new \RuntimeException(sprintf('Unable to load renderer class %s', $type), 500);
		}

		return new $class($document);
	}
}
