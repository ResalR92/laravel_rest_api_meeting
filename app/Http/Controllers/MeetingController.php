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
        $meeting = Meeting::with('users')->where('id',$id)->firstOrFail();//eager loading

        $meeting->view_meeting = [
            'href' => 'api/v1/meeting',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Meeting Information',
            'meetings' => $meeting
        ];
        return response()->json($response,200);
    }
    //hasil
    // {
    //     "msg": "Meeting Information",
    //     "meetings": {
    //         "id": 3,
    //         "created_at": "2017-07-15 14:16:21",
    //         "updated_at": "2017-07-15 14:16:21",
    //         "time": "2016-01-15 01:33:00",
    //         "title": "Test2",
    //         "description": "This is test2",
    //         "view_meeting": {
    //             "href": "api/v1/meeting",
    //             "method": "GET"
    //         },
    //         "users": [
    //             {
    //                 "id": 2,
    //                 "name": "Guest",
    //                 "email": "guest@mail.com",
    //                 "created_at": "2017-07-15 14:19:17",
    //                 "updated_at": "2017-07-15 14:19:17",
    //                 "pivot": {
    //                     "meeting_id": 3,
    //                     "user_id": 2
    //                 }
    //             }
    //         ]
    //     }
    // }

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

        $meeting = Meeting::with('users')->findOrFail($id);
        //jika user bukan pemilik meeting
        if(!$meeting->users()->where('user_id',$user_id)->first()) {
            return response()->json(['msg'=>'User not registered for meeting, update not successful'],401);
        }

        $meeting->time = Carbon::createFromFormat('YmdHie',$time);
        $meeting->title = $title;
        $meeting->description = $description;

        if(!$meeting->update()) {
            return response()->json(['msg'=>'Error during updating'], 404);
        }

        $meeting->view_meeting = [
            'href' => 'api/v1/meeting/'.$meeting->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Meeting updated',
            'meeting' => $meeting
        ];

        return response()->json($response,200);
    }
    //hasil
    // {
    //     "msg": "Meeting updated",
    //     "meeting": {
    //         "id": 5,
    //         "created_at": "2017-07-15 14:18:53",
    //         "updated_at": "2017-07-15 14:37:44",
    //         "time": {
    //             "date": "2016-01-15 01:33:00.000000",
    //             "timezone_type": 2,
    //             "timezone": "CET"
    //         },
    //         "title": "Test4 UPDATED",
    //         "description": "This is test4",
    //         "view_meeting": {
    //             "href": "api/v1/meeting/5",
    //             "method": "GET"
    //         },
    //         "users": [
    //             {
    //                 "id": 1,
    //                 "name": "Resal Ramdahadi",
    //                 "email": "resalramdahadi92@gmail.com",
    //                 "created_at": "2017-07-15 13:47:12",
    //                 "updated_at": "2017-07-15 13:47:12",
    //                 "pivot": {
    //                     "meeting_id": 5,
    //                     "user_id": 1
    //                 }
    //             }
    //         ]
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users = $meeting->users;
        $meeting->users()->detach();//clear relation->pivot table

        if(!$meeting->delete()) {
            foreach ($users as $user) {
                $meeting->users()->attach($user);//mengembalikan relasi jika tidak menghapus
            }
            return response()->json(['msg'=>'Deletion failed'],404);
        }

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
    //hasil
    // {
    //     "msg": "Meeting deleted",
    //     "create": {
    //         "href": "api/v1/meeting",
    //         "method": "POST",
    //         "params": "title,description,time"
    //     }
    // }
}
