<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage; // Correct import for Storage
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;

use Illuminate\Support\Facades\DB;
use Botble\Member\Models\Member;
use Botble\Blog\Models\Post;
use Botble\Slug\Models\Slug;




class WpImportController extends BaseController
{

    public function users()
{
    try {
        // Fetch the user_login as 'Amirezalla' from the frntn_users table
        $users = DB::connection('mysql2')
            ->table('frntn_users')
            ->get();

        if (!$users) {
            return response()->json(['message' => 'Users not found in frntn_users table.'], 404);
        }

        foreach ($users as $user) {
            // Check if a user with the same email already exists in the `members` table
            $existingMember = Member::where('email', $user->user_email)->first();
            if ($existingMember) {
                continue; // Skip this user if email already exists
            }

            // Determine the confirmation timestamp
            $confirmed_at = !$user->user_activation_key ? null : '2024-09-24 13:42:15';

            // Import the user data into the Member model
            $member = new Member();
            $member->setRawAttributes([
                'id' => $user->ID,
                'first_name' => $user->user_nicename, // Assuming `user_nicename` is the first name
                'last_name' => '',                   // No last name field in the source table
                'email' => $user->user_email,
                'password' => $user->user_pass,      // Use raw WordPress-hashed password
                'avatar_id' => null,                 // Set null or default value
                'confirmed_at' => $confirmed_at,
                'dob' => null,                       // Set null or default value
                'phone' => null,                     // Set null or default value
                'description' => null,               // Set null or default value
                'gender' => null,                    // Set null or default value
            ]);

            $member->save();
        }

        return response()->json([
            'message' => 'Users imported successfully!',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing users.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function singlePost($postId=554810)
{
    try {
        // Fetch the post from the WordPress `frntn_posts` table
        $wpPost = DB::connection('mysql2')
            ->table('frntn_posts')
            ->where('ID', $postId)
            ->where('post_type', 'post') // Ensure it's a post
            ->first();

        if (!$wpPost) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        // Fetch post metadata
        $postMeta = DB::connection('mysql2')
            ->table('frntn_postmeta')
            ->where('post_id', $postId)
            ->pluck('meta_value', 'meta_key');

        // Initialize variables for image handling
        $featuredImageId = $postMeta['_thumbnail_id'] ?? null;
        $featuredImageUrl = null;
        $storedImagePath = null;

        // Retrieve the featured image URL using `_thumbnail_id`
        if ($featuredImageId) {
            $featuredImageUrl = DB::connection('mysql2')
                ->table('frntn_posts')
                ->where('ID', $featuredImageId)
                ->value('guid');

            if ($featuredImageUrl) {
                // Download the image and save it to storage
                $imageContents = Http::get($featuredImageUrl)->body();
                $imageExtension = pathinfo($featuredImageUrl, PATHINFO_EXTENSION);
                $imageName = 'featured_' . $featuredImageId . '.' . $imageExtension;

                // Save the image in the storage/app/public/posts directory
                $storedImagePath = 'posts/' . $imageName;
                Storage::disk('public')->put($storedImagePath, $imageContents);
            }
        }

        // Save the post data to your `Post` model
        $post = new Post();
        $post->fill([
            'name' => $wpPost->post_title,
            'description' => $wpPost->post_excerpt,
            'content' => $wpPost->post_content,
            'image' => $storedImagePath, // Save the local storage path of the image
            'is_featured' => 0,
            'format_type' => $postMeta['mvp_post_template'] ?? null,
            'status' => $wpPost->post_status === 'publish' ? 'published' : 'draft',
            'author_id' => 1,
            'author_type' => 'Botble\ACL\Models\User',
            'published_at' => $wpPost->post_date,
        ]);

        $post->save();
        $slug= new Slug();
        $slug->fill([
            'key'=>$wpPost->post_name,
            'reference_id'=>$post->id,
            'reference_type'=>'Botble\Blog\Models\Post'
        ]);
        $slug->save();

        return response()->json([
            'message' => 'Post imported successfully!',
            'post' => $post,
            'image_path' => $storedImagePath,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing post.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



}