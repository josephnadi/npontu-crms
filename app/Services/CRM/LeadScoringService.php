<?php

namespace App\Services\CRM;

use App\Models\Lead;

class LeadScoringService
{
    /**
     * Calculate the score for a lead without saving.
     */
    public function calculate(Lead $lead): int
    {
        $score = 0;

        // 1. Job Title Significance
        if ($lead->job_title) {
            $titles = [
                'CEO' => 30,
                'Director' => 25,
                'Manager' => 15,
                'VP' => 25,
                'Founder' => 30,
                'Owner' => 30,
            ];

            foreach ($titles as $keyword => $points) {
                if (stripos($lead->job_title, $keyword) !== false) {
                    $score += $points;
                    break;
                }
            }
        }

        // 2. Email Domain (Corporate vs Public)
        if ($lead->email) {
            $publicDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];
            $domain = substr(strrchr($lead->email, "@"), 1);
            
            if ($domain && !in_array($domain, $publicDomains)) {
                $score += 15;
            }
        }

        // 3. Completeness of Profile
        if ($lead->company_name) $score += 10;
        if ($lead->phone) $score += 5;
        if ($lead->source) $score += 5;

        // 4. Source Priority
        $sourceScores = [
            'Referral' => 20,
            'Webinar' => 15,
            'Website' => 10,
            'Direct' => 5,
        ];

        if (isset($sourceScores[$lead->source])) {
            $score += $sourceScores[$lead->source];
        }

        // 5. Status Engagement
        $statusScores = [
            'contacted' => 10,
            'qualified' => 30,
            'converted' => 50,
        ];

        if (isset($statusScores[$lead->status])) {
            $score += $statusScores[$lead->status];
        }

        // 6. Engagements (Behavioral)
        $engagementCount = $lead->engagements()->count();
        $score += min($engagementCount * 5, 50); // 5 points per engagement, max 50

        $avgEngagementScore = $lead->engagements()->avg('score') ?? 0;
        $score += round($avgEngagementScore * 0.5); // Add 50% of avg engagement score

        return min($score, 100); // Cap at 100
    }

    /**
     * Calculate and update the score for a lead.
     */
    public function calculateScore(Lead $lead): int
    {
        $score = $this->calculate($lead);
        $lead->score = $score;
        $lead->saveQuietly();

        return $score;
    }

    /**
     * Recalculate scores for all leads.
     */
    public function recalculateAll(): void
    {
        Lead::chunk(100, function ($leads) {
            foreach ($leads as $lead) {
                $this->calculateScore($lead);
            }
        });
    }
}
