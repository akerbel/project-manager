<?php

namespace App\Policies;

use App\Models\Situation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SituationPolicy
{
    use HandlesAuthorization;

    /**
     * Check if project belongs to user.
     *
     * @param Situation $situation
     * @param User $user
     *
     * @return bool
     */
    protected function isSituationBelongsToUser(Situation $situation, User $user): bool {
        return $user->id === $situation->project->user_id;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Situation  $situation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Situation $situation)
    {
        return $this->isSituationBelongsToUser($situation, $user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Situation  $situation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Situation $situation)
    {
        return $this->isSituationBelongsToUser($situation, $user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Situation  $situation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Situation $situation)
    {
        return $this->isSituationBelongsToUser($situation, $user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Situation  $situation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Situation $situation)
    {
        return $this->isSituationBelongsToUser($situation, $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Situation  $situation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Situation $situation)
    {
        return $this->isSituationBelongsToUser($situation, $user);
    }
}
