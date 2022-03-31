<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplication;

/**
 * Interface defining a factory which can create Document objects
 *
 * @since  1.0.0
 */
interface FactoryInterface
{

    /**
     * Creates a new Document object for the requested format.
     *
     * @param WebApplication $application
     * @param string $type The document type to instantiate
     * @param array $attributes Array of attributes
     *
     * @return  Document
     *
     * @since  1.0.0
     */
	public function createDocument(WebApplication $application, string $type = 'html', array $attributes = []): Document;

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
	public function createRenderer(Document $document, string $type, string $docType = ''): RendererInterface;
}
