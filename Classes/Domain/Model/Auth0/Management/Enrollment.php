<?php
declare(strict_types=1);
namespace Bitmotion\Auth0\Domain\Model\Auth0\Management;

class Enrollment
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';

    const AUTH_AUTHENTICATOR = 'authenticator';
    const AUTH_GUARDIAN = 'guardian';
    const AUTH_SMS = 'sms';

    /**
     * Enrollment generated id
     *
     * @var string
     */
    protected $id;

    /**
     * Enrollment status
     *
     * @var string
     */
    protected $status;

    /**
     * Enrollment type
     *
     * @var string
     */
    protected $type;

    /**
     * Enrollment name (usually phone number)
     *
     * @var string
     */
    protected $name;

    /**
     * Device identifier (usually phone identifier)
     *
     * @var string
     */
    protected $identity;

    /**
     * Phone number
     *
     * @var string
     */
    protected $phoneNumber;

    /**
     * Enrollment type
     *
     * @var string
     */
    protected $authMethod;

    /**
     * Enrollment date
     *
     * @var string
     */
    protected $enrolledAt;

    /**
     * Last authentication
     *
     * @var string
     */
    protected $lastAuth;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    public function setIdentity(string $identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getAuthMethod()
    {
        return $this->authMethod;
    }

    public function setAuthMethod(string $authMethod)
    {
        $this->authMethod = $authMethod;
    }

    /**
     * @return string
     */
    public function getEnrolledAt()
    {
        return $this->enrolledAt;
    }

    public function setEnrolledAt(string $enrolledAt)
    {
        $this->enrolledAt = $enrolledAt;
    }

    /**
     * @return string
     */
    public function getLastAuth()
    {
        return $this->lastAuth;
    }

    public function setLastAuth(string $lastAuth)
    {
        $this->lastAuth = $lastAuth;
    }
}
