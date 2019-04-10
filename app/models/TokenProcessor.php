<?php

namespace PP;

use DateInterval;
use DateTime;
use Exception;

/**
 * @author Martin Stoeckli - www.martinstoeckli.ch/php
 * @author Andrej Souček
 * @copyright Martin Stoeckli 2013, this code may be freely used in every
 * type of project, it is provided without warranties of any kind.
 * @version 2.1
 */
class TokenProcessor {

	/**
	 * The token must be strong enough, so we can store its unsalted hash
	 * with a fast algorithm (sha256) in the database. Do not go beneath 20.
	 *
	 * @var int
	 */
	private static $tokenLength = 32;

	/**
	 * Tokens expires after 8 hours
	 * @var string
	 */
	private static $expiryInterval = 'PT8H';

	/**
	 * Generates a new token, that can be used to build a password reset link.
	 * Like a password, the token itself should not be stored in the database,
	 * rather store its hash-value.
	 * @param string $tokenForLink This variable receives the new generated
	 *   random token. It will be used to build the password reset link.
	 * @param string $tokenHashForDatabase This variable receives the
	 *   hash-value of the token. It can be safely stored in the database
	 *   and is always 64 characters in length.
	 * @throws Exception
	 */
	public static function generateToken(&$tokenForLink, &$tokenHashForDatabase) : void {
		$tokenForLink = self::generateRandomBase62String(self::$tokenLength);
		$tokenHashForDatabase = self::calculateTokenHash($tokenForLink);
	}

	/**
	 * Calculates the hash of a token. This hash can be searched for in the
	 * database, after the user pressed a link containing a token.
	 * @param string $token Token that was extracted from the clicked link.
	 * @throws Exception It the token is invalid.
	 * @return string Returns the searchable hash-value of the token.
	 */
	public static function calculateTokenHash($token) : string {
		if (strlen($token) < 20) {
			throw new Exception('The token is too short and therefore too weak');
		}
		return hash('sha256', $token, false);
	}

	/**
	 * Makes a formal test whether a token is valid.
	 * @param string $token Token to test.
	 * @return bool Returns true if the token is formally valid,
	 *   otherwise false.
	 */
	public static function isTokenValid($token) : bool {
		// Valid tokens must be of a certain length and must contain
		// only following characters 0..9, a..z, A..Z.
		return !is_null($token) && (self::$tokenLength == strlen($token)) && ctype_alnum($token);
	}

	/**
	 * Checks whether the token is expired.
	 * @param DateTime $creationDate The moment the token was created.
	 * @return bool Returns true if the token is expired, otherwise false.
	 * @throws Exception
	 */
	public static function isTokenExpired(DateTime $creationDate) :bool {
		$now = new DateTime();

		$expiryDate = clone $creationDate;
		$validFor = new DateInterval(self::$expiryInterval);
		$expiryDate->add($validFor);

		return $now > $expiryDate;
	}

	/**
	 * Generates a random string of a given length, using the random source of
	 * the operating system. The string contains only safe characters of this
	 * alphabet: 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz
	 * @param int $length Number of characters the string should have.
	 * @return string A random base62 encoded string.
	 * @throws Exception
	 */
	public static function generateRandomBase62String($length) : string {
		$result = '';
		$remainingLength = $length;
		do {
			// We take advantage of the fast base64 encoding
			$binaryLength = (int)($remainingLength * 3 / 4 + 1);
			$binaryString = random_bytes($binaryLength);
			$base64String = base64_encode($binaryString);

			// Remove invalid characters
			$base62String = str_replace(array('+', '/', '='), '', $base64String);
			$result .= $base62String;

			// If too many characters have been removed, we repeat the procedure
			$remainingLength = $length - strlen($result);
		} while ($remainingLength > 0);
		return substr($result, 0, $length);
	}
}