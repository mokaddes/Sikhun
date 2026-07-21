# SIKHUN.COM — AGENT SKILLS & CODING PATTERNS

> This file defines HOW to write code for the Sikhun project.
> Every pattern here must be followed consistently across the entire codebase.
> Read this BEFORE writing any PHP, Vue, or migration file.

---

## SKILL 1: LARAVEL MULTI-GUARD AUTHENTICATION

### How guards are wired

```php
// bootstrap/app.php — register admin routes
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    then: function () {
        Route::middleware('web')
             ->group(base_path('routes/admin.php'));
    },
)
```

### Student model (web guard)
```php
// app/Models/Student.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guard = 'web';

    protected $fillable = [
        'name', 'email', 'password', 'type', 'status',
        'wallet_balance', 'ai_trial_minutes_used', 'referral_code',
        'referred_by_student_id', 'avatar', 'theme_mode',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'wallet_balance' => 'decimal:2',
        'password' => 'hashed',
    ];

    // Relationships
    public function bookShelf() { return $this->hasMany(BookShelf::class); }
    public function activeSubscription() { return $this->hasOne(StudentSubscription::class)->where('status', 'active')->latest(); }
    public function walletTransactions() { return $this->hasMany(WalletTransaction::class); }
    public function examSessions() { return $this->hasMany(ExamSession::class); }
}
```

### Admin model (admin guard)
```php
// app/Models/Admin.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $guard = 'admin';

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed', 'is_active' => 'boolean'];
}
```

### Route middleware pattern
```php
// routes/web.php
Route::middleware(['auth:web', 'verified', 'student.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ...
});

// routes/admin.php
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // ...
});

// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    // ...
});
```

### Auth controller pattern
```php
// app/Http/Controllers/Auth/StudentAuthController.php
public function login(LoginRequest $request)
{
    if (!Auth::guard('web')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
        throw ValidationException::withMessages([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    $student = Auth::guard('web')->user();

    if ($student->status === 'inactive') {
        Auth::guard('web')->logout();
        throw ValidationException::withMessages([
            'email' => 'Your account has been deactivated.',
        ]);
    }

    $request->session()->regenerate();
    return redirect()->intended(route('dashboard'));
}
```

---

## SKILL 2: DATABASE MIGRATIONS PATTERN

### Migration naming convention
```
create_students_table.php
create_books_table.php
create_ai_providers_table.php
```

### Standard migration structure
```php
// database/migrations/create_exam_sessions_table.php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('source_type', ['book', 'chapter', 'page', 'topic', 'paragraph']);
            $table->foreignId('source_book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->string('source_chapter', 100)->nullable();
            $table->integer('source_page')->nullable();
            $table->text('source_text')->nullable();
            $table->json('config'); // {type, count, duration, mode}
            $table->json('questions');
            $table->json('answers')->nullable();
            $table->integer('score')->default(0);
            $table->integer('total')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->enum('mode', ['practice', 'exam']);
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['student_id', 'status']);
            $table->index(['student_id', 'mode', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
```

### JSON column casting in models
```php
protected $casts = [
    'config' => 'array',
    'questions' => 'array',
    'answers' => 'array',
    'gift_book_ids' => 'array',
    'features' => 'array',
];
```

---

## SKILL 3: SERVICE CLASS PATTERN

All business logic lives in Service classes. Controllers are thin.

```php
// app/Services/WalletService.php
namespace App\Services;

use App\Models\Student;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(Student $student, float $amount, string $category, string $reference = null, string $notes = null): WalletTransaction
    {
        return DB::transaction(function () use ($student, $amount, $category, $reference, $notes) {
            $balanceBefore = $student->wallet_balance;
            $student->increment('wallet_balance', $amount);
            $student->refresh();

            return WalletTransaction::create([
                'student_id' => $student->id,
                'type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $student->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);
        });
    }

    public function debit(Student $student, float $amount, string $category, string $reference = null, string $notes = null): WalletTransaction
    {
        if ($student->wallet_balance < $amount) {
            throw new \Exception('Insufficient wallet balance.');
        }

        return DB::transaction(function () use ($student, $amount, $category, $reference, $notes) {
            $balanceBefore = $student->wallet_balance;
            $student->decrement('wallet_balance', $amount);
            $student->refresh();

            return WalletTransaction::create([
                'student_id' => $student->id,
                'type' => 'debit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $student->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);
        });
    }

    public function hasSufficientBalance(Student $student, float $amount): bool
    {
        return $student->wallet_balance >= $amount;
    }
}
```

### Controller using service (thin controller pattern)
```php
// app/Http/Controllers/Student/WalletController.php
class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function index(): Response
    {
        $student = auth()->user();
        return Inertia::render('Student/Wallet/Index', [
            'balance' => $student->wallet_balance,
            'transactions' => $student->walletTransactions()
                ->latest()
                ->paginate(20),
        ]);
    }
}
```

---

## SKILL 4: AI PROVIDER SYSTEM

### Contract interface (strict — do not deviate)
```php
// app/Contracts/AiProviderContract.php
namespace App\Contracts;

interface AiProviderContract
{
    public function chat(array $messages, array $options = []): string;
    public function stream(array $messages, array $options = []): \Generator;
    public function embed(string $text): array;
    public function isAvailable(): bool;
}
```

### Example provider implementation
```php
// app/Services/Ai/Providers/OpenAiProvider.php
namespace App\Services\Ai\Providers;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAiProvider implements AiProviderContract
{
    public function __construct(private AiProvider $provider) {}

    public function chat(array $messages, array $options = []): string
    {
        $response = OpenAI::chat()->create([
            'model' => $this->provider->model_name,
            'messages' => $messages,
            'max_tokens' => $options['max_tokens'] ?? $this->provider->max_tokens,
            'temperature' => $options['temperature'] ?? $this->provider->temperature,
        ]);

        return $response->choices[0]->message->content;
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        $stream = OpenAI::chat()->createStreamed([
            'model' => $this->provider->model_name,
            'messages' => $messages,
        ]);

        foreach ($stream as $response) {
            $chunk = $response->choices[0]->delta->content;
            if ($chunk !== null) {
                yield $chunk;
            }
        }
    }

    public function embed(string $text): array
    {
        $response = OpenAI::embeddings()->create([
            'model' => 'text-embedding-3-small',
            'input' => $text,
        ]);
        return $response->embeddings[0]->embedding;
    }

    public function isAvailable(): bool
    {
        return !empty($this->provider->api_key) && $this->provider->is_active;
    }
}
```

### Factory (always use this to get providers)
```php
// app/Services/Ai/AiProviderFactory.php
namespace App\Services\Ai;

use App\Contracts\AiProviderContract;
use App\Models\AiProvider;

class AiProviderFactory
{
    public static function make(int $providerId): AiProviderContract
    {
        $provider = AiProvider::findOrFail($providerId);
        return self::instantiate($provider);
    }

    public static function default(string $useCase): AiProviderContract
    {
        $provider = AiProvider::where('use_case', $useCase)
            ->where('is_default', true)
            ->where('is_active', true)
            ->firstOrFail();
        return self::instantiate($provider);
    }

    private static function instantiate(AiProvider $provider): AiProviderContract
    {
        return match($provider->type) {
            'openai'      => new Providers\OpenAiProvider($provider),
            'gemini'      => new Providers\GeminiProvider($provider),
            'claude'      => new Providers\ClaudeProvider($provider),
            'groq'        => new Providers\GroqProvider($provider),
            'deepseek'    => new Providers\DeepSeekProvider($provider),
            'ollama'      => new Providers\OllamaProvider($provider),
            'vllm'        => new Providers\VllmProvider($provider),
            'huggingface' => new Providers\HuggingFaceProvider($provider),
            default       => throw new \InvalidArgumentException("Unknown provider type: {$provider->type}"),
        };
    }
}
```

### Using provider in a Job
```php
// app/Jobs/GenerateExamQuestions.php
class GenerateExamQuestions implements ShouldQueue
{
    public $queue = 'ai';
    public $tries = 3;
    public $timeout = 120;

    public function __construct(private int $examSessionId) {}

    public function handle(): void
    {
        $session = ExamSession::findOrFail($this->examSessionId);

        $provider = AiProviderFactory::default('exam_gen');

        $prompt = $this->buildPrompt($session);
        $response = $provider->chat([
            ['role' => 'system', 'content' => $prompt['system']],
            ['role' => 'user', 'content' => $prompt['user']],
        ]);

        $questions = $this->parseQuestionsJson($response);

        $session->update([
            'questions' => $questions,
            'total' => count($questions),
            'status' => 'in_progress',
        ]);
    }

    private function parseQuestionsJson(string $response): array
    {
        // Strip markdown code fences if present
        $clean = preg_replace('/```json\s*|\s*```/', '', $response);
        $data = json_decode(trim($clean), true);

        if (!isset($data['questions']) || !is_array($data['questions'])) {
            throw new \Exception('AI returned invalid question format');
        }

        return $data['questions'];
    }
}
```

---

## SKILL 5: INERTIA + VUE 3 PATTERNS

### Page component structure (Composition API — ALWAYS use this, not Options API)
```vue
<!-- resources/js/Pages/Student/Library/Index.vue -->
<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import StudentLayout from '@/Components/Layout/StudentLayout.vue'
import BookCard from '@/Components/Library/BookCard.vue'

// Props from Inertia (typed)
const props = defineProps({
  books: { type: Object, required: true },  // paginated
  filters: { type: Object, default: () => ({}) },
})

// Reactive state
const search = ref(props.filters.search ?? '')
const selectedLevel = ref(props.filters.level ?? '')

// Computed
const hasBooks = computed(() => props.books.data.length > 0)

// Methods
function applyFilters() {
  router.get(route('library.index'), {
    search: search.value,
    level: selectedLevel.value,
  }, {
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <Head title="Library" />
  <StudentLayout>
    <!-- content -->
  </StudentLayout>
</template>
```

### SEO in every page via Head + JsonLd component
```vue
<script setup>
import { Head } from '@inertiajs/vue3'
import JsonLd from '@/Components/Seo/JsonLd.vue'

const props = defineProps({
  book: Object,
  seo: Object,  // from SeoService — title, description, og_image, canonical
})
</script>

<template>
  <Head>
    <title>{{ seo.title }}</title>
    <meta name="description" :content="seo.description" />
    <link rel="canonical" :href="seo.canonical" />
    <meta property="og:title" :content="seo.title" />
    <meta property="og:description" :content="seo.description" />
    <meta property="og:image" :content="seo.og_image" />
    <meta property="og:url" :content="seo.canonical" />
    <meta property="og:type" content="book" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="seo.title" />
    <meta name="twitter:description" :content="seo.description" />
    <meta name="twitter:image" :content="seo.og_image" />
  </Head>

  <JsonLd :data="bookJsonLd" />
</template>
```

### JsonLd component
```vue
<!-- resources/js/Components/Seo/JsonLd.vue -->
<script setup>
defineProps({ data: { type: Object, required: true } })
</script>

<template>
  <component
    :is="'script'"
    type="application/ld+json"
    v-html="JSON.stringify(data)"
  />
</template>
```

### Pinia store pattern
```js
// resources/js/Stores/theme.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useThemeStore = defineStore('theme', {
  state: () => ({
    mode: localStorage.getItem('sikhun_theme') || 'system',
  }),

  actions: {
    init() {
      this.applyTheme()
      // Listen for OS preference changes
      window.matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', () => {
          if (this.mode === 'system') this.applyTheme()
        })
    },

    async setMode(mode) {
      this.mode = mode
      localStorage.setItem('sikhun_theme', mode)
      this.applyTheme()

      // Persist to backend if authenticated
      try {
        await axios.put('/api/profile/theme', { theme_mode: mode })
      } catch (e) {
        // Not authenticated — that's fine
      }
    },

    applyTheme() {
      const root = document.documentElement
      const dark =
        this.mode === 'dark' ||
        (this.mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)

      dark ? root.classList.add('dark') : root.classList.remove('dark')
    },
  },
})
```

---

## SKILL 6: SSE STREAMING (AI CHAT)

### Backend SSE endpoint
```php
// app/Http/Controllers/Student/AiChatController.php
public function stream(AiSession $session, Request $request): StreamedResponse
{
    // Auth + ownership check
    abort_unless($session->student_id === auth()->id(), 403);

    $messages = $this->buildMessages($session);

    return response()->stream(function () use ($messages, $session) {
        try {
            $provider = AiProviderFactory::default('book_chat');
            $fullResponse = '';

            foreach ($provider->stream($messages) as $chunk) {
                $fullResponse .= $chunk;
                echo 'data: ' . json_encode(['content' => $chunk, 'done' => false]) . "\n\n";
                ob_flush();
                flush();
            }

            // Save complete response to session
            $this->appendToSession($session, $fullResponse);

            echo 'data: ' . json_encode(['content' => '', 'done' => true]) . "\n\n";
            ob_flush();
            flush();

        } catch (\Exception $e) {
            echo 'data: ' . json_encode(['error' => 'AI service unavailable. Please try again.']) . "\n\n";
            ob_flush();
            flush();
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'X-Accel-Buffering' => 'no',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
}
```

### Frontend SSE client
```vue
<!-- resources/js/Components/AiChat/ChatBox.vue -->
<script setup>
import { ref } from 'vue'

const messages = ref([])
const currentResponse = ref('')
const isStreaming = ref(false)

async function sendMessage(content) {
  messages.value.push({ role: 'user', content })
  isStreaming.value = true
  currentResponse.value = ''

  const eventSource = new EventSource(
    route('ai.chat.stream', { session: props.sessionId })
  )

  eventSource.onmessage = (event) => {
    const data = JSON.parse(event.data)

    if (data.error) {
      currentResponse.value = data.error
      eventSource.close()
      isStreaming.value = false
      return
    }

    if (data.done) {
      messages.value.push({ role: 'assistant', content: currentResponse.value })
      currentResponse.value = ''
      eventSource.close()
      isStreaming.value = false
      return
    }

    currentResponse.value += data.content
  }

  eventSource.onerror = () => {
    eventSource.close()
    isStreaming.value = false
  }
}
</script>
```

---

## SKILL 7: FORM REQUEST VALIDATION PATTERN

Always use Form Request classes — never validate in controllers directly.

```php
// app/Http/Requests/Student/CreateExamRequest.php
namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class CreateExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'source_type' => ['required', 'in:book,chapter,page,topic,paragraph'],
            'source_book_id' => ['required_if:source_type,book,chapter,page', 'nullable', 'exists:books,id'],
            'source_chapter' => ['required_if:source_type,chapter', 'nullable', 'string', 'max:100'],
            'source_page' => ['required_if:source_type,page', 'nullable', 'integer', 'min:1'],
            'source_text' => ['required_if:source_type,topic,paragraph', 'nullable', 'string', 'min:50', 'max:5000'],
            'config.type' => ['required', 'in:mcq,cq,short,true_false,fill_blank'],
            'config.count' => ['required', 'integer', 'min:5', 'max:50'],
            'config.duration' => ['required', 'in:0,10,15,20,30,45,60,90,120'],
            'config.mode' => ['required', 'in:practice,exam'],
        ];
    }

    public function messages(): array
    {
        return [
            'config.count.max' => 'Maximum 50 questions allowed per exam.',
            'source_text.min' => 'Please provide at least 50 characters of text.',
        ];
    }
}
```

---

## SKILL 8: API CONTROLLER PATTERN

API controllers are separate from web controllers but share service classes.

```php
// app/Http/Controllers/Api/LibraryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\BookAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function __construct(private BookAccessService $accessService) {}

    public function index(Request $request): JsonResponse
    {
        $books = Book::published()
            ->with(['author', 'publication', 'category'])
            ->when($request->level, fn($q) => $q->where('level', $request->level))
            ->when($request->subject, fn($q) => $q->where('subject', $request->subject))
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->free, fn($q) => $q->where('is_free', true))
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $books->items(),
            'meta' => [
                'pagination' => [
                    'total' => $books->total(),
                    'per_page' => $books->perPage(),
                    'current_page' => $books->currentPage(),
                    'last_page' => $books->lastPage(),
                ],
            ],
        ]);
    }

    public function show(Book $book): JsonResponse
    {
        $book->load(['author', 'publication', 'category']);
        $student = auth()->user();
        $hasAccess = $this->accessService->studentHasAccess($student, $book);

        return response()->json([
            'success' => true,
            'data' => array_merge($book->toArray(), [
                'has_access' => $hasAccess,
                'access_type' => $this->accessService->accessType($student, $book),
            ]),
        ]);
    }
}
```

### API error handling helper
```php
// app/Http/Controllers/Api/Controller.php (base API controller)
abstract class Controller extends BaseController
{
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    protected function error(string $message, array $errors = [], int $code = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
```

---

## SKILL 9: REDIS CACHING PATTERN

### Leaderboard caching
```php
// app/Services/LeaderboardService.php
class LeaderboardService
{
    private string $cachePrefix = 'leaderboard:';
    private int $cacheTtl = 300; // 5 minutes

    public function getTopStudents(string $type, string $period, array $filters = []): array
    {
        $cacheKey = $this->buildCacheKey($type, $period, $filters);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($type, $period, $filters) {
            return $this->queryLeaderboard($type, $period, $filters);
        });
    }

    public function invalidate(string $type, string $period): void
    {
        // Invalidate all filter combinations for this type+period
        $pattern = $this->cachePrefix . "{$type}:{$period}:*";
        $keys = Redis::keys($pattern);
        if (!empty($keys)) {
            Redis::del($keys);
        }
    }

    private function buildCacheKey(string $type, string $period, array $filters): string
    {
        $filterHash = md5(json_encode($filters));
        return $this->cachePrefix . "{$type}:{$period}:{$filterHash}";
    }
}
```

### Site settings caching
```php
// app/Services/SiteSettingService.php
class SiteSettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("site_setting:{$key}", 3600, function () use ($key, $default) {
            $setting = SiteSetting::where('key', $key)->first();
            return $setting ? json_decode($setting->value, true) : $default;
        });
    }

    public function set(string $key, mixed $value): void
    {
        SiteSetting::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
        Cache::forget("site_setting:{$key}");
    }
}
```

---

## SKILL 10: BOOK READER SECURITY

### PDF page serving (never expose PDF directly)
```php
// app/Services/BookReaderService.php
class BookReaderService
{
    public function getPageImageUrl(Book $book, int $page, Student $student): string
    {
        return URL::temporarySignedRoute(
            'reader.page',
            now()->addMinutes(15),
            ['book' => $book->id, 'page' => $page, 'student' => $student->id]
        );
    }

    public function renderPage(Book $book, int $page, Student $student): string
    {
        $cacheKey = "book_page:{$book->id}:{$page}:{$student->id}";

        return Cache::remember($cacheKey, 900, function () use ($book, $page, $student) {
            // Convert PDF page to image
            $imagick = new \Imagick();
            $pdfPath = storage_path("app/private/{$book->pdf_path}");
            $imagick->readImage("{$pdfPath}[" . ($page - 1) . "]");
            $imagick->setImageFormat('jpg');
            $imagick->setImageResolution(150, 150);
            $imagick->resampleImage(150, 150, \Imagick::FILTER_LANCZOS, 1);

            // Apply watermark
            $draw = new \ImagickDraw();
            $draw->setFillColor(new \ImagickPixel('rgba(0,0,0,0.2)'));
            $draw->setFontSize(14);
            $draw->setTextAntialias(true);
            $watermark = "{$student->name} | ID:{$student->id} | sikhun.com";
            $imagick->annotateImage($draw, 10, 20, 0, $watermark);

            return base64_encode($imagick->getImageBlob());
        });
    }
}
```

### Rate limiting on reader page route
```php
// routes/web.php
Route::get('/reader/{book}/page/{page}', [ReaderController::class, 'servePage'])
    ->name('reader.page')
    ->middleware([
        'auth:web',
        'signed',
        RateLimiter::for('reader-pages', function (Request $request) {
            return Limit::perSeconds(10, 5)->by($request->user()?->id);
        }),
    ]);
```

---

## SKILL 11: TAILWIND DARK MODE PATTERN

### Component styling pattern (always include dark: variants)
```vue
<!-- ALWAYS style both modes. Never leave dark mode unstyled. -->
<template>
  <div class="bg-white dark:bg-[#111118] border border-gray-200 dark:border-[#2a2a38] rounded-xl p-6">
    <h3 class="text-gray-900 dark:text-[#e8e8f0] font-semibold text-lg mb-2">
      {{ title }}
    </h3>
    <p class="text-gray-500 dark:text-[#7a7a9a] text-sm">
      {{ description }}
    </p>
    <button class="
      bg-[#6c63ff] hover:bg-[#5b53ee]
      dark:bg-[#6c63ff] dark:hover:bg-[#5b53ee]
      text-white font-medium px-4 py-2 rounded-lg
      transition-colors duration-200
    ">
      {{ buttonText }}
    </button>
  </div>
</template>
```

### StudentLayout dark mode setup
```vue
<!-- resources/js/Components/Layout/StudentLayout.vue -->
<script setup>
import { onMounted } from 'vue'
import { useThemeStore } from '@/Stores/theme'
import ThemeToggle from '@/Components/UI/ThemeToggle.vue'

const theme = useThemeStore()
onMounted(() => theme.init())
</script>

<template>
  <div class="min-h-screen bg-white dark:bg-[#09090f] transition-colors duration-200">
    <nav class="bg-white dark:bg-[#111118] border-b border-gray-200 dark:border-[#2a2a38]">
      <!-- ... -->
      <ThemeToggle />
    </nav>
    <main>
      <slot />
    </main>
  </div>
</template>
```

---

## SKILL 12: SEEDER PATTERNS

### Idempotent seeder (safe to run multiple times)
```php
// database/seeders/PlanSeeder.php
public function run(): void
{
    $plans = [
        [
            'name' => 'স্টার্টার',
            'slug' => 'starter',
            'description' => 'নতুন শিক্ষার্থীদের জন্য আদর্শ পরিকল্পনা',
            'price_monthly' => 99.00,
            'ai_chat_minutes' => 60,
            'ai_exam_count' => 20,
            'gift_book_ids' => json_encode([]),
            'trial_ai_minutes' => 10,
            'features' => json_encode([
                'AI Chat (60 মিনিট/মাস)',
                '20টি AI পরীক্ষা/মাস',
                'ফ্ল্যাশকার্ড জেনারেটর',
                'সাধারণ সাপোর্ট',
            ]),
            'is_active' => true,
        ],
        // ... other plans
    ];

    foreach ($plans as $plan) {
        Plan::updateOrCreate(
            ['slug' => $plan['slug']],
            $plan
        );
    }
}
```

### Demo data seeder with relationships
```php
// database/seeders/DemoDataSeeder.php
public function run(): void
{
    $students = Student::all();
    $books = Book::all();

    foreach ($students as $student) {
        // Add 1-3 books to bookshelf
        $booksToAdd = $books->random(min(3, $books->count()));
        foreach ($booksToAdd as $book) {
            BookShelf::updateOrCreate(
                ['student_id' => $student->id, 'book_id' => $book->id],
                ['source' => 'purchased', 'added_at' => now()->subDays(rand(1, 30))]
            );
        }

        // Add wallet transaction
        WalletTransaction::create([
            'student_id' => $student->id,
            'type' => 'credit',
            'category' => 'wallet_recharge',
            'amount' => rand(100, 500),
            'balance_before' => 0,
            'balance_after' => rand(100, 500),
            'reference' => 'DEMO-' . strtoupper(Str::random(8)),
            'notes' => 'Demo wallet recharge',
        ]);

        // Create 2 completed exam sessions
        for ($i = 0; $i < 2; $i++) {
            $score = rand(5, 10);
            $total = 10;
            ExamSession::create([
                'student_id' => $student->id,
                'source_type' => 'topic',
                'source_text' => 'পদার্থবিজ্ঞান',
                'config' => ['type' => 'mcq', 'count' => 10, 'duration' => 30, 'mode' => 'exam'],
                'questions' => json_encode($this->fakeMcqQuestions(10)),
                'score' => $score,
                'total' => $total,
                'percentage' => ($score / $total) * 100,
                'mode' => 'exam',
                'status' => 'completed',
                'started_at' => now()->subDays(rand(1, 20)),
                'completed_at' => now()->subDays(rand(0, 19)),
            ]);
        }
    }
}

private function fakeMcqQuestions(int $count): array
{
    $questions = [];
    for ($i = 1; $i <= $count; $i++) {
        $questions[] = [
            'id' => $i,
            'question' => "ডেমো প্রশ্ন {$i}: নিউটনের কোন সূত্রে বল এবং ত্বরণের সম্পর্ক আলোচনা করা হয়?",
            'type' => 'mcq',
            'options' => ['প্রথম সূত্র', 'দ্বিতীয় সূত্র', 'তৃতীয় সূত্র', 'কোনোটি নয়'],
            'correct_answer' => 'দ্বিতীয় সূত্র',
            'explanation' => 'নিউটনের দ্বিতীয় সূত্র: F = ma',
        ];
    }
    return $questions;
}
```

---

## SKILL 13: SEO SERVICE

```php
// app/Services/SeoService.php
namespace App\Services;

use App\Models\Book;
use App\Models\Course;
use Illuminate\Support\Str;

class SeoService
{
    private string $siteName = 'Sikhun.com';
    private string $siteUrl;

    public function __construct()
    {
        $this->siteUrl = config('app.url');
    }

    public function forHome(): array
    {
        return [
            'title' => "Sikhun.com — বাংলাদেশের প্রথম AI-চালিত শিক্ষা প্ল্যাটফর্ম",
            'description' => 'ডিজিটালি বই পড়ুন, AI-এর সাথে চ্যাট করুন, পরীক্ষা দিন এবং ফ্ল্যাশকার্ড তৈরি করুন। HSC, SSC, বিশ্ববিদ্যালয় ও চাকরির প্রস্তুতির জন্য।',
            'og_image' => asset('images/og-home.jpg'),
            'canonical' => $this->siteUrl . '/',
            'keywords' => 'sikhun, শিখুন, HSC, SSC, বাংলাদেশ শিক্ষা, AI শিক্ষা, ডিজিটাল বই',
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOrganization',
                'name' => 'Sikhun.com',
                'description' => 'AI-powered education platform for Bangladeshi students',
                'url' => $this->siteUrl,
                'inLanguage' => ['bn', 'en'],
                'areaServed' => 'BD',
            ],
        ];
    }

    public function forBook(Book $book): array
    {
        return [
            'title' => "{$book->title} — {$this->levelLabel($book->level)} | {$this->siteName}",
            'description' => Str::limit(strip_tags($book->description ?? ''), 155),
            'og_image' => $book->cover_image_url ?? asset('images/og-default.jpg'),
            'canonical' => $this->siteUrl . '/library/' . $book->slug,
            'keywords' => implode(', ', array_filter([
                $book->title, $book->subject, $book->level, 'বই', 'Sikhun', 'ডিজিটাল বই'
            ])),
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'Book',
                'name' => $book->title,
                'description' => Str::limit(strip_tags($book->description ?? ''), 200),
                'author' => ['@type' => 'Person', 'name' => $book->author->name ?? ''],
                'publisher' => ['@type' => 'Organization', 'name' => $book->publication->name ?? ''],
                'inLanguage' => 'bn',
                'educationalLevel' => $this->levelLabel($book->level),
                'url' => $this->siteUrl . '/library/' . $book->slug,
            ],
        ];
    }

    public function forCourse(Course $course): array
    {
        return [
            'title' => "{$course->title} | {$this->siteName}",
            'description' => Str::limit(strip_tags($course->description ?? ''), 155),
            'og_image' => $course->cover_image_url ?? asset('images/og-default.jpg'),
            'canonical' => $this->siteUrl . '/courses/' . $course->slug,
            'keywords' => implode(', ', [$course->title, 'কোর্স', 'শিখুন', 'Sikhun']),
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'Course',
                'name' => $course->title,
                'description' => Str::limit(strip_tags($course->description ?? ''), 200),
                'provider' => ['@type' => 'Organization', 'name' => 'Sikhun.com', 'url' => $this->siteUrl],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $course->price,
                    'priceCurrency' => 'BDT',
                    'availability' => 'https://schema.org/InStock',
                ],
                'url' => $this->siteUrl . '/courses/' . $course->slug,
            ],
        ];
    }

    private function levelLabel(?string $level): string
    {
        return match($level) {
            'hsc' => 'HSC',
            'ssc' => 'SSC',
            'university' => 'বিশ্ববিদ্যালয়',
            'job' => 'চাকরির প্রস্তুতি',
            default => 'সাধারণ',
        };
    }
}
```

---

## SKILL 14: SCHEDULED COMMANDS PATTERN

```php
// app/Console/Commands/GenerateAiNotifications.php
namespace App\Console\Commands;

use App\Models\Student;
use App\Services\Ai\AiProviderFactory;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class GenerateAiNotifications extends Command
{
    protected $signature = 'notifications:generate-ai';
    protected $description = 'Generate and queue AI-powered educational notifications';

    public function handle(NotificationService $notificationService): int
    {
        $this->info('Generating AI notifications...');

        $topics = ['history', 'science', 'geography', 'culture', 'language', 'technology'];
        $topic = $topics[array_rand($topics)];

        try {
            $provider = AiProviderFactory::default('notification_gen');

            $content = $provider->chat([
                ['role' => 'system', 'content' => 'You generate short Bengali educational notifications. Keep under 150 characters. Format: "Topic: fact". Only return the notification text, nothing else.'],
                ['role' => 'user', 'content' => "Generate a fact about {$topic} in Bengali for HSC students."],
            ]);

            $notificationService->scheduleAiNotification(
                type: 'general_knowledge',
                title: 'আজকের তথ্য',
                body: trim($content),
                targetAudience: 'all',
                scheduledFor: now()->addMinutes(5),
            );

            $this->info('AI notification generated and scheduled.');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to generate AI notification: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
```

---

## SKILL 15: COMMON MISTAKES TO AVOID

### ❌ WRONG: Business logic in controller
```php
// WRONG
public function purchase(Request $request, Book $book) {
    $student = auth()->user();
    $student->wallet_balance -= $book->price;
    $student->save();
    // ...
}
```
### ✅ RIGHT: Delegate to service
```php
// RIGHT
public function purchase(PurchaseBookRequest $request, Book $book) {
    $this->bookPurchaseService->purchase(auth()->user(), $book);
    return back()->with('success', 'Book purchased!');
}
```

### ❌ WRONG: Direct AI call in controller
```php
// WRONG — blocks HTTP request for 30+ seconds
public function generateExam(Request $request) {
    $questions = OpenAI::chat()->create([...]); // BLOCKS
}
```
### ✅ RIGHT: Dispatch to queue
```php
// RIGHT
public function generateExam(CreateExamRequest $request) {
    $session = ExamSession::create([...]);
    GenerateExamQuestions::dispatch($session->id);
    return response()->json(['session_id' => $session->id, 'status' => 'generating']);
}
```

### ❌ WRONG: Exposing PDF path
```php
// WRONG — never do this
return response()->download(storage_path("app/private/{$book->pdf_path}"));
```
### ✅ RIGHT: Serve as watermarked signed image
```php
// RIGHT
return response($this->readerService->renderPage($book, $page, $student), 200, [
    'Content-Type' => 'image/jpeg',
    'Cache-Control' => 'private, max-age=900',
]);
```

### ❌ WRONG: N+1 in loop
```php
// WRONG
foreach ($students as $student) {
    echo $student->subscription->plan->name; // N+1!
}
```
### ✅ RIGHT: Eager load
```php
// RIGHT
$students = Student::with(['subscription.plan'])->get();
```

### ❌ WRONG: Options API in Vue
```javascript
// WRONG — do not use Options API
export default {
  data() { return { count: 0 } },
  methods: { increment() { this.count++ } }
}
```
### ✅ RIGHT: Composition API with `<script setup>`
```vue
<script setup>
import { ref } from 'vue'
const count = ref(0)
const increment = () => count.value++
</script>
```

---

*Sikhun.com Agent Skills v2.0 — Follow these patterns for every file written*
