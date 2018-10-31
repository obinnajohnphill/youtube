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
use App\Repositories\YoutubeRepository;



class SearchVideosController  extends Controller
{
    public $data;

    public function index()
    {
        ## Get the post request data from blade
        $searchItem = Input::post('searchItem') ;
        $num_of_video = Input::post('num_of_video');

        ## Call service to get the youtube API data
        $data = new YoutubeService();
        $search = $data->youtubeData ($searchItem,$num_of_video);

        ## sort the YouTube Videos ready for the laravel blade
        foreach ($search as $result){

           $video  = $result->id->videoId;
           $title  = $result->snippet->title;

           $this->data = $title;

            $results[] = [
                'video'    => $video,
                'title' => $title,
            ];

        }

        ## Return data (video details) to the show blade
        return view('videos.show', compact('results'));


    }

    ## Pass data into the kafka component
    public function passData(){
        return $this->data;
    }

}