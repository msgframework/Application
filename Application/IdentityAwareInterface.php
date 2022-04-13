<?php

namespace Msgframework\Lib\Application;

use Msgframework\Lib\Identity\IdentityFactoryInterface;
use Msgframework\Lib\Identity\IdentityInterface;

interface IdentityAwareInterface
{

    /**
     * Get the application identity.
     *
     * @return  IdentityInterface
     *
     * @since   1.0.0
     */
    public function getIdentity(): IdentityInterface;

    /**
     * Allows the application to load a custom or default identity.
     *
     * @param IdentityInterface|null $identity An optional identity object. If omitted, a null user object is created.
     *
     * @return  $this
     *
     * @since   1.0.0
     */
    public function loadIdentity(?IdentityInterface $identity = null): self;

    /**
     * Set the user factory to use.
     *
     * @param   IdentityFactoryInterface  $identityFactory  The user factory to use
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function setIdentityFactory(IdentityFactoryInterface $identityFactory): void;
}