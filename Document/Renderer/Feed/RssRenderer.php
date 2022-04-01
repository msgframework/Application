<?php

namespace Msgframework\Lib\Document\Renderer\Feed;

use Msgframework\Lib\Date\Date;
use Msgframework\Lib\Document\DocumentRenderer;
use Msgframework\Lib\Document\FeedDocument;
use Msgframework\Lib\Route\Route;

/**
 * RssRenderer is a feed that implements RSS 2.0 Specification
 *
 * @link   http://www.rssboard.org/rss-specification
 * @since  1.0.0
 *
 * @property-read  FeedDocument  $_doc  Reference to the Document object that instantiated the renderer
 */
class RssRenderer extends DocumentRenderer
{
	/**
	 * Renderer mime type
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected string $_mime = 'application/rss+xml';

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
	public function render(string $name = '', ?array $params = null, string $content = null): string
	{
		$app = $this->_doc->getApplication();
        $config = $app->getConfig();
		$tz  = new \DateTimeZone($config->get('offset', 'UTC'));

		$data = $this->_doc;

		// If the last build date from the document isn't a Date object, create one
		if (!($data->lastBuildDate instanceof Date))
		{
			// Gets and sets timezone offset from site configuration
			$data->lastBuildDate = new Date('now', $tz);
		}

		$url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$syndicationURL = Route::_('&format=feed&type=rss');

		$title = $data->getTitle();

		$feed_title = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');

		$datalink = $data->getLink();

		if (preg_match('/[\x80-\xFF]/', $datalink))
		{
			$datalink = implode('/', array_map('rawurlencode', explode('/', $datalink)));
		}

		$feed = "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
		$feed .= "	<channel>\n";
		$feed .= "		<title>" . $feed_title . "</title>\n";
		$feed .= "		<description><![CDATA[" . $data->getDescription() . "]]></description>\n";
		$feed .= "		<link>" . str_replace(' ', '%20', $url . $datalink) . "</link>\n";
		$feed .= "		<lastBuildDate>" . htmlspecialchars($data->lastBuildDate->toRFC822(true), ENT_COMPAT, 'UTF-8') . "</lastBuildDate>\n";
		$feed .= "		<generator>" . $data->getGenerator() . "</generator>\n";
		$feed .= "		<atom:link rel=\"self\" type=\"application/rss+xml\" href=\"" . str_replace(' ', '%20', $url . $syndicationURL) . "\"/>\n";

		if ($data->image != null)
		{
			$feed .= "		<image>\n";
			$feed .= "			<url>" . $data->image->url . "</url>\n";
			$feed .= "			<title>" . htmlspecialchars($data->image->title, ENT_COMPAT, 'UTF-8') . "</title>\n";
			$feed .= "			<link>" . str_replace(' ', '%20', $data->image->link) . "</link>\n";

			if ($data->image->width != '')
			{
				$feed .= "			<width>" . $data->image->width . "</width>\n";
			}

			if ($data->image->height != '')
			{
				$feed .= "			<height>" . $data->image->height . "</height>\n";
			}

			if ($data->image->description != '')
			{
				$feed .= "			<description><![CDATA[" . $data->image->description . "]]></description>\n";
			}

			$feed .= "		</image>\n";
		}

		if ($data->getLanguage() !== '')
		{
			$feed .= "		<language>" . $data->getLanguage() . "</language>\n";
		}

		if ($data->copyright != '')
		{
			$feed .= "		<copyright>" . htmlspecialchars($data->copyright, ENT_COMPAT, 'UTF-8') . "</copyright>\n";
		}

		if ($data->editorEmail != '')
		{
			$feed .= "		<managingEditor>" . htmlspecialchars($data->editorEmail, ENT_COMPAT, 'UTF-8') . ' ('
				. htmlspecialchars($data->editor, ENT_COMPAT, 'UTF-8') . ")</managingEditor>\n";
		}

		if ($data->webmaster != '')
		{
			$feed .= "		<webMaster>" . htmlspecialchars($data->webmaster, ENT_COMPAT, 'UTF-8') . "</webMaster>\n";
		}

		if ($data->pubDate != '')
		{
			$pubDate = new Date($data->pubDate, $tz);
			$feed .= "		<pubDate>" . htmlspecialchars($pubDate->toRFC822(true), ENT_COMPAT, 'UTF-8') . "</pubDate>\n";
		}

		if (!empty($data->category))
		{
			if (\is_array($data->category))
			{
				foreach ($data->category as $cat)
				{
					$feed .= "		<category>" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "</category>\n";
				}
			}
			else
			{
				$feed .= "		<category>" . htmlspecialchars($data->category, ENT_COMPAT, 'UTF-8') . "</category>\n";
			}
		}

		if ($data->docs != '')
		{
			$feed .= "		<docs>" . htmlspecialchars($data->docs, ENT_COMPAT, 'UTF-8') . "</docs>\n";
		}

		if ($data->ttl != '')
		{
			$feed .= "		<ttl>" . htmlspecialchars($data->ttl, ENT_COMPAT, 'UTF-8') . "</ttl>\n";
		}

		if ($data->rating != '')
		{
			$feed .= "		<rating>" . htmlspecialchars($data->rating, ENT_COMPAT, 'UTF-8') . "</rating>\n";
		}

		if ($data->skipHours != '')
		{
			$feed .= "		<skipHours>" . htmlspecialchars($data->skipHours, ENT_COMPAT, 'UTF-8') . "</skipHours>\n";
		}

		if ($data->skipDays != '')
		{
			$feed .= "		<skipDays>" . htmlspecialchars($data->skipDays, ENT_COMPAT, 'UTF-8') . "</skipDays>\n";
		}

		for ($i = 0, $count = \count($data->items); $i < $count; $i++)
		{
			$itemlink = $data->items[$i]->link;

			if (preg_match('/[\x80-\xFF]/', $itemlink))
			{
				$itemlink = implode('/', array_map('rawurlencode', explode('/', $itemlink)));
			}

			if ((strpos($itemlink, 'http://') === false) && (strpos($itemlink, 'https://') === false))
			{
				$itemlink = str_replace(' ', '%20', $url . $itemlink);
			}

			$feed .= "		<item>\n";
			$feed .= "			<title>" . htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8') . "</title>\n";
			$feed .= "			<link>" . str_replace(' ', '%20', $itemlink) . "</link>\n";

			if (empty($data->items[$i]->guid))
			{
				$feed .= "			<guid isPermaLink=\"true\">" . str_replace(' ', '%20', $itemlink) . "</guid>\n";
			}
			else
			{
				$feed .= "			<guid isPermaLink=\"false\">" . htmlspecialchars($data->items[$i]->guid, ENT_COMPAT, 'UTF-8') . "</guid>\n";
			}

			$feed .= "			<description><![CDATA[" . $this->_relToAbs($data->items[$i]->description) . "]]></description>\n";

			if ($data->items[$i]->authorEmail != '')
			{
				$feed .= '			<author>'
					. htmlspecialchars($data->items[$i]->authorEmail . ' (' . $data->items[$i]->author . ')', ENT_COMPAT, 'UTF-8') . "</author>\n";
			}

			/*
			 * @todo: On hold
			 * if ($data->items[$i]->source!='')
			 * {
			 *   $data.= "			<source>" . htmlspecialchars($data->items[$i]->source, ENT_COMPAT, 'UTF-8') . "</source>\n";
			 * }
			 */

			if (empty($data->items[$i]->category) === false)
			{
				if (\is_array($data->items[$i]->category))
				{
					foreach ($data->items[$i]->category as $cat)
					{
						$feed .= "			<category>" . htmlspecialchars($cat, ENT_COMPAT, 'UTF-8') . "</category>\n";
					}
				}
				else
				{
					$feed .= "			<category>" . htmlspecialchars($data->items[$i]->category, ENT_COMPAT, 'UTF-8') . "</category>\n";
				}
			}

			if ($data->items[$i]->comments != '')
			{
				$feed .= "			<comments>" . htmlspecialchars($data->items[$i]->comments, ENT_COMPAT, 'UTF-8') . "</comments>\n";
			}

			if ($data->items[$i]->date != '')
			{
				$itemDate = new Date($data->items[$i]->date, $tz);
				$feed .= "			<pubDate>" . htmlspecialchars($itemDate->toRFC822(true), ENT_COMPAT, 'UTF-8') . "</pubDate>\n";
			}

			if ($data->items[$i]->enclosure != null)
			{
				$feed .= "			<enclosure url=\"";
				$feed .= $data->items[$i]->enclosure->url;
				$feed .= "\" length=\"";
				$feed .= $data->items[$i]->enclosure->length;
				$feed .= "\" type=\"";
				$feed .= $data->items[$i]->enclosure->type;
				$feed .= "\"/>\n";
			}

			$feed .= "		</item>\n";
		}

		$feed .= "	</channel>\n";
		$feed .= "</rss>\n";

		return $feed;
	}
}
