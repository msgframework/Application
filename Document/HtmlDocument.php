<?php

namespace Msgframework\Lib\Document;

use Msgframework\Lib\Application\WebApplication;
use Msgframework\Lib\AssetManager\WebAssetManager;
use Msgframework\Lib\AssetManager\WebAssetRegistry;
use Msgframework\Lib\Extension\TemplateInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * HtmlDocument class, provides an easy interface to parse and display a HTML document
 *
 * @since  1.0.0
 */
class HtmlDocument extends Document
{
    /**
     * HtmlDocument full URL
     *
     * @var    string
     * @since  1.0.0
     */
    public string $link = '';

    /**
     * HtmlDocument base URL
     *
     * @var    string
     * @since  1.0.0
     */
    public string $base = '';

    /**
     * Array of Header `<link>` tags
     *
     * @var    array
     * @since  1.0.0
     */
    public array $_links = array();

    /**
     * Array of custom tags
     *
     * @var    array
     * @since  1.0.0
     */
    public array $_custom = array();

    /**
     * Script nonce (string if set, null otherwise)
     *
     * @var    string|null
     * @since  1.0.0
     */
    public ?string $cspNonce = null;

    /**
     * String holding parsed template
     *
     * @var    TemplateInterface
     * @since  1.0.0
     */
    protected TemplateInterface $template;

    /**
     * Integer with caching setting
     *
     * @var    integer|null
     * @since  1.0.0
     */
    protected ?int $_caching = null;

    /**
     * Set to true when the document should be output as HTML5
     *
     * @var    boolean
     * @since  1.0.0
     */
    private bool $html5 = true;

    /**
     * Meta Description tag value.
     * @var string
     * @since  1.0.0
     */
    protected string $description;

    /**
     * Array of meta tags
     *
     * @var    array
     * @since  1.0.0
     */
    public array $_metaTags = array();

    /**
     * Preload manager
     *
     * @var    PreloadManagerInterface
     * @since  1.0.0
     */
    protected PreloadManagerInterface $preloadManager;

    /**
     * The supported preload types
     *
     * @var    array
     * @since  1.0.0
     */
    protected array $preloadTypes = ['preload', 'dns-prefetch', 'preconnect', 'prefetch', 'prerender'];

    /**
     * Web Asset instance
     *
     * @var    WebAssetManager
     * @since  1.0.0
     */
    protected WebAssetManager $webAssetManager;

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

        if (\array_key_exists('template', $options)) {
            $this->setTemplate($options['template']);
        } else {
            throw new \InvalidArgumentException(sprintf('Missing required parameter "%s" for document "%s"', 'template', self::class));
        }

        if (\array_key_exists('preloadManager', $options)) {
            $this->setPreloadManager($options['preloadManager']);
        } else {
            $this->setPreloadManager(new PreloadManager);
        }

        if (\array_key_exists('webAssetManager', $options)) {
            $this->setWebAssetManager($options['webAssetManager']);
        } else {
            $registry = new WebAssetRegistry('');

            $this->setWebAssetManager(new WebAssetManager($registry));
        }

        if (\array_key_exists('link', $options))
        {
            $this->setLink($options['link']);
        }

        if (\array_key_exists('base', $options))
        {
            $this->setBase($options['base']);
        }

        // Set document type
        $this->setType('html');

        // Set default mime type and document metadata (metadata syncs with mime type by default)
        $this->setMimeEncoding('text/html');
    }

    /**
     * Get the HTML document head data
     *
     * @return  array  The document head data in array form
     *
     * @since  1.0.0
     */
    public function getHeadData(): array
    {
        $data = array();
        $data['title'] = $this->title;
        $data['description'] = $this->description;
        $data['link'] = $this->link;
        $data['metaTags'] = $this->_metaTags;
        $data['links'] = $this->_links;
        $data['custom'] = $this->_custom;

        // Get Asset manager state
        $wa = $this->getWebAssetManager();
        $waState = $wa->getManagerState();

        // Get asset objects and filter only manually added/enabled assets,
        // Dependencies will be picked up from registry files
        $waState['assets'] = [];

        foreach ($waState['activeAssets'] as $assetType => $assetNames) {
            foreach ($assetNames as $assetName => $assetState) {
                if ($assetState === WebAssetManager::ASSET_STATE_ACTIVE) {
                    $waState['assets'][$assetType][] = $wa->getAsset($assetType, $assetName);
                }
            }
        }

        // We have loaded asset objects, now can remove unused stuff
        unset($waState['activeAssets']);

        $data['assetManager'] = $waState;

        return $data;
    }

    /**
     * Reset the HTML document head data
     *
     * @param mixed $types type or types of the heads elements to reset
     *
     * @return  $this  instance of $this to allow chaining
     *
     * @since  1.0.0
     */
    public function resetHeadData($types = null): self
    {
        if (\is_null($types)) {
            $this->title = '';
            $this->description = '';
            $this->link = '';
            $this->_metaTags = array();
            $this->_links = array();
            $this->_custom = array();
        }

        if (\is_array($types)) {
            foreach ($types as $type) {
                $this->resetHeadDatum($type);
            }
        }

        if (\is_string($types)) {
            $this->resetHeadDatum($types);
        }

        return $this;
    }

    /**
     * Reset a part the HTML document head data
     *
     * @param string $type type of the heads elements to reset
     *
     * @return  void
     *
     * @since  1.0.0
     */
    private function resetHeadDatum(string $type)
    {
        switch ($type) {
            case 'title':
            case 'description':
            case 'link':
                $this->{$type} = '';
                break;

            case 'metaTags':
            case 'links':
            case 'custom':
                $realType = '_' . $type;
                $this->{$realType} = array();
                break;
        }
    }

    /**
     * Set the HTML document head data
     *
     * @param array $data The document head data in array form
     *
     * @return $this instance of $this to allow chaining
     *
     * @since  1.0.0
     */
    public function setHeadData(array $data): self
    {
        if (empty($data)) {
            return $this;
        }

        $this->title = $data['title'] ?? $this->title;
        $this->description = $data['description'] ?? $this->description;
        $this->link = $data['link'] ?? $this->link;
        $this->_metaTags = $data['metaTags'] ?? $this->_metaTags;
        $this->_links = $data['links'] ?? $this->_links;
        $this->_custom = $data['custom'] ?? $this->_custom;

        // Restore asset manager state
        $waManager = $this->getWebAssetManager();

        if (!empty($data['assetManager']['registryFiles'])) {
            $waRegistry = $waManager->getRegistry();

            foreach ($data['assetManager']['registryFiles'] as $registryFile) {
                $waRegistry->addRegistryFile($registryFile);
            }
        }

        if (!empty($data['assetManager']['assets'])) {
            foreach ($data['assetManager']['assets'] as $assetType => $assets) {
                foreach ($assets as $asset) {
                    $waManager->registerAsset($assetType, $asset)->useAsset($assetType, $asset->getName());
                }
            }
        }

        return $this;
    }

    /**
     * Merge the HTML document head data
     *
     * @param array $data The document head data in array form
     *
     * @return  $this instance of $this to allow chaining
     *
     * @since  1.0.0
     */
    public function mergeHeadData(array $data): self
    {
        if (empty($data)) {
            return $this;
        }

        $this->title = (isset($data['title']) && !empty($data['title']) && !stristr($this->title, $data['title']))
            ? $this->title . $data['title']
            : $this->title;
        $this->description = (isset($data['description']) && !empty($data['description']) && !stristr($this->description, $data['description']))
            ? $this->description . $data['description']
            : $this->description;
        $this->link = $data['link'] ?? $this->link;

        if (isset($data['metaTags'])) {
            foreach ($data['metaTags'] as $type1 => $data1) {
                $attr = $type1 === 'http-equiv';

                foreach ($data1 as $name2 => $data2) {
                    $this->setMetaData($name2, $data2, $attr);
                }
            }
        }

        $this->_links = (isset($data['links']) && !empty($data['links']) && \is_array($data['links']))
            ? array_unique(array_merge($this->_links, $data['links']), SORT_REGULAR)
            : $this->_links;

        $this->_custom = (isset($data['custom']) && !empty($data['custom']) && \is_array($data['custom']))
            ? array_unique(array_merge($this->_custom, $data['custom']))
            : $this->_custom;

        // Restore asset manager state
        $waManager = $this->getWebAssetManager();

        if (!empty($data['assetManager']['registryFiles'])) {
            $waRegistry = $waManager->getRegistry();

            foreach ($data['assetManager']['registryFiles'] as $registryFile) {
                $waRegistry->addRegistryFile($registryFile);
            }
        }

        if (!empty($data['assetManager']['assets'])) {
            foreach ($data['assetManager']['assets'] as $assetType => $assets) {
                foreach ($assets as $asset) {
                    $waManager->registerAsset($assetType, $asset)->useAsset($assetType, $asset->getName());
                }
            }
        }

        return $this;
    }

    /**
     * Adds `<link>` tags to the head of the document
     *
     * $relType defaults to 'rel' as it is the most common relation type used.
     * ('rev' refers to reverse relation, 'rel' indicates normal, forward relation.)
     * Typical tag: `<link href="index.php" rel="Start">`
     *
     * @param string $href The link that is being related.
     * @param string $relation Relation of link.
     * @param string $relType Relation type attribute.  Either rel or rev (default: 'rel').
     * @param array $attribs Associative array of remaining attributes.
     *
     * @return $this instance of $this to allow chaining
     *
     * @since  1.0.0
     */
    public function addHeadLink(string $href, string $relation, string $relType = 'rel', array $attribs = array()): self
    {
        $this->_links[$href]['relation'] = $relation;
        $this->_links[$href]['relType'] = $relType;
        $this->_links[$href]['attribs'] = $attribs;

        return $this;
    }

    /**
     * Adds a shortcut icon (favicon)
     *
     * This adds a link to the icon shown in the favorites list or on
     * the left of the url in the address bar. Some browsers display
     * it on the tab, as well.
     *
     * @param string $href The link that is being related.
     * @param string $type File type
     * @param string $relation Relation of link
     *
     * @return $this instance of $this to allow chaining
     *
     * @since  1.0.0
     */
    public function addFavicon(string $href, string $type = 'image/vnd.microsoft.icon', string $relation = 'shortcut icon'): self
    {
        $href = str_replace('\\', '/', $href);
        $this->addHeadLink($href, $relation, 'rel', array('type' => $type));

        return $this;
    }

    /**
     * Adds a custom HTML string to the head block
     *
     * @param string $html The HTML to add to the head
     *
     * @return  $this instance of $this to allow chaining
     *
     * @since  1.0.0
     */
    public function addCustomTag(string $html): self
    {
        $this->_custom[] = trim($html);

        return $this;
    }

    /**
     * Returns whether the document is set up to be output as HTML5
     *
     * @return  boolean true when HTML5 is used
     *
     * @since  1.0.0
     */
    public function isHtml5(): bool
    {
        return $this->html5;
    }

    /**
     * Sets whether the document should be output as HTML5
     *
     * @param bool $state True when HTML5 should be output
     *
     * @return  void
     *
     * @since  1.0.0
     */
    public function setHtml5(bool $state): void
    {
        $this->html5 = $state;
	}

	/**
	 * Get the contents of a document include
	 *
	 * @param string|null $type     The type of renderer
	 * @param string|null $name     The name of the element to render
	 * @param array $attribs  Associative array of remaining attributes.
	 *
	 * @return  mixed|string The output of the renderer
	 *
	 * @since  1.0.0
	 */
	public function getBuffer(?string $type = null, ?string $name = null, array $attribs = array())
	{
		// If no type is specified, return the whole buffer
		if ($type === null)
		{
			return parent::$_buffer;
		}

		if (isset(parent::$_buffer[$type][$name]))
		{
			return parent::$_buffer[$type][$name];
		}

		$renderer = $this->loadRenderer($type);

		if ($this->_caching == true && $type === 'modules' && $name !== 'debug')
		{
			/** @var  \Msgframework\Lib\Document\Renderer\Html\ModulesRenderer  $renderer */
			$cache = \Cms::getCache('com_modules', '');
			$hash = md5(serialize(array($name, $attribs, null, get_class($renderer))));
			$cbuffer = $cache->get('cbuffer_' . $type);

			if (isset($cbuffer[$hash]))
			{
				return Cache::getWorkarounds($cbuffer[$hash], array('mergehead' => 1));
			}

			$options = array();
			$options['nopathway'] = 1;
			$options['nomodules'] = 1;
			$options['modulemode'] = 1;

			$this->setBuffer($renderer->render($name, $attribs, null), $type, $name);
			$data = parent::$_buffer[$type][$name][$title];

			$tmpdata = Cache::setWorkarounds($data, $options);

			$cbuffer[$hash] = $tmpdata;

			$cache->store($cbuffer, 'cbuffer_' . $type);
		}
		else
		{
			$this->setBuffer($renderer->render($name, $attribs, null), array('type' => $type, 'name' => $name));
		}

		return parent::$_buffer[$type][$name];
	}

	/**
	 * Set the contents a document includes
	 *
	 * @param   string  $content  The content to be set in the buffer.
	 * @param   array   $options  Array of optional elements.
	 *
	 * @return  self
	 *
	 * @since  1.0.0
	 */
	public function setBuffer(string $content, array $options = array()): self
	{
		parent::$_buffer[$options['type']][$options['name']] = $content;

		return $this;
	}

	/**
	 * Outputs the template to the browser.
	 *
	 * @param   boolean  $caching  If true, cache the output
	 * @param array $params   Associative array of attributes
	 *
	 * @return  string The rendered data
	 *
	 * @since  1.0.0
	 */
	public function render(bool $caching = false, array $params = array()): Response
	{
        if (\array_key_exists('statusCode', $params))
        {
            $statusCode = $params['statusCode'];
        } else {
            $statusCode = 200;
        }

        if (\array_key_exists('tmpl', $params))
        {
            $file = $params['tmpl'].'.php';
        } else {
            $file = 'index.php';
        }

        $response = new Response($this->_loadTemplate($this->template->getDir(), $file), $statusCode);

        if (isset($params['maxAge']) && \array_key_exists('maxAge', $params))
        {
            $response->setMaxAge($params['maxAge']);
        }

        if (isset($params['sharedAge']) && \array_key_exists('sharedAge', $params))
        {
            $response->setSharedMaxAge($params['sharedAge']);
        }

        if (isset($params['isPrivate']) && \array_key_exists('isPrivate', $params) && $params['isPrivate'] == true)
        {
            $response->setPrivate();
        } elseif (!isset($params['isPrivate']) || false === $params['isPrivate'] || (null === $params['isPrivate'] && (null !== $params['maxAge'] || null !== $params['sharedAge']))) {
            $response->setPublic();
        }

        //$this->preloadAssets();

        return $response;
	}

    /**
     * Generate the Link header for assets configured for preloading
     *
     * @return  void
     *
     * @since  1.1.0
     */
    protected function preloadAssets()
    {
        $waManager = $this->getWebAssetManager();

        // Process stylesheets first
        foreach ($waManager->getAssets('style', true) as $key => $item) {
            if (null !== $item->getOption('preload', null)) {
                foreach ($item->getOption('preload', null) as $preloadMethod) {
                    // Make sure the preload method is supported, special case for `dns-prefetch` to convert it to the right method name
                    if ($preloadMethod === 'dns-prefetch') {
                        $this->getPreloadManager()->dnsPrefetch($item->getUri());
                    } elseif (\in_array($preloadMethod, $this->preloadTypes)) {
                        $this->getPreloadManager()->$preloadMethod($item->getUri());
                    } else {
                        throw new \InvalidArgumentException(sprintf('The "%s" method is not supported for preloading.', $preloadMethod), 500);
                    }
                }
            }
        }

        // Now process scripts
        foreach ($waManager->getAssets('script', true) as $key => $item) {
            if (null !== $item->getOption('preload', null)) {
                foreach ($item->getOption('preload', null) as $preloadMethod) {
                    // Make sure the preload method is supported, special case for `dns-prefetch` to convert it to the right method name
                    if ($preloadMethod === 'dns-prefetch') {
                        $this->getPreloadManager()->dnsPrefetch($item->getUri());
                    } elseif (\in_array($preloadMethod, $this->preloadTypes)) {
                        $this->getPreloadManager()->$preloadMethod($item->getUri());
                    } else {
                        throw new \InvalidArgumentException(sprintf('The "%s" method is not supported for preloading.', $preloadMethod), 500);
                    }
                }
            }
        }
    }

	/**
	 * Load a template file
	 *
	 * @param string $directory  The name of the template
	 * @param string $filename   The actual filename
	 *
	 * @return  string  The contents of the template
	 *
	 * @since  1.0.0
	 */
	protected function _loadTemplate(string $directory, string $filename): string
    {
		$contents = '';

		// Check to see if we have a valid template file
		if (is_file($directory . '/' . $filename))
		{
			// Store the file path
			$this->_file = $directory . '/' . $filename;

			// Get the file content
			ob_start();
			require $directory . '/' . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		return $contents;
	}

    /**
     * Sets the document MIME encoding that is sent to the browser.
     *
     * This usually will be text/html because most browsers cannot yet
     * accept the proper mime settings for XHTML: application/xhtml+xml
     * and to a lesser extent application/xml and text/xml. See the W3C note
     * ({@link https://www.w3.org/TR/xhtml-media-types/
     * https://www.w3.org/TR/xhtml-media-types/}) for more details.
     *
     * @param   string   $type  The document type to be sent
     * @param   boolean  $sync  Should the type be synced with HTML?
     *
     * @return  self
     *
     * @since  1.1.0
     *
     * @link    https://www.w3.org/TR/xhtml-media-types/
     */
    public function setMimeEncoding(string $type = 'text/html'): self
    {
        $this->_mime = strtolower($type);

        //$this->setMetaData('content-type', $type . '; charset=' . $this->_charset, true);

        return $this;
    }

    /**
     * Set the preload manager
     *
     * @param   PreloadManagerInterface  $preloadManager  The preload manager service
     *
     * @return  self instance of $this to allow chaining
     *
     * @since  1.1.0
     */
    public function setPreloadManager(PreloadManagerInterface $preloadManager): self
    {
        $this->preloadManager = $preloadManager;

        return $this;
    }

    /**
     * Return the preload manager
     *
     * @return  PreloadManagerInterface
     *
     * @since  1.1.0
     */
    public function getPreloadManager(): PreloadManagerInterface
    {
        return $this->preloadManager;
    }

    /**
     * Set WebAsset manager
     *
     * @param   WebAssetManager  $webAsset  The WebAsset instance
     *
     * @return  Document
     *
     * @since   4.0.0
     */
    public function setWebAssetManager(WebAssetManager $webAsset): self
    {
        $this->webAssetManager = $webAsset;

        return $this;
    }

    /**
     * Return WebAsset manager
     *
     * @return  WebAssetManager
     *
     * @since   4.0.0
     */
    public function getWebAssetManager(): WebAssetManager
    {
        return $this->webAssetManager;
    }

    /**
     * Sets the title of the document
     *
     * @param   string  $title  The title to be set
     *
     * @return  self instance of $this to allow chaining
     *
     * @since   1.1.0
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the title of the document.
     *
     * @return  string
     *
     * @since   1.1.0
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
