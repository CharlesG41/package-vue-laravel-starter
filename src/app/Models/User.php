<?php

namespace Cyvian\Src\App\Models;

use Cyvian\Src\app\Classes\EntryType;
use Cyvian\Src\app\Handlers\Action\GetActionByNameFromEntryType;
use Cyvian\Src\app\Handlers\User\CanUserExecuteAction;
use Cyvian\Src\app\Repositories\ActionEntryRoleRepository;
use Cyvian\Src\app\Repositories\ActionEntryTypeRoleRepository;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Cyvian\Src\App\Models\Cyvian\BaseModel;

class User extends BaseModel implements AuthenticatableContract
{
    static protected $entry_type = 'user';

    public function can(string $actionName, EntryType $entryType, ?int $entryId = null): bool
    {
        $canUserExecuteAction = new CanUserExecuteAction(
            new ActionEntryRoleRepository,
            new ActionEntryTypeRoleRepository
        );
        $getActionByNameFromEntryType = new GetActionByNameFromEntryType();

        $action = $getActionByNameFromEntryType->handle($actionName, $entryType);

        return $canUserExecuteAction->handle($this, $action, $entryId);
    }

    public function roleIds(): array
    {
        if (!property_exists($this, 'role_ids')) {
            $this->role_ids = array_map(function ($role) {
                return $role->id;
            }, $this->roles);
        }

        return $this->role_ids;
    }

    /**
     * The column name of the "remember me" token.
     *
     * @var string
     */
    protected $rememberTokenName = 'remember_token';

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        if (!empty($this->getRememberTokenName())) {
            return (string) $this->{$this->getRememberTokenName()};
        }
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        if (!empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->rememberTokenName;
    }
}
