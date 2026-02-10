<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $setting = UserSetting::firstOrCreate(
            ['user_id' => $request->user()->id],
            []
        );

        return Inertia::render('Settings/Index', [
            'setting' => $setting->only([
                'locale','timezone','currency','theme',
                'notif_in_app','notif_email','notif_due_reminders'
            ]),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|string|in:en',
            'timezone' => 'required|string',
            'currency' => 'required|string|size:3',
            'theme' => 'required|string|in:light,dark',
            'notif_in_app' => 'required|boolean',
            'notif_email' => 'required|boolean',
            'notif_due_reminders' => 'required|boolean',
        ]);

        UserSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return redirect()->route('settings.index')->with('success', 'Settings saved.');
    }
}
