<?php

namespace App\Http\Resources\Api\Auth;

use App\Models\LandingPageContent;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $landing = LandingPageContent::first();
        return [
            'login_welcome_title' => $this->login_welcome_title,
            'login_welcome_description' => $this->login_welcome_description,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'linkedin' => $this->linkedin,
            'contact_email' => $this->contact_email,
            'contact_number' => $this->contact_number,
            'show_contact_number' => $this->show_contact_number ?? 0,
            'show_social_media' => $this->show_social_media ?? 0,
            'term_and_condition' => $this->term_and_condition,
            'privacy_policy' => $this->privacy_policy,
            'about_us' => $this->about_us,
            'income_note' => $this->income_note,
            'expense_note' => $this->expense_note,
            'budget_note' => $this->budget_note,
            'item_note' => $this->item_note,
            'goal_note' => $this->goal_note,
            'flash_screen_text' => $this->flash_screen_text,
            'income_icon_path' => $this->income_icon_path,
            'expense_icon_path' => $this->expense_icon_path,
            'budget_icon_path' => $this->budget_icon_path,
            'item_icon_path' => $this->item_icon_path,
            'goal_icon_path' => $this->goal_icon_path,
            'landing_faqs' => json_decode($landing->faqs ?? [])
        ];
    }
}
