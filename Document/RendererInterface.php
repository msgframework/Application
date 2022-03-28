<?php

namespace Msgframework\Lib\Document;

/**
 * Interface for a document renderer
 *
 * @since  1.1.0
 */
interface RendererInterface
{
	/**
	 * Renders a script and returns the results as a string
	 *
	 * @param   string  $name     The name of the element to render
	 * @param   array|null   $params   Array of values
	 * @param   string|null  $content  Override the output of the renderer
	 *
	 * @return  string  The output of the script
	 *
	 * @since  1.0.0
	 */
	public function render(string $name, ?array $params = null, ?string $content = null): string;
}
