<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeRoleRequest;
use App\Http\Requests\UpdateNameEmailUserRequest;
use App\Http\Requests\UpdatePasswordUserRequest;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->can('view all profil')) {
            return response()->json([
                'status' => true,
                'message' => 'User retrieved successfully!',
                'data' => new UserResource($user),
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully!',
            'data' => UserResource::collection(User::all()),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateNameEmail(UpdateNameEmailUserRequest $request, $id)
    {
        
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $userauth = Auth::user();
        if (!$userauth->can('edit all profil') && $userauth->id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You dont have permission to Update this user'
            ], Response::HTTP_FORBIDDEN);
        }

        $user->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => "User updated successfully!",
            'data' => new UserResource($user)
        ], Response::HTTP_OK);
    }

    public function updatePassword(UpdatePasswordUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $userauth = Auth::user();

        if (!$userauth->can('edit all profil') && !$userauth->can('edit all profil') && $userauth->id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You dont have permission to Update this user'
            ], Response::HTTP_FORBIDDEN);
        }
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => "User updated successfully!",
            'data' => new UserResource($user)
        ], Response::HTTP_OK);
    }


    public function changeRole(ChangeRoleRequest $request,$id){

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $user->syncRoles($request->validated());

        return response()->json([
            'status' => true,
            'message' => "Role updated successfully!",
            'data' => new UserResource($user)
        ], Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $userauth = Auth::user();
        if (!$userauth->can('delete all profil') && $userauth->id != $user->id) {
           return response()->json([
                'status' => false,
                'message' => "You don't have permission to delete this user!",
            ], Response::HTTP_FORBIDDEN);
        }
        $user->delete();

         return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ], Response::HTTP_OK);
    }
}
