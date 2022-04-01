<?php

namespace Msgframework\Lib\Application;

use Msgframework\Lib\Identity\IdentityFactoryInterface;
use Msgframework\Lib\Identity\IdentityInterface;

/**
 * Trait for application classes which are identity (user) aware
 *
 * @since  1.0.0
 */
trait IdentityAwareTrait
{
	/**
	 * The application identity object.
	 *
	 * @var    IdentityInterface
	 * @since  1.0.0
	 */
	protected IdentityInterface $identity;

	/**
	 * UserFactoryInterface
	 *
	 * @var    IdentityFactoryInterface
	 * @since  1.0.0
	 */
	private IdentityFactoryInterface $identityFactory;

	/**
	 * Get the application identity.
	 *
	 * @return  IdentityInterface
	 *
	 * @since   1.0.0
	 */
	public function getIdentity(): IdentityInterface
	{
		return $this->identity;
	}

    /**
     * Allows the application to load a custom or default identity.
     *
     * @param IdentityInterface|null $identity An optional identity object. If omitted, a null user object is created.
     *
     * @return  $this
     *
     * @since   1.0.0
     */
	public function loadIdentity(?IdentityInterface $identity = null): self
	{
		$this->identity = $identity ?: $this->identityFactory->loadIdentityById(0);

		return $this;
	}

	/**
	 * Set the user factory to use.
	 *
	 * @param   IdentityFactoryInterface  $identityFactory  The user factory to use
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function setIdentityFactory(IdentityFactoryInterface $identityFactory): void
	{
		$this->identityFactory = $identityFactory;
	}
}
