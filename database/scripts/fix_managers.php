<?php
use App\Models\Branch;
use App\Models\User;
use App\Models\Incident;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

$branches = Branch::with('company')->get();
$count = 0;

foreach ($branches as $branch) {
    // Fetch or create a user specifically for this branch
    $user = User::where('branch_id', $branch->id)->where('rol', 'user')->first();

    if (!$user) {
        $cleanBranchName = Str::slug($branch->nombre);
        $cleanCompanyName = Str::slug($branch->company->nombre);
        
        $email = "{$cleanBranchName}@{$cleanCompanyName}.com";
        // Ensure email uniqueness
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = "{$cleanBranchName}{$counter}@{$cleanCompanyName}.com";
            $counter++;
        }

        $user = User::create([
            'name' => 'Gerente - ' . $branch->nombre,
            'email' => $email,
            'password' => Hash::make('password123'),
            'company_id' => $branch->company_id,
            'branch_id' => $branch->id,
            'rol' => 'user',
        ]);
        echo "Creado: {$user->name} ({$email})\n";
    }

    // Reassign all incidents for this branch to this specific user
    $updated = Incident::where('branch_id', $branch->id)->update(['notifier_id' => $user->id]);
    if ($updated > 0) {
        echo " -> Reasignadas {$updated} incidencias a {$user->name}\n";
        $count += $updated;
    }
}

echo "\nProceso finalizado. Total de incidencias reasignadas: {$count}\n";
