<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Models\Favorites;
use App\Models\Users;
use App\Models\Developers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller
{
    /**
     * get all favorites
     *
     * @return object
     */
    public function list()
    {
        return Favorites::all();
    }

    /**
     * get one favorite by id
     *
     * @param [int] $id
     * @return object
     */
    public function item($id)
    {
        return Favorites::whereId($id)->first();
    }

    /**
     * create new
     *
     * @param Request $request
     * @return object
     */
    public function create(Request $request)
    {
        try {
            $favorite = new Favorites();
            $favorite->developer_id = $request->developer_id;
            $favorite->recruiter_id = $request->recruiter_id;

            if ($favorite->save()) {
                return response()->json(['status' => 'success', 'message' => 'Favorite created successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * update a specific favorite with id
     *
     * @param Request $request
     * @param [int] $id
     * @return object
     */
    public function update(Request $request, $id)
    {
        try {
            $favorite = Favorites::findOrFail($id);
            $favorite->developer_id = $request->developer_id;
            $favorite->recruiter_id = $request->recruiter_id;

            if ($favorite->save()) {
                return response()->json(['status' => 'success', 'message' => 'Favorite updated successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * delete a favorite using id
     *
     * @param [int] $id
     * @return object
     */
    public function delete($id)
    {
        try {
            $favorite = Favorites::findOrFail($id);

            if ($favorite->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Favorite deleted successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    /**
     * Function that will retrieve all favorites from one user profile, using their id
     *
     * @param [int] $id
     * @return objects
     */
    public function getAllFromOneUser($id)
    {
        $favs= Favorites::where('recruiter_user_id', '=', $id)->get();

        $favUsers = [];
        $favoriteUsers = $favs->map(function($fav) {
            $devUserId = $fav->developer_user_id;
            //return $devUserId;
            $favUsers['UserId'] = $devUserId;

            $favoriteProfile = Users::join('developers', 'users.dev_id', '=', 'developers.id')
            ->where('users.id', '=', $devUserId)
            ->first();
            $favUsers['UserData'] = $favoriteProfile;
            return $favUsers;
        });

    return response()->json(['status' => 'success', 'favoritesDetails' => $favs , 'favoriteUsersData' => $favoriteUsers]);
    }


    /**
     * Function that will retrieve one complete profile marked as favorite by one user, using their id
     *
     * @param Request $request
     * @return objects
     */
    public function getOneFromOneUser(Request $request)
    {
        $devUserId = $request->devId;
        $recrutUserId = $request->recrutId;

        $favoriteProfile = Favorites::join('users', 'favorites.developer_user_id', '=', 'users.id')
        ->where('recruiter_user_id', '=', $recrutUserId)
        ->where('developer_user_id', '=', $devUserId)
        ->join('developers', 'users.dev_id', '=', 'developers.id')
        ->first();

/*        $favorite = Favorites::where('developer_user_id', '=', $devUserId)
            ->where('recruiter_user_id', '=', $recrutUserId)
            ->first();
        $favorite->users;
        return $favorite;
        $user = Users::find($devUserId)->developers;
        return $user;
        //return $user;
        //$user->load('developers');
        $dev = Developers::find($user);
        //return $dev;
        $dev->load('users');
        return $dev;
        $fav = new Favorites();
        $fav->developers();
        return $fav;
       /* $fav = Favorites::where('recruiter_user_id', '=', $recrutId)
            ->where('developer_user_id', '=', $devId)
            ->get();
        //$favs = $fav->developers();
        //return $fav;
        return $fav->developers;

        $favoritesProfile = Favorites::join('users', 'favorites.developer_user_id', '=', 'users.id')
        ->where('recruiter_user_id', '=', $recrutUserId)
        ->where('developer_user_id', '=', $devUserId)
        ->join('developers', 'users.dev_id', '=', 'developers.id')
        ->first();
        return $favoritesProfile;


        $favId = Favorites::where('developer_user_id', '=', $devUserId)
        ->where('recruiter_user_id', '=', $recrutUserId)
        ->get('id');
*/
        return response()->json(['status' => 'success', 'userId' => $devUserId, 'favoriteUserDetails' => $favoriteProfile]);
    }


    /**
     * Add a new favorite to a recruiter profile, using said recruiter id and developer user id
     *
     * @param Request $request
     * @return object
     */
    public function AddNewToProfile(Request $request)
    {
        try {
            $favorite = new Favorites();
            $favorite->developer_user_id = $request->devUserId;
            $favorite->recruiter_user_id = $request->recrutUserId;

            if ($favorite->save()) {
                return response()->json(['status' => 'success', 'message' => 'Favorite created successfully', 'newFavorite' => $favorite]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
