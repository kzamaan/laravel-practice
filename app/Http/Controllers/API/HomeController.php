<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HomeController extends Controller
{
    public function initApp()
    {
        $users = User::latest()->limit(20)->get();
        $tags = Tag::latest()->limit(20)->get();
        return response()->json([
            'success' => true,
            'users' => $users,
            'tags' => $tags
        ], 200);
    }

    public function user($id)
    {
        $user = User::find($id);
        return response()->json([
            'success' => true,
            'user' => $user
        ], 200);
    }

    public function uploadImage(Request $request)
    {
        $png_url = uniqid() . time() . ".jpg";
        $path = "images/" . $png_url;
        $img = file_get_contents($request->image);
        $success = Storage::put($path, $img);
        print $success ? $png_url : 'Unable to save the file.';
    }

    public function uploadDocsFile(Request $request)
    {
        $uploaded_file = [];
        foreach ($request->attachments as $attachment) {
            $type = explode('.', $attachment['name']);
            $file_name = uniqid() . time() . "." . end($type);
            $path = "docs/" . $file_name;
            $base64 = file_get_contents($attachment['base64']);
            Storage::put($path, $base64);
            $uploaded_file[] = $file_name;
        }
        return $uploaded_file;
    }

    public function getAllUsers()
    {
        $users = User::with('roles')->latest()->limit(50)->get();
        return response()->json([
            'success' => true,
            'users' => $users
        ], 200);
    }

    public function testDatabaseTransactions()
    {

        DB::beginTransaction();

        try {
            $category = Category::query()->create([
                'name' => 'Test Category',
                'slug' => 'test-category'
            ]);
            $user = User::query()->create([
                'name' => 'ZAMAN',
                'email' => 'zaman@gmail.com',
                'password' => bcrypt('password')
            ]);

            DB::commit();
            return [$user, $category];
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'Failed',
                'message' => $e->getMessage()
            ]);
        }
    }
}
