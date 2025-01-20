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
use Botble\Blog\Models\Category;
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

public function singlePost($postId=554825)
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
            $primaryCategoryId = $postMeta['_yoast_wpseo_primary_category'] ?? null;
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
                'created_at'=>$wpPost->post_date,
            ]);

            $post->save();
            if($primaryCategoryId){
                $this->category($primaryCategoryId,$post->id);
            }
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
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing post.',
                'error' => $e->getMessage(),
            ], 500);
        }
}


private function category($primaryCategoryId,$post_id){
    try{


        $term = DB::connection('mysql2')
            ->table('frntn_terms')
            ->where('term_id',$primaryCategoryId)
            ->first();
        $slug= new Slug();
        $existingCategory = Category::where('name', $term->name)->first();
        if (!$existingCategory) {
            // Create and save the category
            $category = new Category();
            $category->fill([
                'id'=>$term_id,
                'name' => $term->name,
                'description' => null, // Set to null or map from another source if available
                'parent_id' => 0, // Default to no parent (adjust logic for hierarchical categories)
                'icon' => null, // Default to null or provide logic for icons
                'is_featured' => 0, // Default to not featured
                'order' => $term->term_order ?? 0, // Use `term_order` or default to 0
                'is_default' => 0, // Default to not default
                'status' => 'published', // Default to published
                'author_id' => 1, // Set default author or map as needed
                'author_type' => 'Botble\ACL\Models\User', // Default author type
            ]);
            $category->save();
            $slug->fill([
                'key'=>$term->name,
                'reference_id'=>$primaryCategoryId,
                'reference_type'=>'Botble\Blog\Models\Category'
            ]);
            $slug->save();
        }
        DB::table('post_categories')->insert([
            'post_id' => $post_id,
            'category_id' => $primaryCategoryId,
        ]);


    }catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing category.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
    private function importComment($postId)
    {
        try {
            // Fetch all comments related to the specific post ID
            $comments = DB::connection('mysql2')
                ->table('frntn_comments')
                ->where('comment_post_ID', $postId)
                ->get();

            if ($comments->isEmpty()) {
                return response()->json(['message' => 'No comments found for the post.'], 404);
            }

            foreach ($comments as $comment) {
                // Check if the comment already exists in your comments table
                $existingComment = Comment::where('id', $comment->comment_ID)->first();
                if ($existingComment) {
                    continue; // Skip if the comment already exists
                }

                // Create and save the comment
                $newComment = new Comment();
                $newComment->fill([
                    'id' => $comment->comment_ID, // Match WordPress comment_ID
                    'post_id' => $postId,
                    'author' => $comment->comment_author,
                    'author_email' => $comment->comment_author_email,
                    'author_url' => $comment->comment_author_url,
                    'author_ip' => $comment->comment_author_IP,
                    'content' => $comment->comment_content,
                    'status' => $comment->comment_approved === '1' ? 'approved' : 'pending',
                    'parent_id' => $comment->comment_parent,
                    'created_at' => $comment->comment_date,
                    'updated_at' => $comment->comment_date_gmt,
                ]);

                $newComment->save();
            }

            return response()->json([
                'message' => 'Comments imported successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing comments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



}
