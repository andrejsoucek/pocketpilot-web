<?php

declare(strict_types=1);

namespace PP\User;

use Exception;
use GettextTranslator\Gettext;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Context;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Security\Passwords;
use Nette\SmartObject;
use Nette\Utils\DateTime;
use PP\TokenProcessor;

/**
 * @author Andrej Souček
 */
class PasswordReset
{
    use SmartObject;

    private Context $database;

    private Passwords $passwords;

    private Gettext $translator;

    private LinkGenerator $linkGenerator;

    private IMailer $mailer;

    public function __construct(
        Context $database,
        Passwords $passwords,
        Gettext $translator,
        LinkGenerator $linkGenerator,
        IMailer $mailer
    ) {
        $this->database = $database;
        $this->passwords = $passwords;
        $this->translator = $translator;
        $this->linkGenerator = $linkGenerator;
        $this->mailer = $mailer;
    }

    public function isTokenValid(string $token): bool
    {
        if (!TokenProcessor::isTokenValid($token)) {
            return false;
        }

        try {
            $tokenHash = TokenProcessor::calculateTokenHash($token);
            $record = $this->database->table(PasswordResetDatabaseDef::TABLE_NAME)
                ->where(PasswordResetDatabaseDef::COLUMN_TOKEN, $tokenHash)
                ->fetch();
            if (!$record || TokenProcessor::isTokenExpired($record->offsetGet('created'))) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function changePassword(string $token, string $pw): void
    {
        $tokenHash = TokenProcessor::calculateTokenHash($token);
        $row = $this->database->table(PasswordResetDatabaseDef::TABLE_NAME)
            ->where(PasswordResetDatabaseDef::COLUMN_TOKEN, $tokenHash)
            ->fetch();
        if ($row) {
            $userId = $row[PasswordResetDatabaseDef::COLUMN_ID_USER];
            $this->database->table(UserDatabaseDef::TABLE_NAME)
                ->where(UserDatabaseDef::COLUMN_ID, $userId)
                ->update([UserDatabaseDef::COLUMN_PASSWORD_HASH => $this->passwords->hash($pw)]);
            $this->database->table(PasswordResetDatabaseDef::TABLE_NAME)
                ->where(PasswordResetDatabaseDef::COLUMN_ID_USER, $userId)
                ->delete();
        }
    }

    /**
     * @throws SendException
     * @throws InvalidLinkException
     */
    public function sendLinkTo(UserEntry $user): void
    {
        $token = $this->createAndSaveToken($user->getId());
        $link = $this->linkGenerator->link('PasswordRecovery:reset', ['token' => $token]);
        $this->mailer->send($this->createMail($user->getEmail(), $link));
    }

    /**
     * @param int $userId
     * @return string
     * @throws Exception
     */
    private function createAndSaveToken(int $userId): string
    {
        TokenProcessor::generateToken($tokenForLink, $tokenHashForDatabase);
        $this->database->table(PasswordResetDatabaseDef::TABLE_NAME)->insert([
            'token' => $tokenHashForDatabase,
            'user_id' => $userId,
            'created' => new DateTime()
        ]);
        return $tokenForLink;
    }

    private function createMail(string $to, string $link): Message
    {
        $mail = new Message();
        $s1 = $this->translator->translate("Hello");
        $s2 = $this->translator->translate("you have requested a password reset.");
        $s3 = $this->translator->translate("To reset your password please click on the following link");
        $s4 = $this->translator->translate("This e-mail has been sent automatically.");
        $s5 = $this->translator->translate("Do not reply to this e-mail.");
        $mail
            ->setFrom('Pocket Pilot <recovery@pocketpilot.cz>')
            ->addTo($to)
            ->setSubject('Password reset')
            ->setBody(
                "$s1,\n$s2\n"
                . "$s3\n"
                . $link . "\n\n"
                . "$s4\n$s5"
            );
        return $mail;
    }
}
