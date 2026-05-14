<?php

namespace Modules\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontendController extends Controller
{

    public function HomePage()
    {
        return Inertia::render('HomePage/Index', [
            'event' => ['title' => 'Home'],
        ]);
    }

    public function TransactionLogPage()
    {
        return Inertia::render('TransactionLog/Index', [
            'event' => ['title' => 'Transaction Log'],
        ]);
    }

    public function IncomePage()
    {
        return Inertia::render('Income/Index', [
            'event' => ['title' => 'Income'],
        ]);
    }

    public function ExpensePage()
    {
        return Inertia::render('Expense/Index', [
            'event' => ['title' => 'Expense'],
        ]);
    }

    public function BalanceSheetPage()
    {
        return Inertia::render('BalanceSheet/Index', [
            'event' => ['title' => 'Balance Sheet'],
        ]);
    }

    public function MemberDetailPage($id)
    {
        return Inertia::render('MemberDetail/Index', [
            'event'    => ['title' => 'Member Detail'],
            'memberId' => (int) $id,
        ]);
    }
    public function VocabularyPage()
    {
        return Inertia::render('Words/Index', [
            'event' => [
                'title' => 'Words Page',
            ]
        ]);
    }
    public function VocabularyTestPage()
    {
        return Inertia::render('VocabularyTest/Index', [
            'event' => [
                'title' => 'Words Page',
            ]
        ]);
    }
    public function StoryPage()
    {
        return Inertia::render('Story/Index', [
            'event' => [
                'title' => 'Story Page',
            ]
        ]);
    }
    public function StoryTestPage()
    {
        return Inertia::render('Story/StoryTest', [
            'event' => [
                'title' => 'Story Test Page',
            ]
        ]);
    }
    public function ProfilePage()
    {
        return Inertia::render('Profile/Index', [
            'event' => [
                'title' => 'Profile Page',
            ]
        ]);
    }
}
