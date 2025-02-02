<?php

declare(strict_types=1);

namespace In2code\Lux\Domain\Cache;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception as ExceptionDbal;
use In2code\Lux\Exception\EnvironmentException;
use In2code\Lux\Utility\DatabaseUtility;
use In2code\Lux\Utility\StringUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Authentication\IpLocker;
use TYPO3\CMS\Core\Authentication\Mfa\MfaRequiredException;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Session\Backend\DatabaseSessionBackend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BackendSessionFaker
{
    const TABLE_BACKENDUSERS = 'be_users';
    const TABLE_BACKENDUSERSESSION = 'be_sessions';

    protected DatabaseSessionBackend $dbSessionBackend;

    public function __construct()
    {
        $this->dbSessionBackend = GeneralUtility::makeInstance(DatabaseSessionBackend::class);
    }

    public function __destruct()
    {
        $this->removeBackendSession();
    }

    /**
     * @return void
     * @throws EnvironmentException
     * @throws Exception
     * @throws ExceptionDbal
     * @throws MfaRequiredException
     */
    public function fake(): void
    {
        $this->createBackendSession();
        $this->createBackendUserGlobalObject();
    }

    /**
     * @return void
     * @throws EnvironmentException
     * @throws Exception
     * @throws ExceptionDbal
     */
    protected function createBackendSession(): void
    {
        $sessionData = ['formProtectionSessionToken' => $this->getFormProtectionSessionToken()];
        $properties = [
            'ses_id' => $this->dbSessionBackend->hash($this->getSessionIdentifier()),
            'ses_iplock' => IpLocker::DISABLED_LOCK_VALUE,
            'ses_userid' => $this->getBackendUserAdminIdentifier(),
            'ses_tstamp' => time(),
            'ses_data' => serialize($sessionData),
        ];

        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_BACKENDUSERSESSION);
        $queryBuilder->insert(self::TABLE_BACKENDUSERSESSION)->values($properties)->executeStatement();
    }

    protected function removeBackendSession(): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_BACKENDUSERSESSION);
        $queryBuilder
            ->delete(self::TABLE_BACKENDUSERSESSION)
            ->where(
                $queryBuilder->expr()->eq(
                    'ses_id',
                    $queryBuilder->createNamedParameter($this->dbSessionBackend->hash($this->getSessionIdentifier()))
                )
            )
            ->executeStatement();
    }

    /**
     * We do need to create a backend user authentication object to create a valid link to a backend module
     * from CLI context
     *
     * @return void
     * @throws EnvironmentException
     * @throws Exception
     * @throws ExceptionDbal
     * @throws MfaRequiredException
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function createBackendUserGlobalObject(): void
    {
        $request = GeneralUtility::makeInstance(ServerRequest::class);
        $newRequest = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieSecure'] = 0;
        $GLOBALS['BE_USER'] = GeneralUtility::makeInstance(BackendUserAuthentication::class);
        $GLOBALS['BE_USER']->start($newRequest);
        $GLOBALS['BE_USER']->setBeUserByUid($this->getBackendUserAdminIdentifier());
        $GLOBALS['BE_USER']->backendCheckLogin();
        $GLOBALS['BE_USER']->setSessionData('formProtectionSessionToken', $this->getFormProtectionSessionToken());
    }

    /**
     * @return int
     * @throws EnvironmentException
     * @throws Exception
     * @throws ExceptionDbal
     */
    protected function getBackendUserAdminIdentifier(): int
    {
        static $identifier = 0;
        if ($identifier === 0) {
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_BACKENDUSERS);
            $identifier = (int)$queryBuilder
                ->select('uid')
                ->from(self::TABLE_BACKENDUSERS)
                ->where('admin=1')
                ->executeQuery()
                ->fetchOne();
            if ($identifier === 0) {
                throw new EnvironmentException('No administration backend user found', 1645125690);
            }
        }
        return $identifier;
    }

    /**
     * Used for be_typo_user cookie value
     *
     * @return string
     */
    public function getSessionIdentifier(): string
    {
        static $sessionIdentifier = '';
        if ($sessionIdentifier === '') {
            $sessionIdentifier = StringUtility::getRandomString(32, false);
        }
        return $sessionIdentifier;
    }

    protected function getFormProtectionSessionToken(): string
    {
        static $sessionToken = '';
        if ($sessionToken === '') {
            $sessionToken = StringUtility::getRandomString(64, false);
        }
        return $sessionToken;
    }
}
