<?php

namespace Msgframework\Lib\Document\Renderer\Feed;

use Msgframework\Lib\Date\Date;
use Msgframework\Lib\Document\DocumentRenderer;
use Msgframework\Lib\Document\FeedDocument;

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
 * @property-read  FeedDocument $_doc  Reference to the Document object that instantiated the renderer
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
		$app = $this->_doc->getApplication();
        $config = $app->getConfig();

        // Gets and sets timezone offset from site configuration
		$tz  = new \DateTimeZone($config->get('offset', 'UTC'));
		$now = new Date('now', $tz);

        /** @var FeedDocument $document */
		$document = $this->_doc;

        $url = $document->getLink();
		$syndicationURL = $document->getSyndicationURL();

		$title = $document->getTitle();

		$feed_title = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');

		$feed = "<feed xmlns=\"http://www.w3.org/2005/Atom\"";

		if ($document->getLanguage() != '')
		{
			$feed .= " xml:lang=\"" . $document->getLanguage() . "\"";
		}

		$feed .= ">\n";
		$feed .= "	<title type=\"text\">" . $feed_title . "</title>\n";
		$feed .= "	<subtitle type=\"text\">" . htmlspecialchars($document->getDescription(), ENT_COMPAT, 'UTF-8') . "</subtitle>\n";

		if (!empty($document->category))
		{
			if (\is_array($document->category))
			{
				foreach ($document->category as $cat)
				{
					$feed .= "	<category term=\"" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "\" />\n";
				}
			}
			else
			{
				$feed .= "	<category term=\"" . htmlspecialchars($document->category, ENT_COMPAT, 'UTF-8') . "\" />\n";
			}
		}

		$feed .= "	<link rel=\"alternate\" type=\"text/html\" href=\"" . $url . "\"/>\n";
		$feed .= "	<id>" . str_replace(' ', '%20', $document->getBase()) . "</id>\n";
		$feed .= "	<updated>" . htmlspecialchars($now->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</updated>\n";

		if ($document->editor != '')
		{
			$feed .= "	<author>\n";
			$feed .= "		<name>" . $document->editor . "</name>\n";

			if ($document->editorEmail != '')
			{
				$feed .= "		<email>" . htmlspecialchars($document->editorEmail, ENT_COMPAT, 'UTF-8') . "</email>\n";
			}

			$feed .= "	</author>\n";
		}

		$feed .= "	<link rel=\"self\" type=\"application/atom+xml\" href=\"" . str_replace(' ', '%20', $url . $syndicationURL) . "\"/>\n";

		for ($i = 0, $count = \count($document->items); $i < $count; $i++)
		{
			$itemlink = $document->items[$i]->link;

			if (preg_match('/[\x80-\xFF]/', $itemlink))
			{
				$itemlink = implode('/', array_map('rawurlencode', explode('/', $itemlink)));
			}

			$feed .= "	<entry>\n";
			$feed .= "		<title>" . htmlspecialchars(strip_tags($document->items[$i]->title), ENT_COMPAT, 'UTF-8') . "</title>\n";
			$feed .= "		<link rel=\"alternate\" type=\"text/html\" href=\"" . $url . $itemlink . "\"/>\n";

			if ($document->items[$i]->date == '')
			{
				$document->items[$i]->date = $now->toUnix();
			}

			$itemDate = new Date($document->items[$i]->date, $tz);
			$feed .= "		<published>" . htmlspecialchars($itemDate->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</published>\n";
			$feed .= "		<updated>" . htmlspecialchars($itemDate->toISO8601(true), ENT_COMPAT, 'UTF-8') . "</updated>\n";

			if (empty($document->items[$i]->guid))
			{
				$itemGuid = str_replace(' ', '%20', $url . $itemlink);
			}
			else
			{
				$itemGuid = htmlspecialchars($document->items[$i]->guid, ENT_COMPAT, 'UTF-8');
			}

			$feed .= "		<id>" . $itemGuid . "</id>\n";

			if ($document->items[$i]->author != '')
			{
				$feed .= "		<author>\n";
				$feed .= "			<name>" . htmlspecialchars($document->items[$i]->author, ENT_COMPAT, 'UTF-8') . "</name>\n";

				if (!empty($document->items[$i]->authorEmail))
				{
					$feed .= "			<email>" . htmlspecialchars($document->items[$i]->authorEmail, ENT_COMPAT, 'UTF-8') . "</email>\n";
				}

				$feed .= "		</author>\n";
			}

			if (!empty($document->items[$i]->description))
			{
				$feed .= "		<summary type=\"html\">" . htmlspecialchars($this->_relToAbs($document->items[$i]->description), ENT_COMPAT, 'UTF-8') . "</summary>\n";
				$feed .= "		<content type=\"html\">" . htmlspecialchars($this->_relToAbs($document->items[$i]->description), ENT_COMPAT, 'UTF-8') . "</content>\n";
			}

			if (!empty($document->items[$i]->category))
			{
                foreach ($document->items[$i]->category as $cat)
                {
                    $feed .= "		<category term=\"" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "\" />\n";
                }
			}

			if ($document->items[$i]->enclosure != null)
			{
				$feed .= "		<link rel=\"enclosure\" href=\"" . $document->items[$i]->enclosure->url . "\" type=\""
					. $document->items[$i]->enclosure->type . "\"  length=\"" . $document->items[$i]->enclosure->length . "\"/>\n";
			}

			$feed .= "	</entry>\n";
		}

		$feed .= "</feed>\n";

		return $feed;
	}
}
