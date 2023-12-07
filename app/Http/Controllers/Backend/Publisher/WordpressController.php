<?php

namespace App\Http\Controllers\Backend\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\AiBlogWizard;
use App\Models\AiBlogWizardArticle;
use App\Models\AiBlogWizardArticleLog;
use App\Models\Language;
use App\Models\SubscriptionPackage;
use App\Traits\PopulateWizardData;
use Illuminate\Http\JsonResponse;

class WordpressController extends Controller
{
    #publish blog
    public function publishBlog(Request $request){
        $article = AiBlogWizardArticle::where('id', $request->id)->where('created_by', auth()->user()->id)->first();
    
        if (!$article) {
            flash(localize('Blog not found for you'));
            return redirect()->back();
        }

        $client = new Client();
        $headers = [
            'Authorization' => 'Basic am9lc2hlcGFyZDpxUVJDIHR1bjMgSHhscCBUQVZuIDY5eTEgblhYaw==',
            'Content-Type' => 'application/json',
        ];

        $base_url = 'https://dev.graylingagency.co/sites/grayling/wp-json/wp/v2';
        $endpoint = '/posts';

        $data = [
            'content' => $article->value,
            'title' => $article->title,
            'status' => 'publish',
            'author' => 2,
            'categories' => [62,71],
            'tags' => [23,66],
            'excerpt' => 'This is the excerpt',
        ];
        // status  One of: publish, future, draft, pending, private
        // author JSON data type: integer
        // cateogry array (do I need to know the IDs or can I create new ones?)
        // tags array (do I need to know the IDs or can I create new ones?)

        $response = $client->post($base_url . $endpoint, [
            'headers' => $headers,
            'json' => $data,
        ]);

        $body = $response->getBody()->getContents();
        echo '<pre>';
        echo response()->json(json_decode($body, true));
        echo '</pre>';
    }
}
