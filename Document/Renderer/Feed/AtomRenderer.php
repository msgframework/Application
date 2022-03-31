<?php

namespace Msgframework\Lib\Document\Renderer\Feed;



use Msgframework\Lib\Document\DocumentRenderer;
use Msgframework\Lib\Language\Text;

/**
 * AtomRenderer is a feed that implements the atom specification
 *
 * Please note that just by using this class you won't automatically
 * produce valid atom files. For example, you have to specify either an editor
 * for the feed or an author for every single feed item.
 *
 * @link   http://www.atomenabled.org/developers/syndication/atom-format-spec.php
 * @since  1.0.0
 *
 * @property-read  \Msgframework\Lib\Document\FeedDocument  $_doc  Reference to the Document object that instantiated the renderer
 */
class AtomRenderer extends DocumentRenderer
{
	/**
	 * Document mime type
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected string $_mime = 'application/atom+xml';

    /**
     * Render the feed.
     *
     * @param string $name The name of the element to render
     * @param array|null $params Array of values
     * @param string|null $content Override the output of the renderer
     *
     * @return  string  The output of the script
     *
     * @throws \Exception
     * @since  1.0.0
     * @see     DocumentRenderer::render()
     */
	public function render(string $name = '', ?array $params = null, ?string $content = null): string
	{
		$app = Factory::getApplication();

		// Gets and sets timezone offset from site configuration
		$tz  = new \DateTimeZone($app->get('offset'));
		$now = Factory::getDate();
		$now->setTimezone($tz);

		$data = $this->_doc;

		$url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$syndicationURL = Route::_('&format=feed&type=atom');

		$title = $data->getTitle();

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $data->getTitle());
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $data->getTitle(), $app->get('sitename'));
		}

		$feed_title = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');

		$feed = "<feed xmlns=\"http://www.w3.org/2005/Atom\"";

		if ($data->getLanguage() != '')
		{
			$feed .= " xml:lang=\"" . $data->getLanguage() . "\"";
		}

		$feed .= ">\n";
		$feed .= "	<title type=\"text\">" . $feed_title . "</title>\n";
		$feed .= "	<subtitle type=\"text\">" . htmlspecialchars($data->getDescription(), ENT_COMPAT, 'UTF-8') . "</subtitle>\n";

		if (!empty($data->category))
		{
			if (\is_array($data->category))
			{
				foreach ($data->category as $cat)
				{
					$feed .= "	<category term=\"" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "\" />\n";
				}
			}
			else
			{
				$feed .= "	<category term=\"" . htmlspecialchars($data->category, ENT_COMPAT, 'UTF-8') . "\" />\n";
			}
		}

		$feed .= "	<link rel=\"alternate\" type=\"text/html\" href=\"" . $url . "\"/>\n";
		$feed .= "	<id>" . str_replace(' ', '%20', $data->getBase()) . "</id>\n";
		$feed .= "	<updated>" . htmlspecialchars($now->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</updated>\n";

		if ($data->editor != '')
		{
			$feed .= "	<author>\n";
			$feed .= "		<name>" . $data->editor . "</name>\n";

			if ($data->editorEmail != '')
			{
				$feed .= "		<email>" . htmlspecialchars($data->editorEmail, ENT_COMPAT, 'UTF-8') . "</email>\n";
			}

			$feed .= "	</author>\n";
		}

		$versionHtmlEscaped = '';

		if ($app->get('MetaVersion', 0))
		{
			$minorVersion       = Version::MAJOR_VERSION . '.' . Version::MINOR_VERSION;
			$versionHtmlEscaped = ' version="' . htmlspecialchars($minorVersion, ENT_COMPAT, 'UTF-8') . '"';
		}

		$feed .= "	<generator uri=\"https://www.joomla.org\"" . $versionHtmlEscaped . ">" . $data->getGenerator() . "</generator>\n";
		$feed .= "	<link rel=\"self\" type=\"application/atom+xml\" href=\"" . str_replace(' ', '%20', $url . $syndicationURL) . "\"/>\n";

		for ($i = 0, $count = \count($data->items); $i < $count; $i++)
		{
			$itemlink = $data->items[$i]->link;

			if (preg_match('/[\x80-\xFF]/', $itemlink))
			{
				$itemlink = implode('/', array_map('rawurlencode', explode('/', $itemlink)));
			}

			$feed .= "	<entry>\n";
			$feed .= "		<title>" . htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8') . "</title>\n";
			$feed .= "		<link rel=\"alternate\" type=\"text/html\" href=\"" . $url . $itemlink . "\"/>\n";

			if ($data->items[$i]->date == '')
			{
				$data->items[$i]->date = $now->toUnix();
			}

			$itemDate = Factory::getDate($data->items[$i]->date);
			$itemDate->setTimezone($tz);
			$feed .= "		<published>" . htmlspecialchars($itemDate->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</published>\n";
			$feed .= "		<updated>" . htmlspecialchars($itemDate->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</updated>\n";

			if (empty($data->items[$i]->guid))
			{
				$itemGuid = str_replace(' ', '%20', $url . $itemlink);
			}
			else
			{
				$itemGuid = htmlspecialchars($data->items[$i]->guid, ENT_COMPAT, 'UTF-8');
			}

			$feed .= "		<id>" . $itemGuid . "</id>\n";

			if ($data->items[$i]->author != '')
			{
				$feed .= "		<author>\n";
				$feed .= "			<name>" . htmlspecialchars($data->items[$i]->author, ENT_COMPAT, 'UTF-8') . "</name>\n";

				if (!empty($data->items[$i]->authorEmail))
				{
					$feed .= "			<email>" . htmlspecialchars($data->items[$i]->authorEmail, ENT_COMPAT, 'UTF-8') . "</email>\n";
				}

				$feed .= "		</author>\n";
			}

			if (!empty($data->items[$i]->description))
			{
				$feed .= "		<summary type=\"html\">" . htmlspecialchars($this->_relToAbs($data->items[$i]->description), ENT_COMPAT, 'UTF-8') . "</summary>\n";
				$feed .= "		<content type=\"html\">" . htmlspecialchars($this->_relToAbs($data->items[$i]->description), ENT_COMPAT, 'UTF-8') . "</content>\n";
			}

			if (!empty($data->items[$i]->category))
			{
				if (\is_array($data->items[$i]->category))
				{
					foreach ($data->items[$i]->category as $cat)
					{
						$feed .= "		<category term=\"" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "\" />\n";
					}
				}
				else
				{
					$feed .= "		<category term=\"" . htmlspecialchars($data->items[$i]->category, ENT_COMPAT, 'UTF-8') . "\" />\n";
				}
			}

			if ($data->items[$i]->enclosure != null)
			{
				$feed .= "		<link rel=\"enclosure\" href=\"" . $data->items[$i]->enclosure->url . "\" type=\""
					. $data->items[$i]->enclosure->type . "\"  length=\"" . $data->items[$i]->enclosure->length . "\"/>\n";
			}

			$feed .= "	</entry>\n";
		}

		$feed .= "</feed>\n";

		return $feed;
	}
}
