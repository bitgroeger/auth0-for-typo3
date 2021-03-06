<?php
declare(strict_types=1);
namespace Bitmotion\Auth0\Domain\Repository;

use Bitmotion\Auth0\Domain\Transfer\EmAuth0Configuration;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\EnvironmentService;

class UserRepository implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var ExpressionBuilder
     */
    protected $expressionBuilder;

    /**
     * @var string
     */
    protected $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        $this->expressionBuilder = $this->queryBuilder->expr();
    }

    /**
     * Gets an user by given auth0 user ID.
     */
    public function getUserByAuth0Id(string $auth0UserId): array
    {
        $this->queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $this->expressionBuilder->eq(
                    'auth0_user_id',
                    $this->queryBuilder->createNamedParameter($auth0UserId)
                )
            );
        $this->logger->debug(sprintf('[%s] Executed SELECT query: %s', $this->tableName, $this->queryBuilder->getSQL()));
        $user = $this->queryBuilder->execute()->fetch();

        return ($user !== false) ? $user : [];
    }

    /**
     * Removes DeletedRestriction and / or HiddenRestriction from QueryBuilder.
     * Depends on extension configuration.
     */
    public function removeRestrictions()
    {
        $environmentService = GeneralUtility::makeInstance(EnvironmentService::class);
        $emConfiguration = GeneralUtility::makeInstance(EmAuth0Configuration::class);

        if ($environmentService->isEnvironmentInFrontendMode()) {
            $this->removeFrontendRestrictions($emConfiguration);
        } elseif ($environmentService->isEnvironmentInBackendMode()) {
            $this->removeBackendRestrictions($emConfiguration);
        } else {
            $this->logger->notice('Undefined environment');
        }
    }

    protected function removeFrontendRestrictions(EmAuth0Configuration $emConfiguration)
    {
        if ($emConfiguration->getReactivateDeletedFrontendUsers()) {
            $this->removeDeletedRestriction();
        }
        if ($emConfiguration->getReactivateDisabledFrontendUsers()) {
            $this->removeHiddenRestriction();
        }
    }

    protected function removeBackendRestrictions(EmAuth0Configuration $emConfiguration)
    {
        if ($emConfiguration->getReactivateDeletedBackendUsers()) {
            $this->removeDeletedRestriction();
        }
        if ($emConfiguration->getReactivateDisabledBackendUsers()) {
            $this->removeHiddenRestriction();
        }
    }

    protected function removeHiddenRestriction()
    {
        $this->queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $this->logger->debug('Removed HiddenRestriction.');
    }

    protected function removeDeletedRestriction()
    {
        $this->queryBuilder->getRestrictions()->removeByType(DeletedRestriction::class);
        $this->logger->debug('Removed DeletedRestriction.');
    }

    /**
     * Get not deleted users.
     * The restriction is not available in some cases.
     */
    public function addDeletedRestriction()
    {
        $this->queryBuilder->andWhere(
            $this->expressionBuilder->eq(
                'deleted',
                $this->queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            )
        );
    }

    /**
     * Get only active users.
     * The restriction is not available in some cases.
     */
    public function addDisabledRestriction()
    {
        $this->queryBuilder->andWhere(
            $this->expressionBuilder->eq(
                'disable',
                $this->queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            )
        );
    }

    /**
     * Adds ordering to a select query.
     */
    public function setOrdering(string $fieldName, string $order = 'ASC')
    {
        $this->queryBuilder->orderBy($fieldName, $order);
    }

    /**
     * Adds max results to a select query.
     */
    public function setMaxResults(int $maxResults)
    {
        $this->queryBuilder->setMaxResults($maxResults);
    }

    /**
     * Updates a backend or frontend user by given uid.
     */
    public function updateUserByUid(array $sets, int $uid)
    {
        $this->resolveSets($sets);
        $this->queryBuilder->where(
            $this->expressionBuilder->eq('uid', $this->queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
        );
        $this->updateUser();
    }

    /**
     * Updates a backend or frontend user by given auth0_user_id.
     */
    public function updateUserByAuth0Id(array $sets, string $auth0Id)
    {
        $this->resolveSets($sets);
        $this->queryBuilder->where(
            $this->expressionBuilder->eq('auth0_user_id', $this->queryBuilder->createNamedParameter($auth0Id))
        );
        $this->updateUser();
    }

    /**
     * Resolves the set array.
     */
    protected function resolveSets(array $sets)
    {
        foreach ($sets as $key => $value) {
            $this->logger->debug(sprintf('Set property "%s" to: "%s"', $key, $value));
            $this->queryBuilder->set($key, $value);
        }
    }

    /**
     * Executes the update query.
     */
    protected function updateUser()
    {
        $this->queryBuilder->update($this->tableName);
        $this->logger->debug(sprintf('[%s] Executed UPDATE query: %s', $this->tableName, $this->queryBuilder->getSQL()));
        $this->queryBuilder->execute();
    }

    /**
     * Inserts a backend or frontend user by given value array.
     */
    public function insertUser(array $values)
    {
        $this->queryBuilder->insert($this->tableName)->values($values);
        $this->logger->debug(sprintf('[%s] Executed INSERT query: %s', $this->tableName, $this->queryBuilder->getSQL()));
        $this->queryBuilder->execute();
    }
}
