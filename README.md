
# Recruitment Assignment

A simple Laravel application for fetching users and purchase data.


## Tech Stack

- PHP 8.3.11
- composer v. 2.4.3
- npm v. 18.17.0
## Installation and run locally

Clone the project:

```bash
git clone https://link-to-project
```

Go to the project directory:

```bash
cd assignment
```

Install dependencies:

```bash
composer install
npm install
```

Create a `.env` file based on `env.example` (make sure the `DB_CONNECTION` variable is set to "sqlite").

Migrate the database:

```bash
php artisan migrate
```

Start the server:

```bash
php artisan serve
```

Run Vite:

```bash
npm run dev
```


## Description of Implemented Functionalities

### Created users and purchases data
**Models**

    
First, the existing *User* model was extended by defining a relationship:

```php
public function purchases(): HasMany
{
    return $this->hasMany(Purchase::class);
}
```

Next, the *Purchase* model was defined:

```php
class Purchase extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

**Migrations**

While the migration for the *users* table already exists, a new migration for the *purchases* table was created:

```php
return new class extends Migration
{
    /**
    * Run the migrations.
    */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->date('purchase_date');
            $table->timestamps();
        });
    }

    /**
    * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
```

**Factories**

The *UserFactory.php* already exists, but *PurchaseFactory.php* had to be defined:

```php
class PurchaseFactory extends Factory
{
    /**
    * Define the model's default state.
    *
    * @return array<string, mixed>
    */
    public function definition(): array
    {
        return [
            'purchase_date' => fake()->dateTimeBetween('-5 year', 'now'),
        ];
    }
}
```

**Seeders**

To seed the database with users and purchases, the *run()* method in *UserSeeder.php* was defined:


```php
public function run(): void
{
    User::factory()->count(1000)->hasPurchases(10)->create();
}
```
### Fetching User Data with the Last Purchase Date

To fetch users along with their last purchase date, the following local scope was defined in *User.php*:

```php
public function scopeWithLastPurchaseDate(Builder $query): Builder
{
    return $query->addSelect(
        DB::raw('MAX(purchases.purchase_date) as last_purchase_date')
    )
        ->leftJoin('purchases', 'users.id', '=', 'purchases.user_id')
        ->groupBy('users.id');
}
```

This scope can then be used in business logic:

```php
$users = User::select(
    'users.id', 
    'users.name', 
    'users.birthdate', 
    'users.email', 
    'users.email_verified_at', 
    'users.created_at', 
    'users.updated_at', 
)
    ->withLastPurchaseDate()
    ->paginate($this->perPage);
```
### Adding User's Birthdate Column

To add a new column to the *users* table, a new migration was created:
```php
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthdate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'birthdate')) {
                $table->dropColumn('birthdate');
            }
        });
    }
};
```

**Fetching Users Sorted by Birthday**

To order users by their birthday (not their full birthdate), a new local scope was created in the *User* model:

```php
public function scopeOrderByBirthday(Builder $query): Builder
{
    return $query->orderByRaw('strftime(\'%m\', birthdate) ASC, strftime(\'%d\', birthdate) ASC');
}
```
This can be utilized by chaining it with the existing query:

```php
$users = User::select(
    'users.id', 
    'users.name', 
    'users.birthdate', 
    'users.email', 
    'users.email_verified_at', 
    'users.created_at', 
    'users.updated_at', 
)
    ->withLastPurchaseDate()
    ->orderByBirthday()
    ->paginate($this->perPage);
```

**Show Users Having Birthdays This Week**

To limit users to only those having birthdays this week, another local scope was added:

```php
public function scopeHavingBirthdayThisWeek(Builder $query): Builder
{
    $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
    $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

    return $query->whereBetween(
        DB::raw('strftime(\'%m-%d\', birthdate)'), 
        [
            $startOfWeek->format('m-d'), 
            $endOfWeek->format('m-d')
        ]
    );
}
```

By chaining this scope function, the final query looks like this:

```php
$users = User::select(
    'users.id', 
    'users.name', 
    'users.birthdate', 
    'users.email', 
    'users.email_verified_at', 
    'users.created_at', 
    'users.updated_at', 
)
    ->withLastPurchaseDate()
    ->havingBirthdayThisWeek()
    ->orderByBirthday()
    ->paginate($this->perPage);
```


## Demo

To display the results utilizing all created scopes, a new route in *web.php* was created:

```php
Route::get('/users', function () {
    return view('users');
});
```

The defined *users* view invokes a Livewire users-table component:

```php
<livewire:users-table />
```

It's a simple table with pagination displaying user data. It's available at URL http://127.0.0.1:8000/users.


## Potential Performance Issues
- **Use of costly formatting function strftime() (SQLite):**

The *strftime()* function is used in the query, which can lead to considerable load with large amounts of data. 

- **Instantiating Eloquent models:**

Instantiating many Eloquent models in Laravel may have a significant impact on performance.

## Potential Optimizations

As the amount of data in the users and purchases tables grows, some optimizations could be made to improve the performance of the query used in the `GET /users` route.

### Model vs. Table

With a large amount of data and an increasing number of columns in the users table, using *DB::table('users')* instead of the *User* model should be considered. However, using *DB::table('users')* eliminates the possibility of using Eloquent scopes and relationships. Therefore, the final query would look like this (without splitting it into smaller and more readable functions):

```php
$startOfWeek = now()->startOfWeek(Carbon::MONDAY);
$endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

$users = DB::table('users')->select(
    'users.id', 
    'users.name', 
    'users.birthdate', 
    'users.email', 
    'users.email_verified_at', 
    'users.created_at', 
    'users.updated_at', 
    DB::raw('MAX(purchases.purchase_date) as last_purchase_date'),
)
->leftJoin('purchases', 'users.id', '=', 'purchases.user_id')
->groupBy('users.id')
->whereBetween(
    DB::raw('strftime(\'%m-%d\', birthdate)'), 
    [
        $startOfWeek->format('m-d'), 
        $endOfWeek->format('m-d')
    ]
)
->orderByRaw('strftime(\'%m\', birthdate) ASC, strftime(\'%d\', birthdate) ASC')
->paginate($this->perPage);
```

The performance of the proposed approach compared to the original (measured using Laravel Debugbar's *startMeasure()* and *stopMeasure()* methods) does not differ significantly with a data sample of 100,000 users and one million purchases.

### Perform Ordering and Filtering on Laravel Collection Instead of Query

To avoid using date formatting functions in queries, ordering and filtering by formatted user birthdates can be replaced with sorting and filtering on the query result represented by an Eloquent collection. The example below fetches data using a database query and then filters it using the *Collection::filter()* method. Finally, the data is sorted using the *Collection::sortBy()* method, and the result is paginated using a custom *paginate()* method.

```php
public function render()
{
    $users = User::select(
        'users.id', 
        'users.name', 
        'users.birthdate', 
        'users.email', 
        'users.email_verified_at', 
        'users.created_at', 
        'users.updated_at', 
        DB::raw('MAX(purchases.purchase_date) as last_purchase_date')
    )
    ->leftJoin('purchases', 'users.id', '=', 'purchases.user_id')
    ->groupBy('users.id')
    ->get();

    $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
    $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

    $filteredUsers = $users->filter(function($user) use ($startOfWeek, $endOfWeek) {
        $birthdate = Carbon::parse($user->birthdate);
        $birthdateThisYear = $birthdate->copy()->year(now()->year);
        return $birthdateThisYear->between($startOfWeek, $endOfWeek);
    });

    $sortedUsers = $filteredUsers->sortBy(function($user) {
        return Carbon::parse($user->birthdate)->format('m-d');
    });

    $paginatedUsers = $this->paginate($sortedUsers);

    return view('livewire.users-table', [
        'users' => $paginatedUsers,
        'currentPage' => $paginatedUsers->currentPage(),
    ]);
}

protected function paginate($items, $page = null, $options = [])
{
    $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

    $items = $items instanceof Collection ? $items : Collection::make($items);

    $paginatedItems = new LengthAwarePaginator(
        $items->forPage($page, $this->perPage), 
        $items->count(), 
        $this->perPage, 
        $page, 
        $options,
    );
    $paginatedItems->setPath(request()->url());

    return $paginatedItems;
}
```

### Indexes
Depending on the potential amount of data stored in the *users* table and the frequency of *INSERT* and *UPDATE* operations on this table, it may be worth considering the use of database indexes, which could potentially lead to increased query performance.

**purchases.purchase_date**

An aggregation function *MAX()* is executed on the *purchase_date* column to return the date of the user's last purchase. Therefore, using an index on this column is highly recommended.

**users.birthdate**

An index on the *birthdate* column may not contribute to increased performance due to the use of the ^*strftime()* function in the *WHERE* clause condition. Indexes are based on the original value of the column, not on the result of a function executed on those values.

For this purpose, the migration *add_indexes_in_users_table.php* was created:

```php
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('birthdate');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index('purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['birthdate']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['purchase_date']);
        });
    }
};
```

### Adding *birthday* Column

An alternative approach, though requiring a departure from the third normal form of the table, is to add a birthday column, which allows for omitting formatting functions used in the query.

For this purpose, the following migration can be created:

```php
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->date('birthday')->nullable();
            });
    
            DB::statement("UPDATE users SET birthday = strftime('%m-%d', birthdate)");
            
            Schema::table('users', function (Blueprint $table) {
                $table->index('birthday');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['birthday']);
            });
    
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('birthday');
            });
        });
    }
};
```

Next, in the query, the birthday column should be added to the *User::select()* method, the *scopeOrderByBirthday()* method defined in the *User* model should be replaced with a direct call to *orderBy('birthday')* in the query chain, and the body of the *scopeHavingBirthdayThisWeek()* method should be changed to the following:

```php
public function scopeHavingBirthdayThisWeek(Builder $query): Builder
{
    $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
    $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

    return $query->whereBetween(
        'birthday', 
        [
            $startOfWeek->format('m-d'), 
            $endOfWeek->format('m-d')
        ]
    );
}
```

The final query looks as follows:

```php
$users = User::select(
    'users.id', 
    'users.name', 
    'users.birthdate', 
    'users.birthday', 
    'users.email', 
    'users.email_verified_at', 
    'users.created_at', 
    'users.updated_at', 
)
    ->withLastPurchaseDate()
    ->havingBirthdayThisWeek()
    ->orderBy('birthday')
    ->paginate($this->perPage);
```

### simplePaginate() instead of paginate()

Calling the *paginate()* method requires calculating all records that meet the condition in order to determine the total number of available pages. This causes nearly the same query to be executed twice, which lengthens the page load time. An alternative approach is to use the simplePaginate() method, which returns information about the total number of pages.

### Cache

Another possibility is to cache the query results if they do not change frequently and are not dependent on other query parameters.