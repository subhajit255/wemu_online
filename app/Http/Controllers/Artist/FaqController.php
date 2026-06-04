<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends BaseController
{
    public function index(Request $request)
    {
        $query = Faq::latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $faqs = $query->get();
        return view('artist.faq.index', compact('faqs'));
    }

    public function add(Request $request)
    {
        $id = $request->id ?? NULL;
        
        if (!empty($id)) {
            $message = 'FAQ updated successfully.';
            $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string'
            ]);
        } else {
            $message = 'FAQ created successfully.';
            $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string'
            ]);
        }

        $postData = [
            'question' => $request->question,
            'answer' => $request->answer,
        ];
        
        if (empty($id)) {
            $postData['is_active'] = 1;
        }

        Faq::updateOrCreate(['id' => $id], $postData);

        return $this->responseJson(true, 200, $message);
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return $this->responseJson(true, 200, 'FAQ deleted successfully.');
    }
}
