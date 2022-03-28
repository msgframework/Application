<?php

namespace Msgframework\Lib\Application;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Trait which helps implementing `Msgframework\Lib\Application\SessionAwareWebApplicationInterface` in a web application class.
 *
 * @since  1.0.0
 */
trait SessionAwareTrait
{
	/**
	 * The application session object.
	 *
	 * @var    SessionInterface
     *
	 * @since  1.0.0
	 */
	protected SessionInterface $session;


	/**
	 * Method to get the application session object.
	 *
	 * @return  SessionInterface  The session object
	 *
	 * @since   1.0.0
	 */

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

	/**
	 * Sets the session for the application to use, if required.
	 *
	 * @param   SessionInterface  $session  A session object.
	 *
	 * @return  $this
	 *
	 * @since   1.0.0
	 */
	public function setSession(SessionInterface $session): self
	{
		$this->session = $session;

		return $this;
	}

    /**
     * Checks for a form token in the request.
     *
     * @param string $method The request method in which to look for the token key.
     *
     * @return  boolean
     *
     * @throws \Exception
     * @since   1.0.0
     */
	public function checkToken(string $method = 'post'): bool
	{
		$token = $this->getFormToken();
        $request = $this->factory->getRequest();

		// Support a token sent via the X-CSRF-Token header, then fall back to a token in the request
		$requestToken = $request->server->getAlnum(
			'HTTP_X_CSRF_TOKEN',
            $request->$method->getAlnum($token, '')
		);

		if (!$requestToken)
		{
			return false;
		}

		return $this->hasToken($token);
	}

    /**
     * Method to determine a hash for anti-spoofing variable names
     *
     * @param boolean $forceNew If true, force a new token to be created
     *
     * @return  string  Hashed var name
     *
     * @throws \Exception
     * @since   1.0.0
     */
	public function getFormToken(bool $forceNew = false): string
	{
        $session = $this->getSession();
        // Ensure the session token exists and create it if necessary
        if (!$session->has('session.token') || $forceNew)
        {
            $session->set('session.token', $this->createToken());
        }

		return $session->get('session.token');
	}

    /**
     * Create a token string
     *
     * @return  string
     *
     * @throws \Exception
     * @since   1.0.0
     */
    protected function createToken(): string
    {
        /*
         * We are returning a 32 character string.
         * The bin2hex() function will double the length of the hexadecimal value returned by random_bytes(),
         * so generate the token from a 16 byte random value
         */
        return bin2hex(random_bytes(16));
    }

    /**
     * Check if the session has the given token.
     *
     * @param string $token        Hashed token to be verified
     * @param boolean $forceExpire  If true, expires the session
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function hasToken(string $token, bool $forceExpire = true): bool
    {
        $result = $this->getSession()->get('session.token') === $token;

        if (!$result && $forceExpire)
        {
            $this->getSession()->invalidate();
        }

        return $result;
    }
}
