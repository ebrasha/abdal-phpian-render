<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : laravel-usage.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-04 14:06:18
 * Description  : Laravel-specific usage examples for Abdal Phpian Render package
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

use Abdal\PhpianRender\PhpianRender;

/**
 * ============================================================================
 * LARAVEL USAGE EXAMPLES
 * ============================================================================
 * 
 * This file demonstrates various ways to use Abdal Phpian Render in Laravel
 * applications. These examples cover common Laravel patterns and features.
 */

// ============================================================================
// 1. BASIC USAGE IN CONTROLLERS
// ============================================================================

/**
 * Example Controller Method
 * 
 * In your Laravel controller, you can use PhpianRender directly
 */
class PostController extends Controller
{
    public function show(Post $post)
    {
        $renderer = new PhpianRender();
        
        // Process post title
        $processedTitle = $renderer->process($post->title);
        
        // Process post content with full options
        $processedContent = $renderer->process($post->content, [
            'reshape' => true,
            'bidi' => true,
            'convertNumbers' => true,
            'numberLocale' => 'persian',
            'preserveDiacritics' => true,
            'clean' => true,
        ]);
        
        return view('posts.show', [
            'post' => $post,
            'title' => $processedTitle,
            'content' => $processedContent,
        ]);
    }
}

// ============================================================================
// 2. USING STATIC METHODS (RECOMMENDED FOR LARAVEL)
// ============================================================================

/**
 * Static methods are more convenient in Laravel as they don't require
 * instantiation. Use them in controllers, models, or anywhere else.
 */
class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        
        // Process article titles using static method
        $articles->transform(function ($article) {
            $article->processed_title = PhpianRender::processStatic($article->title);
            return $article;
        });
        
        return view('articles.index', compact('articles'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);
        
        // Process before saving
        $processedTitle = PhpianRender::processStatic($validated['title']);
        $processedContent = PhpianRender::processStatic($validated['content'], [
            'convertNumbers' => true,
            'numberLocale' => 'persian',
        ]);
        
        $article = Article::create([
            'title' => $processedTitle,
            'content' => $processedContent,
        ]);
        
        return redirect()->route('articles.show', $article);
    }
}

// ============================================================================
// 3. MODEL ACCESSORS AND MUTATORS
// ============================================================================

/**
 * Using PhpianRender in Eloquent Model Accessors
 * Automatically process text when retrieving from database
 */
class Post extends Model
{
    /**
     * Accessor: Process title when retrieving
     */
    public function getTitleAttribute($value)
    {
        return PhpianRender::processStatic($value, [
            'reshape' => true,
            'bidi' => true,
        ]);
    }
    
    /**
     * Accessor: Process content with full options
     */
    public function getContentAttribute($value)
    {
        return PhpianRender::processStatic($value, [
            'reshape' => true,
            'bidi' => true,
            'convertNumbers' => true,
            'numberLocale' => 'persian',
            'preserveDiacritics' => true,
        ]);
    }
    
    /**
     * Mutator: Store original text, process on retrieval
     * (Alternative approach - store processed text)
     */
    public function setTitleAttribute($value)
    {
        // Store original text
        $this->attributes['title'] = $value;
        
        // Or store processed text
        // $this->attributes['title'] = PhpianRender::processStatic($value);
    }
}

// ============================================================================
// 4. BLADE DIRECTIVES (CUSTOM BLADE DIRECTIVES)
// ============================================================================

/**
 * Register custom Blade directives in AppServiceProvider
 * 
 * In app/Providers/AppServiceProvider.php:
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Custom Blade directive for processing Persian text
        Blade::directive('persian', function ($expression) {
            return "<?php echo \Abdal\PhpianRender\PhpianRender::processStatic($expression); ?>";
        });
        
        // Custom Blade directive with options
        Blade::directive('persianProcess', function ($expression) {
            return "<?php echo \Abdal\PhpianRender\PhpianRender::processStatic($expression); ?>";
        });
        
        // Custom Blade directive for reshaping only
        Blade::directive('reshape', function ($expression) {
            return "<?php echo \Abdal\PhpianRender\PhpianRender::reshapeStatic($expression); ?>";
        });
        
        // Custom Blade directive for number conversion
        Blade::directive('persianNumbers', function ($expression) {
            return "<?php echo \Abdal\PhpianRender\PhpianRender::convertNumbersStatic($expression, 'persian'); ?>";
        });
    }
}

/**
 * Usage in Blade templates:
 * 
 * {{-- Basic usage --}}
 * @persian($post->title)
 * 
 * {{-- With processing --}}
 * <div>@persian($post->content)</div>
 * 
 * {{-- Reshape only --}}
 * @reshape($text)
 * 
 * {{-- Convert numbers --}}
 * @persianNumbers('عدد 123 است')
 */

// ============================================================================
// 5. HELPER FUNCTIONS
// ============================================================================

/**
 * Create helper functions in app/helpers.php
 * Then add to composer.json autoload:
 * "files": ["app/helpers.php"]
 */
if (!function_exists('persian')) {
    /**
     * Process Persian text with default options
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    function persian(string $text, array $options = []): string
    {
        return PhpianRender::processStatic($text, $options);
    }
}

if (!function_exists('persian_reshape')) {
    /**
     * Reshape Persian text only
     *
     * @param string $text
     * @return string
     */
    function persian_reshape(string $text): string
    {
        return PhpianRender::reshapeStatic($text);
    }
}

if (!function_exists('persian_numbers')) {
    /**
     * Convert numbers to Persian
     *
     * @param string $text
     * @return string
     */
    function persian_numbers(string $text): string
    {
        return PhpianRender::convertNumbersStatic($text, 'persian');
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if text is RTL
     *
     * @param string $text
     * @return bool
     */
    function is_rtl(string $text): bool
    {
        return PhpianRender::isRTLStatic($text);
    }
}

/**
 * Usage in controllers and views:
 * 
 * $processed = persian($text);
 * $reshaped = persian_reshape($text);
 * $withNumbers = persian_numbers('عدد 123');
 * $rtl = is_rtl($text);
 */

// ============================================================================
// 6. SERVICE PROVIDER (DEDICATED SERVICE PROVIDER)
// ============================================================================

/**
 * Create a dedicated Service Provider for PhpianRender
 * php artisan make:provider PhpianRenderServiceProvider
 * 
 * app/Providers/PhpianRenderServiceProvider.php
 */
class PhpianRenderServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register()
    {
        // Bind singleton instance
        $this->app->singleton(PhpianRender::class, function ($app) {
            return new PhpianRender();
        });
        
        // Or bind with alias
        $this->app->alias(PhpianRender::class, 'phpian.render');
    }
    
    /**
     * Bootstrap services
     */
    public function boot()
    {
        // Register Blade directives
        Blade::directive('persian', function ($expression) {
            return "<?php echo app('phpian.render')->process($expression); ?>";
        });
    }
}

/**
 * Usage after registering service provider:
 * 
 * $renderer = app(PhpianRender::class);
 * $processed = $renderer->process($text);
 * 
 * // Or using alias
 * $renderer = app('phpian.render');
 * $processed = $renderer->process($text);
 */

// ============================================================================
// 7. FORM REQUEST VALIDATION
// ============================================================================

/**
 * Process text in Form Request after validation
 */
class StorePostRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }
    
    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation()
    {
        // Optional: Clean text before validation
        if ($this->has('title')) {
            $this->merge([
                'title' => PhpianRender::processStatic($this->title, ['clean' => true]),
            ]);
        }
    }
    
    /**
     * Get validated data with processed text
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Process text after validation
        if (isset($validated['content'])) {
            $validated['content'] = PhpianRender::processStatic($validated['content'], [
                'reshape' => true,
                'bidi' => true,
                'convertNumbers' => true,
                'numberLocale' => 'persian',
            ]);
        }
        
        return $validated;
    }
}

// ============================================================================
// 8. MIDDLEWARE FOR PROCESSING RESPONSES
// ============================================================================

/**
 * Middleware to automatically process Persian text in responses
 * php artisan make:middleware ProcessPersianText
 */
class ProcessPersianText
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only process JSON responses
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            
            // Process specific fields
            if (isset($data['title'])) {
                $data['title'] = PhpianRender::processStatic($data['title']);
            }
            
            if (isset($data['content'])) {
                $data['content'] = PhpianRender::processStatic($data['content'], [
                    'convertNumbers' => true,
                    'numberLocale' => 'persian',
                ]);
            }
            
            $response->setData($data);
        }
        
        return $response;
    }
}

// ============================================================================
// 9. API RESOURCES
// ============================================================================

/**
 * Process text in API Resources
 */
class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => PhpianRender::processStatic($this->title),
            'content' => PhpianRender::processStatic($this->content, [
                'reshape' => true,
                'bidi' => true,
                'convertNumbers' => true,
                'numberLocale' => 'persian',
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

// ============================================================================
// 10. QUEUE JOBS
// ============================================================================

/**
 * Process text in background jobs
 */
class ProcessPostContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $post;
    
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
    
    public function handle()
    {
        $renderer = new PhpianRender();
        
        $processedContent = $renderer->process($this->post->content, [
            'reshape' => true,
            'bidi' => true,
            'convertNumbers' => true,
            'numberLocale' => 'persian',
        ]);
        
        $this->post->update([
            'processed_content' => $processedContent,
        ]);
    }
}

// ============================================================================
// 11. VIEW COMPOSERS
// ============================================================================

/**
 * Process text in View Composers
 */
class PostComposer
{
    public function compose(View $view)
    {
        $post = $view->getData()['post'];
        
        $view->with([
            'processedTitle' => PhpianRender::processStatic($post->title),
            'processedContent' => PhpianRender::processStatic($post->content, [
                'convertNumbers' => true,
                'numberLocale' => 'persian',
            ]),
        ]);
    }
}

// ============================================================================
// 12. CUSTOM VALIDATION RULE
// ============================================================================

/**
 * Custom validation rule for RTL text
 * php artisan make:rule RtlText
 */
class RtlText implements Rule
{
    public function passes($attribute, $value)
    {
        return PhpianRender::isRTLStatic($value);
    }
    
    public function message()
    {
        return 'The :attribute must be RTL text.';
    }
}

/**
 * Usage in validation:
 * 
 * 'title' => ['required', new RtlText()]
 */

// ============================================================================
// 13. CONFIG FILE
// ============================================================================

/**
 * Create config file: config/phpian-render.php
 * php artisan vendor:publish --tag=phpian-render-config
 */
return [
    'default' => [
        'reshape' => true,
        'bidi' => true,
        'convertNumbers' => false,
        'numberLocale' => 'persian',
        'preserveDiacritics' => true,
        'clean' => false,
        'reverse' => true,
    ],
    
    'presets' => [
        'full' => [
            'reshape' => true,
            'bidi' => true,
            'convertNumbers' => true,
            'numberLocale' => 'persian',
            'preserveDiacritics' => true,
            'clean' => true,
            'reverse' => true,
        ],
        
        'minimal' => [
            'reshape' => true,
            'bidi' => false,
            'convertNumbers' => false,
        ],
        
        'numbers_only' => [
            'reshape' => false,
            'bidi' => false,
            'convertNumbers' => true,
            'numberLocale' => 'persian',
        ],
    ],
];

/**
 * Usage with config:
 * 
 * $options = config('phpian-render.presets.full');
 * $processed = PhpianRender::processStatic($text, $options);
 */

// ============================================================================
// 14. COMMAND EXAMPLE
// ============================================================================

/**
 * Artisan command to process text
 * php artisan make:command ProcessPersianText
 */
class ProcessPersianText extends Command
{
    protected $signature = 'persian:process {text} {--preset=full}';
    protected $description = 'Process Persian text using PhpianRender';
    
    public function handle()
    {
        $text = $this->argument('text');
        $preset = $this->option('preset');
        
        $options = config("phpian-render.presets.{$preset}", config('phpian-render.default'));
        
        $processed = PhpianRender::processStatic($text, $options);
        
        $this->info("Original: {$text}");
        $this->info("Processed: {$processed}");
        
        return 0;
    }
}

/**
 * Usage:
 * php artisan persian:process "سلام دنیا"
 * php artisan persian:process "عدد 123" --preset=numbers_only
 */

// ============================================================================
// 15. TESTING EXAMPLES
// ============================================================================

/**
 * Feature test example
 */
class PersianTextProcessingTest extends TestCase
{
    public function test_processes_persian_text()
    {
        $text = 'سلام دنیا';
        $processed = PhpianRender::processStatic($text);
        
        $this->assertNotEmpty($processed);
        $this->assertNotEquals($text, $processed);
    }
    
    public function test_converts_numbers_to_persian()
    {
        $text = 'عدد 123 است';
        $processed = PhpianRender::convertNumbersStatic($text, 'persian');
        
        $this->assertStringContainsString('۱۲۳', $processed);
    }
    
    public function test_detects_rtl_text()
    {
        $persianText = 'سلام';
        $englishText = 'Hello';
        
        $this->assertTrue(PhpianRender::isRTLStatic($persianText));
        $this->assertFalse(PhpianRender::isRTLStatic($englishText));
    }
}

// ============================================================================
// 16. CACHE EXAMPLE
// ============================================================================

/**
 * Cache processed text for performance
 */
class CachedPersianProcessor
{
    public function process(string $text, array $options = []): string
    {
        $cacheKey = 'persian:' . md5($text . serialize($options));
        
        return Cache::remember($cacheKey, 3600, function () use ($text, $options) {
            return PhpianRender::processStatic($text, $options);
        });
    }
}

// ============================================================================
// 17. EVENT LISTENER EXAMPLE
// ============================================================================

/**
 * Process text when model is saved
 */
class PostObserver
{
    public function saving(Post $post)
    {
        // Process title before saving
        if ($post->isDirty('title')) {
            $post->title = PhpianRender::processStatic($post->title, [
                'reshape' => true,
                'bidi' => true,
            ]);
        }
        
        // Process content before saving
        if ($post->isDirty('content')) {
            $post->content = PhpianRender::processStatic($post->content, [
                'reshape' => true,
                'bidi' => true,
                'convertNumbers' => true,
                'numberLocale' => 'persian',
            ]);
        }
    }
}

/**
 * Register observer in AppServiceProvider:
 * 
 * Post::observe(PostObserver::class);
 */

// ============================================================================
// 18. SCOPE EXAMPLE
// ============================================================================

/**
 * Eloquent scope for searching Persian text
 */
trait PersianSearchable
{
    public function scopeSearchPersian($query, $term)
    {
        // Process search term
        $processedTerm = PhpianRender::processStatic($term, [
            'reshape' => true,
            'bidi' => true,
        ]);
        
        return $query->where('title', 'like', "%{$processedTerm}%")
                    ->orWhere('content', 'like', "%{$processedTerm}%");
    }
}

/**
 * Usage in model:
 * 
 * class Post extends Model
 * {
 *     use PersianSearchable;
 * }
 * 
 * // In controller:
 * $posts = Post::searchPersian($request->search)->get();
 */

// ============================================================================
// 19. NOTIFICATION EXAMPLE
// ============================================================================

/**
 * Process text in notifications
 */
class PostPublished extends Notification
{
    protected $post;
    
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(PhpianRender::processStatic($this->post->title))
            ->line(PhpianRender::processStatic($this->post->content, [
                'convertNumbers' => true,
                'numberLocale' => 'persian',
            ]));
    }
}

// ============================================================================
// 20. COMPLETE EXAMPLE: POST CONTROLLER WITH ALL FEATURES
// ============================================================================

class CompletePostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();
        
        // Search with Persian text processing
        if ($request->has('search')) {
            $searchTerm = PhpianRender::processStatic($request->search, [
                'reshape' => true,
                'bidi' => true,
            ]);
            $query->where('title', 'like', "%{$searchTerm}%");
        }
        
        $posts = $query->paginate(15);
        
        // Process titles for display
        $posts->getCollection()->transform(function ($post) {
            $post->processed_title = PhpianRender::processStatic($post->title);
            return $post;
        });
        
        return view('posts.index', compact('posts'));
    }
    
    public function show(Post $post)
    {
        // Process content with full options
        $processedContent = PhpianRender::processStatic($post->content, [
            'reshape' => true,
            'bidi' => true,
            'convertNumbers' => true,
            'numberLocale' => 'persian',
            'preserveDiacritics' => true,
            'clean' => true,
        ]);
        
        return view('posts.show', [
            'post' => $post,
            'content' => $processedContent,
        ]);
    }
    
    public function store(StorePostRequest $request)
    {
        // Request already processes the text
        $post = Post::create($request->validated());
        
        // Dispatch job for additional processing
        ProcessPostContent::dispatch($post);
        
        return redirect()->route('posts.show', $post)
            ->with('success', 'Post created successfully');
    }
    
    public function apiIndex()
    {
        $posts = Post::all();
        
        return PostResource::collection($posts);
    }
}

// ============================================================================
// END OF EXAMPLES
// ============================================================================

