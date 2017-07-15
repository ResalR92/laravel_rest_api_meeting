<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;
use Carbon\Carbon;

class MeetingController extends Controller
{

    public function __construct()
    {
        // $this->middleware('name');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $meeting = [
        //     'title' => 'Title',
        //     'description' => 'Description',
        //     'time' => 'Time',
        //     'user_id' => 'User ID',
        //     'view_meeting' => [
        //         'href' => 'api/v1/meeting/1',
        //         'method' => 'GET'
        //     ]
        // ];
        $meetings = Meeting::all();

        //link to individual meeting
        foreach ($meetings as $meeting) {
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/'.$meeting->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all Meetings',
            'meetings' => $meetings
        ];
        return response()->json($response,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' =>'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        //simpan data meeting ke DB
        $meeting = new Meeting([
            'time' => Carbon::createFromFormat('YmdHie',$time),
            'title' => $title,
            'description' => $description
        ]);

        if($meeting->save()) {
            $meeting->users()->attach($user_id);//add entry to pivot table
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/'.$meeting->id,
                'method' => 'GET'
            ];

            $response = [
                'msg' => 'Meeting created',
                'meeting' => $meeting
            ];

            return response()->json($response,201);
        }

        $response = [
            'msg' => 'Error during creating',
            'meeting' => $meeting
        ];

        return response()->json($response,404);
    }
    //hasil
    // {
    //     "msg": "Meeting created",
    //     "meeting": {
    //         "time": {
    //             "date": "2016-01-15 01:33:00.000000",
    //             "timezone_type": 2,
    //             "timezone": "CET"
    //         },
    //         "title": "Test2",
    //         "description": "This is test2",
    //         "updated_at": "2017-07-15 14:16:21",
    //         "created_at": "2017-07-15 14:16:21",
    //         "id": 3,
    //         "view_meeting": {
    //             "href": "api/v1/meeting/3",
    //             "method": "GET"
    //         }
    //     }
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
            'user_id' => 'User ID',
            'view_meeting' => [
                'href' => 'api/v1/meeting',
                'method' => 'GET'
            ]
        ];

        $response = [
            'msg' => 'List of all Meetings',
            'meetings' => $meeting
        ];
        return response()->json($response,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' =>'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = [
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'user_id' => $user_id,
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method' => 'GET'
            ]
        ];

        $response = [
            'msg' => 'Meeting updated',
            'meeting' => $meeting
        ];

        return response()->json($response,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = [
            'msg' => 'Meeting deleted',
            'create' => [
                'href' => 'api/v1/meeting',
                'method' => 'POST',
                'params' => 'title,description,time'
            ]
        ];

        return response()->json($response,200);
    }
}
