<?php

namespace App\Http\Controllers\API\V1;

use Exception;
use App\Models\Like;
use App\Models\Photo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    private $hidden = ['created_at', 'updated_at'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photo = Photo::get();
        
        if ($photo) {
            // response if success
            return ResponseFormatter::success(
                $photo->makeHidden($this->hidden),
                'Get Data Photo Successfully!'
            );
        } else {
            // response if error
            return ResponseFormatter::error(
                null,
                'Data Photo Not Found!',
                404
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // validation
            $validator = Validator::make( $request->all(),[
                'caption' => 'required|max:255',
                'tag' => 'required|max:255',
                'photo' => 'required|mimes:png,jpg,jpeg,svg|max:1000',
            ]);

            // validation error
            if ($validator->fails()) {
                return ResponseFormatter::error(
                    ['error' => $validator->errors()], 
                    'Upload Photo Fails',
                    401
                );    
            }

            // get file
            $file = $request->file('photo');
            if ($request->file('photo')) {
                $fileName = Str::random(6).'-'.$file->getClientOriginalName();
                $photo = $file->storeAs('assets/photo',$fileName,'public');
            }

            // created photo
            $photo = Photo::create([
                'user_id' => Auth::user()->id,
                'caption' => $request->caption,
                'tag' => $request->tag,
                'photo' => $photo,
            ]);

            // return data
            if ($photo) {
                return ResponseFormatter::success(
                    $photo->makeHidden($this->hidden),
                    'Data Created Successfully!'
                );
            }

            // response if error
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went Wrong!',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Photo::find($id);
        if ($item) {
            // response if success
            return ResponseFormatter::success(
                $item->makeHidden($this->hidden),
                'Get Detail Photo Successfully!'
            );
        } else {
            // response if error
            return ResponseFormatter::error(
                null,
                'Data Photo Not Found!',
                404
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        try {
            // validation
            $validator = Validator::make( $request->all(),[
                'caption' => 'required|max:255',
                'tag' => 'required|max:255',
                'photo' => 'nullable|mimes:png,jpg,jpeg,svg|max:1000',
            ]);

            // validation error
            if ($validator->fails()) {
                return ResponseFormatter::error(
                    ['error' => $validator->errors()], 
                    'Upload Photo Fails!', 
                    401
                );    
            }

            // find photo where id
            $item = Photo::findOrFail($id);
            $data['caption'] = $request->caption;
            $data['tag'] = $request->tag;
            $file = $request->file('photo');

            if($file == "") {
                $data['photo'] = $item->getRawOriginal('photo');
            } else if ($file !== "") {
                File::delete('storage/'. $item->getRawOriginal('photo'));
                $fileName = Str::random(6).'-'.$file->getClientOriginalName();
                $data['photo'] = $file->storeAs('assets/photo',$fileName,'public');
            }

            //  update photo
            $item->update($data);

            if ($item) {
                return ResponseFormatter::success(
                    $item,
                    'Data Updated Successfully!'
                );
            }

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went Wrong!',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // find data by id
            $item = Photo::find($id);

            // delete file
            File::delete('storage/'. $item->getRawOriginal('photo'));

            // deleted
            $item->delete();

            // if response success
            if ($item) {
                return ResponseFormatter::success(
                    'Data Deleted Successfully',
                    'Data Deleted Successfully!'
                );
            }
        } catch (Exception $error) {
            // if error success
            return ResponseFormatter::error([
                'message' => 'Something went Wrong!',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function like(Request $request, $id)
    {
        try {
            $photo = Photo::where('id', $id)->firstOrFail();
            if ($photo) {
                $item = Like::updateOrCreate(['photo_id' => $photo->id], [
                    'user_id' => Auth::user()->id,
                    'photo_id' => $photo->id,
                    'status' => 'like'
                ]);
            }
    
            if ($item) {
                return ResponseFormatter::success(
                    $item->makeHidden($this->hidden),
                    'Data Save Successfully!'
                );
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went Wrong!',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function unlike(Request $request, $id)
    {
        try {
            $photo = Photo::where('id', $id)->firstOrFail();
            if ($photo) {
                $item = Like::updateOrCreate(['photo_id' => $photo->id], [
                    'user_id' => Auth::user()->id,
                    'photo_id' => $photo->id,
                    'status' => 'unlike'
                ]);
            }
    
            if ($item) {
                return ResponseFormatter::success(
                    $item->makeHidden($this->hidden),
                    'Data Save Successfully!'
                );
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went Wrong!',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }
}
