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
use Botble\Media\RvMedia;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Botble\Member\Models\Member;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Category;
use Botble\Slug\Models\Slug;
use FriendsOfBotble\Comment\Models\Comment;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use Botble\Base\Models\MetaBox;




class WpImportController extends BaseController
{

    public function __construct(protected RvMedia $rvMedia)
    {
    }
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

public function importPostsWithoutMeta()
{
    try {
        DB::connection('mysql2')
            ->table('frntn_posts')
            ->where('post_type', 'post')
            ->where('post_date_gmt', '>', '2025-02-01 00:00:00')
            ->orderBy('ID')
            ->chunk(100, function ($wpPosts) {
                $postsToInsert = [];

                foreach ($wpPosts as $wpPost) {
                    $postsToInsert[] = [
                        'id' => $wpPost->ID,
                        'name' => $wpPost->post_title,
                        'description' => Str::limit(strip_tags($wpPost->post_content), 100, '...'),
                        'content' => $wpPost->post_content,
                        'status' => $wpPost->post_status === 'publish' ? 'published' : 'draft',
                        'author_id' => 1,
                        'author_type' => 'Botble\ACL\Models\User',
                        'published_at' => $wpPost->post_date,
                        'created_at' => $wpPost->post_date_gmt,
                        'updated_at' => $wpPost->post_date_gmt,
                        'plain_slug' => $wpPost->post_name
                    ];

                    $metaToInsert[] = [
                        'meta_key' => 'allow_comments',
                        'meta_value' => $wpPost->comment_status === 'open' ? json_encode(['1']) : json_encode(['0']),
                        'reference_id' => $wpPost->ID,
                        'reference_type' => 'Botble\Blog\Models\Post',
                        'created_at' => $wpPost->post_date_gmt,
                        'updated_at' => $wpPost->post_date_gmt
                    ];
                }

                if (!empty($postsToInsert)) {
                    $existingPostIds = Post::whereIn('id', array_column($postsToInsert, 'id'))->pluck('id')->toArray();
                    $postsToInsert = array_filter($postsToInsert, function ($post) use ($existingPostIds) {
                        return !in_array($post['id'], $existingPostIds);
                    });
                    Post::insert($postsToInsert);
                }

                if (!empty($metaToInsert)) {
                    $existingMeta = MetaBox::whereIn('reference_id', array_column($metaToInsert, 'reference_id'))
                        ->whereIn('meta_key', array_column($metaToInsert, 'meta_key'))
                        ->get(['reference_id', 'meta_key'])
                        ->toArray();
                    $metaToInsert = array_filter($metaToInsert, function ($meta) use ($existingMeta) {
                        foreach ($existingMeta as $existing) {
                            if ($existing['reference_id'] == $meta['reference_id'] && $existing['meta_key'] == $meta['meta_key']) {
                                return false;
                            }
                        }
                        return true;
                    });
                    MetaBox::insert($metaToInsert);
                }
            });

        return response()->json(['message' => 'Posts imported successfully without meta!'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing posts.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function importMetaForPosts()
{
    try {
        $posts = Post::whereNull('image')->get(); // Fetch all posts from the Laravel database
        foreach ($posts as $post) {
            $meta = DB::connection('mysql2')
                ->table('frntn_postmeta')
                ->where('post_id', $post->id)
                ->get()
                ->pluck('meta_value', 'meta_key');


            // Process featured image
            $featuredImageId = $meta['_thumbnail_id'] ?? null;


            //Put image process on job and call it 
            $featuredImageUrl = $featuredImageId
                ? DB::connection('mysql2')
                    ->table('frntn_posts')
                    ->where('ID', $featuredImageId)
                    ->value('guid')
                : null;

            $storedImagePath = null;
            if ($featuredImageUrl) {
                // Generate a temporary file path
                $tempPath = storage_path('app/temp_images/' . uniqid() . '.jpg');
                $tempDir = dirname($tempPath);
            
                // Ensure the temp directory exists
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
            
                // Download the image
                try {
                    file_put_contents($tempPath, file_get_contents($featuredImageUrl));
                } catch (\Exception $e) {
                    throw new \Exception('Failed to download image: ' . $e->getMessage());
                }
            
                // Sanitize filename for upload
                $pathInfo = pathinfo($featuredImageUrl);
                $sanitizedFileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $pathInfo['filename']);
                $newFilePath = "{$tempDir}/{$sanitizedFileName}.{$pathInfo['extension']}";
            
                // Rename the file locally
                rename($tempPath, $newFilePath);
            
                // Upload the sanitized file
                $uploadResult = $this->rvMedia->uploadFromPath($newFilePath, 0, 'posts');
                unlink($newFilePath); // Clean up the temporary file after uploading
            
                if (!empty($uploadResult['error'])) {
                    throw new \Exception($uploadResult['message']);
                }
            
                $storedImagePath = $uploadResult['data']->url ?? null;
            }
            
            // $post->update and finish job
            
            

            $post->update([
                'image' => $storedImagePath,
                'format_type' => 'post',
            ]);
        }

        return response()->json(['message' => 'Post meta imported successfully!'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing post meta.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function importSlugsForPosts()
{
    try {
        $posts = Post::all(); // Fetch all posts from the Laravel database
        $slugsToInsert = [];

        foreach ($posts as $post) {
            $slugsToInsert[] = [
                'key' => $post->plain_slug, // Use the existing slug from the model
                'reference_id' => $post->id,
                'reference_type' => 'Botble\Blog\Models\Post',
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        }

        if (!empty($slugsToInsert)) {
            Slug::insert($slugsToInsert); // Bulk insert the slugs
        }

        return response()->json(['message' => 'Slugs imported successfully!'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error importing slugs.',
            'error' => $e->getMessage(),
        ], 500);
    }
}





public function deleteTodayImportedPosts()
{
    try {
        // Get today's date
        $today = Carbon::today();

        // Fetch post IDs created today
        $postIds = DB::table('posts')
            ->whereDate('created_at','>', '2025-01-01 00:00:00')
            ->pluck('id');

        if ($postIds->isEmpty()) {
            return response()->json(['message' => 'No posts found for today.'], 404);
        }

        // Delete associated slugs
        DB::table('slugs')
            ->whereIn('reference_id', $postIds)
            ->where('reference_type', 'Botble\Blog\Models\Post')
            ->delete();

        // Delete the posts
        DB::table('posts')
            ->whereIn('id', $postIds)
            ->delete();

        return response()->json([
            'message' => 'Posts and slugs created today have been deleted successfully.',
            'deleted_post_ids' => $postIds,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error deleting posts and slugs.',
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
   

    public function generateSEO($postId=556093)
    {
        try {
            // Retrieve the post
            $post = DB::table('posts')->where('id', $postId)->first();
    
            if (!$post) {
                return response()->json(['message' => 'Post not found.'], 404);
            }

            // Generate SEO content using ChatGPT API

            $apiKey = env('GPT_API'); // Replace with your actual API key

            $apiUrl = 'https://api.openai.com/v1/chat/completions';
    
            $prompt = "Generate SEO metadata for the following post:
            Title: {$post->name}
            Content: {$post->content}
            Provide keywords and a meta description.";
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'model' => 'text-embedding-3-small',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an SEO assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 1024,
            ]);
            $data = $response->json();
            dd($data);
            if (!isset($data['choices'][0]['message']['content'])) {
                return response()->json(['message' => 'Failed to generate SEO content.'], 500);
            }
    
            $seoContent = $data['choices'][0]['message']['content'];
    
            // Parse ChatGPT response for keywords and meta description
            $keywords = []; // Extract keywords from response (custom parsing logic may be needed)
            preg_match('/Keywords: (.*)/i', $seoContent, $keywordMatches);
            if (!empty($keywordMatches[1])) {
                $keywords = explode(',', $keywordMatches[1]);
            }
    
            $metaDescription = '';
            preg_match('/Meta Description: (.*)/i', $seoContent, $metaDescriptionMatches);
            if (!empty($metaDescriptionMatches[1])) {
                $metaDescription = trim($metaDescriptionMatches[1]);
            }
    
            // Save SEO data to the `meta_boxes` table
            DB::table('meta_boxes')->insert([
                [
                    'meta_key' => 'vig_seo_keywords',
                    'meta_value' => json_encode(['keywords' => $keywords]),
                    'reference_id' => $postId,
                    'reference_type' => 'Botble\Blog\Models\Post',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'meta_key' => 'seo_meta',
                    'meta_value' => json_encode(['index' => 'index', 'description' => $metaDescription]),
                    'reference_id' => $postId,
                    'reference_type' => 'Botble\Blog\Models\Post',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
    
            return response()->json([
                'message' => 'SEO metadata generated and saved successfully.',
                'seo_content' => $seoContent,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error generating SEO metadata.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




public static function importComment($postId)
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
            if (Comment::where('id', $comment->comment_ID)->exists()) {
                continue; // Skip if the comment already exists
            }

            // Create and save the comment
            $newComment = new Comment();
            $newComment->fill([
                'id' => $comment->comment_ID, // Match WordPress comment_ID
                'reference_id' => $postId, // Post ID reference
                'reference_type' => 'Botble\Blog\Models\Post', // Assuming the comments belong to blog posts
                'name' => $comment->comment_author,
                'email' => $comment->comment_author_email,
                'content' => strip_tags($comment->comment_content), // Sanitize content
                'status' => $comment->comment_approved === '1' ? CommentStatus::APPROVED : CommentStatus::PENDING,
                'author_id' => $comment->user_id =='0'? null :Member::where('email',$comment->comment_author_email)->first()->id, // If available, update accordingly
                'author_type' => 'Botble\Member\Models\Member', // If available, update accordingly
                'reply_to' => $comment->comment_parent ?: null, // Parent comment reference
                'ip_address' => $comment->comment_author_IP,
                'user_agent' => null, // WordPress does not store user-agent by default
                'created_at' => $comment->comment_date,
                'updated_at' => $comment->comment_date_gmt,
            ]);

            $newComment->save();
        }

        return response()->json([
            'message' => 'Comments imported successfully!',
        ], 200);

    } catch (\Exception $e) {
        dd([
            'message' => 'Error importing comments.',
            'error' => $e->getMessage(),
        ]);
    }
}

    

    
}
