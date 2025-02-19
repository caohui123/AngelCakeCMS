<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Event;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * Event policy
 */
class EventPolicy implements BeforePolicyInterface
{
    public function before(?IdentityInterface $user, $resource, $action)
    {
        if ($user->group_id == 1) // is an admin, can do whatever
            return true;
        if (!in_array($user->group_id, [1,2,3,6,9])) // is not allowed to edit
            return false;
    }

    /**
     * Check if $user can add Event
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Event $event
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Event $event)
    {
        return false;
    }

    /**
     * Check if $user can edit Event
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Event $event
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Event $event)
    {
        return false;
    }

    /**
     * Check if $user can delete Event
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Event $event
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Event $event)
    {
        return false;
    }

    /**
     * Check if $user can view Event
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Event $event
     * @return bool
     */
    public function canView(IdentityInterface $user, Event $event)
    {
        return false;
    }
}
