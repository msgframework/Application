<?php

namespace Msgframework\Lib\Document;

use Psr\Link\EvolvableLinkProviderInterface;

/**
 * Preload Manager Interface
 *
 * @since  1.1.0
 */
interface PreloadManagerInterface
{
	/**
	 * Get the link provider
	 *
	 * @return  EvolvableLinkProviderInterface
	 *
	 * @since  1.0.0
	 */
	public function getLinkProvider(): EvolvableLinkProviderInterface;

	/**
	 * Set the link provider
	 *
	 * @param   EvolvableLinkProviderInterface  $linkProvider  The link provider
	 *
	 * @return  $this
	 *
	 * @since  1.0.0
	 */
	public function setLinkProvider(EvolvableLinkProviderInterface $linkProvider);

	/**
	 * Preloads a resource.
	 *
	 * @param   string  $uri         A public path
	 * @param   array   $attributes  The attributes of this link (e.g. "array('as' => true)", "array('crossorigin' => 'use-credentials')")
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function preload(string $uri, array $attributes = []);

	/**
	 * Resolves a resource origin as early as possible.
	 *
	 * @param   string  $uri         A public path
	 * @param   array   $attributes  The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function dnsPrefetch(string $uri, array $attributes = []);

	/**
	 * Initiates an early connection to a resource (DNS resolution, TCP handshake, TLS negotiation).
	 *
	 * @param   string  $uri         A public path
	 * @param   array   $attributes  The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function preconnect(string $uri, array $attributes = []);

	/**
	 * Indicates to the client that it should prefetch this resource.
	 *
	 * @param   string  $uri         A public path
	 * @param   array   $attributes  The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function prefetch(string $uri, array $attributes = []);

	/**
	 * Indicates to the client that it should prerender this resource.
	 *
	 * @param   string  $uri         A public path
	 * @param   array   $attributes  The attributes of this link (e.g. "array('as' => true)", "array('pr' => 0.5)")
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function prerender(string $uri, array $attributes = []);
}
