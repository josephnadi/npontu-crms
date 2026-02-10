<?php

namespace App\Observers;

use App\Models\Engagement;

class EngagementObserver
{
    /**
     * Handle the Engagement "created" event.
     */
    public function created(Engagement $engagement): void
    {
        $this->updateParentScore($engagement);
    }

    /**
     * Handle the Engagement "updated" event.
     */
    public function updated(Engagement $engagement): void
    {
        $this->updateParentScore($engagement);
    }

    /**
     * Handle the Engagement "deleted" event.
     */
    public function deleted(Engagement $engagement): void
    {
        $this->updateParentScore($engagement);
    }

    /**
     * Update the score of the engageable parent.
     */
    protected function updateParentScore(Engagement $engagement): void
    {
        $parent = $engagement->engageable;
        
        if (!$parent) return;

        if ($parent instanceof \App\Models\Lead) {
            app(\App\Services\CRM\LeadScoringService::class)->calculateScore($parent);
        } elseif (isset($parent->score)) {
            $avgScore = $parent->engagements()->avg('score') ?? 0;
            $parent->update(['score' => round($avgScore)]);
        }
    }

    /**
     * Handle the Engagement "restored" event.
     */
    public function restored(Engagement $engagement): void
    {
        //
    }

    /**
     * Handle the Engagement "force deleted" event.
     */
    public function forceDeleted(Engagement $engagement): void
    {
        //
    }
}
