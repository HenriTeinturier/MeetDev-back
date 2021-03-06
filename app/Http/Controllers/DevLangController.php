<?php

namespace App\Http\Controllers;

use App\Models\Dev_lang;
use Illuminate\Http\Request;

class DevLangController extends Controller
{
    /**
     * Get all developers languages list
     *
     * @return void
     */
    public function list(){
        return Dev_lang::all();
    }

    /**
     * get developers language by id
     *
     * @param [int] $id
     * @return void
     */
    public function item($id){
        return Dev_lang::whereId($id)->first();
    }

    /**
     * Create new developers language
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request) {
        try {
            $dev_lang = new Dev_lang();
            $dev_lang->language_id = $request->language_id;
            $dev_lang->developer_id	 = $request->developer_id;

            if ($dev_lang->save()) {
                return response()->json(['status' => 'success', 'message' => 'Dev_lang created successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a singledevelopers language
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function update(Request $request, $id) {
        try {
            $dev_lang = Dev_lang::findOrFail($id);
            $dev_lang->language_id = $request->language_id;
            $dev_lang->developer_id	 = $request->developer_id;

            if ($dev_lang->save()) {
                return response()->json(['status' => 'success', 'message' => 'Dev_lang updated successfully']);
            }
        } catch(\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a single developers language
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id) {
        try {
            $dev_lang = Dev_lang::findOrFail($id);

            if($dev_lang->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Dev_lang deleted successfully']);
            }
        }catch(\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
