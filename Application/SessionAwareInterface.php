<?php

namespace Msgframework\Lib\Application;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Application sub-interface defining a web application class which supports sessions
 *
 * @since  1.0.0
 */
interface SessionAwareInterface extends WebApplicationInterface
{
	/**
	 * Method to get the application session object.
	 *
	 * @return  SessionInterface  The session object
	 *
	 * @since   1.0.0
	 */
	public function getSession(): SessionInterface;

	/**
	 * Sets the session for the application to use, if required.
	 *
	 * @param   SessionInterface  $session  A session object.
	 *
	 * @return  $this
	 *
	 * @since   1.0.0
	 */
	public function setSession(SessionInterface $session): self;

    /**
     * Starts the session storage.
     *
     * @return $this
     *
     * @since 1.0.0
     */
    public function sessionStart(): self;

	/**
	 * Checks for a form token in the request.
	 *
	 * @param string $method  The request method in which to look for the token key.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function checkToken(string $method = 'post'): bool;

	/**
	 * Method to determine a hash for anti-spoofing variable names
	 *
	 * @param boolean $forceNew  If true, force a new token to be created
	 *
	 * @return  string  Hashed var name
	 *
	 * @since   1.0.0
	 */
	public function getFormToken(bool $forceNew = false): string;
}
