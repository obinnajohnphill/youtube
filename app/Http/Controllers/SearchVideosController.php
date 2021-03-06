<?php
/**
 * Created by PhpStorm.
 * User: obinnajohnphill
 * Date: 29/10/18
 * Time: 10:34
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Input;
use App\Services\YoutubeService;
use App\Events\VideoSaved;


class SearchVideosController  extends Controller
{
    public $data;
    public $save_video;
    public $duplicate;

    public function index()
    {
        ## Get the post request data from blade
        $searchItem = Input::post('searchItem') ;
        $num_of_video = Input::post('num_of_video');
        $save = Input::post('save_video');

        ## Call service to get the youtube API data
        $data = new YoutubeService();
        $search[] = $data->youtubeData ($searchItem,$num_of_video,$save);



        ## Check if the save video option is included in search result array
        if (isset($search[0][1]) AND $search[0][1] == "duplicate"){
            $this->duplicate = true;
            $search = $search[0][0];
        }else{
            if ( $save  == "yes"){
                $this->save_video = true;
                $search = $search[0];
            }
            $search = $search[0];
        }

        ## sort the YouTube Videos ready for the laravel blade
        foreach ($search as $result){
                $video = $result->id->videoId;
                $title = $result->snippet->title;

            $results[] = [
                'video'    => $video,
                'title' => $title,
            ];

        }


        ## Return data (video details) to the show blade
        if ($this->duplicate == true){
            return view('videos.show', compact('results'))->with('unsuccessMsg','Unable to save video(s).There exist duplicate video(s) in the database.');
        }else if($this->save_video  == null) {
            return view('videos.show', compact('results'));
        }
        else{
            event(new VideoSaved ());
            return view('videos.show', compact('results'))->with('successMsg','These video(s) have been successfully save into the database.');
        }
    }


    public function getAll()
    {
        $view = Input::post('viewAll');
        $data = new YoutubeService();
        $results = $data->viewAll($view);
        return view('videos.showall', compact('results'))->with('successMsg','This is a list of all the videos in database.');

    }


}