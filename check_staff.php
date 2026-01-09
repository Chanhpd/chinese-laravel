use App\Models\User;

$staff = User::where('email', 'chanh.staff@gmail.com')->first();
if ($staff) {
    echo "=== Staff Account Created Successfully ===\n";
    echo "ID: {$staff->id}\n";
    echo "Name: {$staff->name}\n";
    echo "Email: {$staff->email}\n";
    echo "Role: {$staff->role}\n";
    echo "Status: {$staff->status}\n";
    echo "Created: {$staff->created_at}\n";
}

echo "\n=== All Users ===\n";
User::all()->each(function($user) {
    echo "{$user->id} | {$user->name} | {$user->email} | {$user->role} | {$user->status}\n";
});
