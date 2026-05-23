<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketTracking;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $superadmin = User::updateOrCreate([
                'username' => 'superadmin',
            ], [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'role' => 'superadmin',
                'password' => Hash::make('123123123'),
            ]);

            $users = $this->seedUsers();
            $this->seedTickets($users, $superadmin);
        });
    }

    /**
     * @return array<int, User>
     */
    private function seedUsers(): array
    {
        $records = [
            ['name' => 'Rai Tilosava', 'username' => 'rai.tilosava', 'email' => 'rai.tilosava@example.com', 'role' => 'user'],
            ['name' => 'Maya Putri', 'username' => 'maya.putri', 'email' => 'maya.putri@example.com', 'role' => 'user'],
            ['name' => 'Bima Prakoso', 'username' => 'bima.prakoso', 'email' => 'bima.prakoso@example.com', 'role' => 'user'],
            ['name' => 'Nadia Salsabila', 'username' => 'nadia.salsabila', 'email' => 'nadia.salsabila@example.com', 'role' => 'user'],
            ['name' => 'Dimas Arya', 'username' => 'dimas.arya', 'email' => 'dimas.arya@example.com', 'role' => 'user'],
            ['name' => 'Sinta Lestari', 'username' => 'sinta.lestari', 'email' => 'sinta.lestari@example.com', 'role' => 'user'],
            ['name' => 'Yoga Saputra', 'username' => 'yoga.saputra', 'email' => 'yoga.saputra@example.com', 'role' => 'user'],
            ['name' => 'IT Staff 1', 'username' => 'it.staff1', 'email' => 'it.staff1@example.com', 'role' => 'staff'],
            ['name' => 'IT Staff 2', 'username' => 'it.staff2', 'email' => 'it.staff2@example.com', 'role' => 'staff'],
            ['name' => 'IT Staff 3', 'username' => 'it.staff3', 'email' => 'it.staff3@example.com', 'role' => 'staff'],
        ];

        return array_map(fn (array $record) => User::updateOrCreate([
            'username' => $record['username'],
        ], [
            'name' => $record['name'],
            'email' => $record['email'],
            'role' => $record['role'],
            'password' => Hash::make('password123'),
        ]), $records);
    }

    /**
     * @param  array<int, User>  $users
     */
    private function seedTickets(array $users, User $superadmin): void
    {
        $requesters = array_values(array_filter($users, fn (User $user) => $user->role === 'user'));
        $staff = array_values(array_filter($users, fn (User $user) => $user->role === 'staff'));
        $cases = $this->ticketCases();

        foreach ($cases as $index => $case) {
            $requester = $requesters[$index % count($requesters)];
            $handler = in_array($case['status'], ['open', 'cancelled'], true)
                ? null
                : $staff[$index % count($staff)];
            $createdAt = Carbon::create(2026, 5, 1, 8, 0)->addHours($index * 5);
            $updatedAt = $case['status'] === 'open' ? null : $createdAt->copy()->addHours(count($this->statusPath($case['status'])));

            $ticket = Ticket::updateOrCreate([
                'ticket_code' => sprintf('TCK-20260501-%04d', $index + 1),
            ], [
                'user_id' => $requester->id,
                'staff_id' => $handler?->id,
                'issues' => $case['issues'],
                'description' => $case['description'],
                'severity_level' => $case['severity_level'],
                'priority_level' => $case['priority_level'],
                'status' => $case['status'],
                'created_by' => $requester->id,
                'updated_by' => $case['status'] === 'open' ? null : ($handler?->id ?? $superadmin->id),
            ]);

            Ticket::withoutTimestamps(fn () => $ticket->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ])->save());

            TicketTracking::where('ticket_id', $ticket->id)->delete();

            foreach ($this->statusPath($case['status']) as $step => $status) {
                $createdBy = $status === 'open' ? $requester->id : ($handler?->id ?? $superadmin->id);
                $tracking = TicketTracking::create([
                    'ticket_id' => $ticket->id,
                    'status' => $status,
                    'note' => $this->trackingNote($status, $case['issues']),
                    'handled_by' => $status === 'open' ? null : $handler?->id,
                    'created_by' => $createdBy,
                ]);

                $tracking->forceFill([
                    'created_at' => $createdAt->copy()->addHours($step),
                ])->save();
            }
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function ticketCases(): array
    {
        $issues = [
            ['Samsung Galaxy Tab A bootloop', 'Device stuck di logo Samsung dan masuk recovery mode setelah update sistem.'],
            ['Printer finance tidak terdeteksi', 'Printer network di ruang finance tidak muncul di daftar device user.'],
            ['Laptop sales lambat saat booting', 'Laptop membutuhkan waktu lebih dari 10 menit untuk masuk desktop.'],
            ['Email user tidak bisa sync', 'Mailbox user gagal sinkron di Outlook dan mobile client.'],
            ['WiFi ruang meeting putus-putus', 'Koneksi WiFi tidak stabil saat digunakan presentasi.'],
            ['Aplikasi kasir gagal login', 'User kasir mendapat pesan credential invalid meskipun password sudah direset.'],
            ['Monitor IT helpdesk flicker', 'Monitor berkedip setiap beberapa menit saat resolusi full HD.'],
            ['Keyboard admin rusak', 'Beberapa tombol keyboard tidak merespons input.'],
            ['VPN remote tidak tersambung', 'User remote mendapat timeout ketika koneksi ke VPN kantor.'],
            ['Scanner gudang tidak membaca barcode', 'Scanner USB menyala tetapi hasil scan tidak masuk ke aplikasi.'],
            ['PC receptionist blue screen', 'PC restart sendiri dan menampilkan blue screen saat membuka browser.'],
            ['Access point lantai 2 offline', 'Access point tidak terlihat di controller dan user kehilangan koneksi.'],
            ['File sharing departemen lambat', 'Akses folder sharing sangat lambat saat membuka file Excel besar.'],
            ['Mouse desain double click', 'Mouse melakukan double click otomatis saat sekali klik.'],
            ['CCTV lobby tidak tampil', 'Kamera lobby tidak muncul di dashboard monitoring.'],
            ['Tablet inventory battery drop', 'Baterai tablet turun dari 80 persen ke 10 persen dalam waktu singkat.'],
            ['Komputer training tidak ada audio', 'Speaker tidak mengeluarkan suara saat video training diputar.'],
            ['POS cabang gagal print struk', 'Struk transaksi tidak keluar dari printer thermal POS.'],
            ['Password domain terkunci', 'Akun domain user locked setelah beberapa percobaan login.'],
            ['Projector meeting blur', 'Tampilan projector tidak fokus dan warna terlihat pudar.'],
            ['Antivirus server memberi alert', 'Antivirus menandai file suspicious di shared folder.'],
            ['Browser HR tidak membuka portal', 'Portal HR hanya loading dan tidak masuk halaman login.'],
            ['Hard disk backup hampir penuh', 'Drive backup tersisa kurang dari 5 persen kapasitas.'],
            ['ERP timeout saat approval', 'Approval purchase request timeout setelah submit.'],
            ['Laptop procurement overheating', 'Fan laptop berbunyi keras dan suhu naik saat membuka spreadsheet.'],
            ['Fingerprint attendance gagal scan', 'Mesin absensi tidak mengenali sidik jari beberapa karyawan.'],
            ['Docking station tidak charge', 'Laptop tidak mengisi daya ketika terhubung ke docking station.'],
            ['Data Excel corrupt', 'File Excel operasional tidak bisa dibuka dan meminta repair.'],
            ['Speaker ruang meeting noise', 'Audio meeting mengeluarkan noise saat mikrofon aktif.'],
            ['User baru butuh akses aplikasi', 'Karyawan baru membutuhkan akses aplikasi internal dan email.'],
        ];
        $statuses = ['open', 'assigned', 'in_progress', 'pending', 'solved', 'done', 'cancelled'];
        $severities = ['low', 'medium', 'high', 'critical'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return array_map(fn (array $issue, int $index) => [
            'issues' => $issue[0],
            'description' => $issue[1],
            'status' => $statuses[$index % count($statuses)],
            'severity_level' => $severities[$index % count($severities)],
            'priority_level' => $priorities[($index + 1) % count($priorities)],
        ], $issues, array_keys($issues));
    }

    /**
     * @return array<int, string>
     */
    private function statusPath(string $status): array
    {
        return match ($status) {
            'assigned' => ['open', 'assigned'],
            'in_progress' => ['open', 'assigned', 'in_progress'],
            'pending' => ['open', 'assigned', 'in_progress', 'pending'],
            'solved' => ['open', 'assigned', 'in_progress', 'solved'],
            'done' => ['open', 'assigned', 'in_progress', 'solved', 'done'],
            'cancelled' => ['open', 'cancelled'],
            default => ['open'],
        };
    }

    private function trackingNote(string $status, string $issue): string
    {
        return match ($status) {
            'open' => "Ticket created for {$issue}.",
            'assigned' => 'Ticket assigned to IT staff for initial review.',
            'in_progress' => 'IT staff started troubleshooting and collecting evidence.',
            'pending' => 'Ticket is pending user confirmation, sparepart, or third-party response.',
            'solved' => 'Root cause has been identified and the issue has been solved.',
            'done' => 'Resolution has been confirmed and ticket is marked as done.',
            'cancelled' => 'Ticket was cancelled because support is no longer required.',
            default => 'Tracking updated.',
        };
    }
}
