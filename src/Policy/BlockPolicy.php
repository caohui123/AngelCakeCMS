<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Block;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;

/**
 * Block policy
 */
class BlockPolicy implements BeforePolicyInterface
{
    public function before(?IdentityInterface $user, $resource, $action)
    {
        if ($user->group_id == 1) // is an admin, can do whatever
            return true;
        if (!in_array($user->group_id, [1,2,3,6,9])) // is not allowed to edit
            return false;
    }

    /**
     * Check if $user can add Block
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Block $block
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Block $block)
    {
        return false;
    }

    /**
     * Check if $user can edit Block
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Block $block
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Block $block)
    {
        return false;
    }

    /**
     * Check if $user can delete Block
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Block $block
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Block $block)
    {
        return false;
    }

    /**
     * Check if $user can view Block
     *
     * @param \Authorization\IdentityInterface $user The user.
     * @param \App\Model\Entity\Block $block
     * @return bool
     */
    public function canView(IdentityInterface $user, Block $block)
    {
        return false;
    }
}
