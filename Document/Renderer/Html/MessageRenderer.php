<?php

namespace Msgframework\Lib\Document\Renderer\Html;

use Msgframework\Lib\Document\DocumentRenderer;

/**
 * HTML document renderer for the system message queue
 *
 * @since  1.1.0
 */
class MessageRenderer extends DocumentRenderer
{
	/**
	 * Renders the error stack and returns the results as a string
	 *
	 * @param   string  $name     Not used.
	 * @param   array|null   $params   Associative array of values
	 * @param   string|null  $content  Not used.
	 *
	 * @return  string  The output of the script
	 *
	 * @since  1.0.0
	 */
	public function render(string $name, ?array $params = null, ?string $content = null) : string
	{
		$msgList     = $this->getData();
		$displayData = array(
			'msgList' => $msgList,
			'name'    => $name,
			'params'  => $params,
			'content' => $content,
		);

		$app        = Factory::getApplication();
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/message.php';

		if (is_file($chromePath))
		{
			include_once $chromePath;
		}

		return LayoutHelper::render('joomla.system.message', $displayData);
	}

	/**
	 * Get and prepare system message data for output
	 *
	 * @return  array  An array contains system message
	 *
	 * @since  1.0.0
	 */
	private function getData()
	{
		// Initialise variables.
		$lists = array();

		// Get the message queue
		$messages = Factory::getApplication()->getMessageQueue();

		// Build the sorted message list
		if (\is_array($messages) && !empty($messages))
		{
			foreach ($messages as $msg)
			{
				if (isset($msg['type']) && isset($msg['message']))
				{
					$lists[$msg['type']][] = $msg['message'];
				}
			}
		}

		return $lists;
	}
}
