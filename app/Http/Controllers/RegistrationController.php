<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;
use App\User;

class RegistrationController extends Controller
{
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required'
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        $message = [
            'msg' => 'User is already registered for meeting.',
            'user' => $user,
            'meeting' => $meeting,
            'unregistered' => [
                'href' => 'api/v1/meeting/registration/'.$meeting->id,
                'method' => 'DELETE'
            ]
        ];
        //if user already registered
        if($meeting->users()->where('users.id',$user->id)->first()) {
            return response()->json($message, 404);
        }

        $user->meetings()->attach($meeting);

        $response = [
            'msg' => 'User registered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregistered' => [
                'href' => 'api/v1/meeting/registration/'.$meeting->id,
                'method' => 'DELETE'
            ]
        ];

        return response()->json($response,201);
    }
    //hasil
    // {
    //     "msg": "User registered for meeting",
    //     "meeting": {
    //         "id": 3,
    //         "created_at": "2017-07-15 14:16:21",
    //         "updated_at": "2017-07-15 14:16:21",
    //         "time": "2016-01-15 01:33:00",
    //         "title": "Test2",
    //         "description": "This is test2"
    //     },
    //     "user": {
    //         "id": 1,
    //         "name": "Resal Ramdahadi",
    //         "email": "resalramdahadi92@gmail.com",
    //         "created_at": "2017-07-15 13:47:12",
    //         "updated_at": "2017-07-15 13:47:12"
    //     },
    //     "unregistered": {
    //         "href": "api/v1/meeting/registration/3",
    //         "method": "DELETE"
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
        $meeting = [
            'title' => 'Title',
            'description' => 'Description',
            'time' => 'Time',
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method' => 'GET'
            ]
        ];

        $user = [
            'name' => 'Name'
        ];

        $response = [
            'msg' => 'User unregistered for meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregistered' => [
                'href' => 'api/v1/meeting/registration',
                'method' => 'POST',
                'params' => 'user_id,meeting_id'
            ]
        ];

        return response()->json($response,200);
    }
}
