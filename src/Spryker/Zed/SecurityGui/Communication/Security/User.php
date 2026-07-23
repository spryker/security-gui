<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SecurityGui\Communication\Security;

use Generated\Shared\Transfer\UserTransfer;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    protected const string SERIALIZATION_KEY_USER_TRANSFER = 'userTransfer';

    protected const string SERIALIZATION_KEY_USERNAME = 'username';

    protected const string SERIALIZATION_KEY_PASSWORD = 'password';

    protected const string SERIALIZATION_KEY_ROLES = 'roles';

    protected const string SERIALIZATION_KEY_STATE_HASH = 'stateHash';

    /**
     * Sessions written before `__serialize()` was introduced used default object serialization,
     * where protected property names are prefixed with "\0*\0".
     */
    protected const string LEGACY_PROTECTED_PROPERTY_PREFIX = "\0*\0";

    /**
     * @var \Generated\Shared\Transfer\UserTransfer
     */
    protected $userTransfer;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var array<string>
     */
    protected $roles = [];

    protected ?string $stateHash = null;

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param array<string> $roles
     */
    public function __construct(UserTransfer $userTransfer, array $roles = [])
    {
        $this->userTransfer = $userTransfer;
        /** @var string $username */
        $username = $userTransfer->requireUsername()->getUsername();
        $this->username = $username;
        $this->password = $userTransfer->getPassword();
        $this->roles = $roles;
        $this->stateHash = $this->computeStateHash($this->password);
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUserIdentifier(): string
    {
        return $this->userTransfer->getUsernameOrFail();
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(SymfonyUserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return $user->getStateHash() === $this->stateHash;
    }

    public function getStateHash(): ?string
    {
        return $this->stateHash;
    }

    public function __serialize(): array
    {
        $userTransferData = $this->userTransfer->modifiedToArray();
        unset($userTransferData[UserTransfer::PASSWORD]);
        $cleanUserTransfer = (new UserTransfer())->fromArray($userTransferData, true);

        return [
            static::SERIALIZATION_KEY_USER_TRANSFER => $cleanUserTransfer,
            static::SERIALIZATION_KEY_USERNAME => $this->username,
            static::SERIALIZATION_KEY_ROLES => $this->roles,
            static::SERIALIZATION_KEY_STATE_HASH => $this->stateHash,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $data = $this->normalizeLegacySessionData($data);

        $this->userTransfer = $data[static::SERIALIZATION_KEY_USER_TRANSFER];
        $this->username = $data[static::SERIALIZATION_KEY_USERNAME];
        $this->roles = $data[static::SERIALIZATION_KEY_ROLES];
        $this->password = null;

        $this->stateHash = $data[static::SERIALIZATION_KEY_STATE_HASH]
            ?? $this->computeStateHash($data[static::SERIALIZATION_KEY_PASSWORD] ?? null);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function normalizeLegacySessionData(array $data): array
    {
        $normalizedData = [];

        foreach ($data as $key => $value) {
            $normalizedData[str_replace(static::LEGACY_PROTECTED_PROPERTY_PREFIX, '', $key)] = $value;
        }

        return $normalizedData;
    }

    public function getUserTransfer(): UserTransfer
    {
        return $this->userTransfer;
    }

    protected function computeStateHash(?string $password): string
    {
        return hash('md5', implode('|', [
            $password ?? '',
            $this->userTransfer->getStatus() ?? '',
            implode(',', $this->roles),
        ]));
    }
}
