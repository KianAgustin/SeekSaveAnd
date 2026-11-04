<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo people. They have no password: real users sign in with Microsoft.
        // (Locally you can use the "sign in as" buttons — see SEEKSAVE_DEV_LOGIN.)
        $admin = $this->user('Campus Admin', 'admin@mcc.edu.ph', null, null, null, null, null, '0900 000 0000', true);
        $jordan = $this->user('Jordan Lee', 'jordan@campus.edu', 'MCC Main', 'BSIT', '3rd Year', 'A', '2023-00111', '0967 111 1111');
        $john = $this->user('John Doe', 'john@campus.edu', 'MCC Main', 'BSIT', '2nd Year', 'B', '2023-00222', '0967 222 2222');
        $seddy = $this->user('Seddy', 'seddy@mcc.edu.ph', 'MCC Main', 'BSIT', '3rd Year', 'A', '24-29-6767', '0967 676 6767');
        $alex = $this->user('Alex M.', 'alex@mcc.edu.ph', 'MCC Main', 'BSCS', '2nd Year', 'B', '22-10-4412', '0918 222 3344');
        $sam = $this->user('Sam Rivera', 'sam@mcc.edu.ph', 'MCC Dapdap', 'BSBA', '4th Year', 'A', '21-04-8891', '0908 555 1212');
        $priya = $this->user('Priya S.', 'priya@mcc.edu.ph', 'MCC Main', 'BSIS', '1st Year', 'C', '23-08-2201', '0917 333 7788');
        $mark = $this->user('Mark D.', 'mark@mcc.edu.ph', 'MCC Main', 'BSED', '3rd Year', 'B', '20-11-0098', '0922 444 5566');

        // 14 sample posts (7 found, 7 lost) with photos from public/images/samples
        $phone = $this->item($seddy, 'found', 'Phone', 'Black smartphone with a cracked screen protector. Left behind on a lab desk.', 'Electronics', 'Cisco Laboratory', 'samples/phone.svg', now()->subHours(3));
        $this->item($alex, 'lost', 'Wrist Watch', 'Purple sport band, digital face.', 'Personal Item', 'Cisco Laboratory', 'samples/watch.svg', now()->subHours(6));
        $wallet = $this->item($jordan, 'found', 'Wallet', 'Black leather wallet with a student ID inside.', 'Documents', 'Gymnasium', 'samples/wallet.svg', now()->subHours(2));
        $this->item($sam, 'found', 'Fountain Pen', 'Brown and gold fountain pen.', 'Other', 'Library', 'samples/pen.svg', now()->subHours(8));
        $this->item($priya, 'lost', 'Laptop', 'Silver laptop with a sticker on the lid. Last seen in the computer lab.', 'Electronics', 'Computer Lab', 'samples/laptop.svg', now()->subHours(12));
        $this->item($mark, 'found', 'Silver Keys', '3 keys plus a small USB drive on a blue keychain.', 'Keys', 'Campus Canteen', 'samples/keys.svg', now()->subDay());
        $this->item($john, 'found', 'Blue Umbrella', 'Folding umbrella with a wooden handle.', 'Personal Item', 'Room 105', 'samples/umbrella.svg', now()->subHours(20));
        $this->item($alex, 'lost', 'Student ID Lace', 'Green MCC lace with ID attached, name starts with "A".', 'Documents', 'Student Center', 'samples/student-id.svg', now()->subHours(26));
        $this->item($sam, 'found', 'Gray Hoodie', 'Size L gray hoodie, left on a chair.', 'Clothing', 'Auditorium', 'samples/hoodie.svg', now()->subHours(30));
        $this->item($priya, 'lost', 'Scientific Calculator', 'Pink Casio calculator with name written inside the cover.', 'Electronics', 'Room 201', 'samples/calculator.svg', now()->subHours(34));
        $this->item($mark, 'found', 'Teal Tumbler', 'Teal insulated tumbler with a straw.', 'Personal Item', 'Parking Lot B', 'samples/tumbler.svg', now()->subHours(40));
        $this->item($jordan, 'lost', 'Wireless Earbuds', 'Purple earbuds in a small charging case.', 'Electronics', 'Boys CR', 'samples/earbuds.svg', now()->subHours(44));
        $this->item($john, 'lost', 'Eyeglasses', 'Black-framed eyeglasses in a blue case.', 'Personal Item', 'Girls CR', 'samples/eyeglasses.svg', now()->subHours(50));
        $backpack = $this->item($seddy, 'found', 'Orange Backpack', 'Orange backpack with notebooks inside.', 'Other', 'Admin Building', 'samples/backpack.svg', now()->subDays(3));

        // One resolved example so the "Resolved" state is visible
        $backpack->update(['resolved' => true, 'resolved_at' => now()->subDay()]);

        // A pending claim and a chat so those screens have data
        Claim::firstOrCreate(
            ['item_id' => $wallet->id, 'user_id' => $john->id],
            ['proof' => 'It has my student ID 2023-00222 and a photo of my dog in the front slot.', 'status' => 'pending']
        );

        $conversation = Conversation::firstOrCreate(['item_id' => $phone->id, 'user_id' => $alex->id]);
        if ($conversation->messages()->count() === 0) {
            $conversation->messages()->create(['sender_id' => $alex->id, 'body' => 'Hi! Is the phone still with you? I think it might be my brother\'s.']);
            $conversation->messages()->create(['sender_id' => $seddy->id, 'body' => 'Yes, I still have it. What color is the case?']);
        }
    }

    private function user(string $name, string $email, ?string $campus, ?string $program, ?string $year, ?string $section, ?string $studentNo, string $contact, bool $admin = false): User
    {
        $user = User::firstOrCreate(['email' => $email], [
            'name' => $name,
            'campus' => $campus,
            'program' => $program,
            'year' => $year,
            'section' => $section,
            'student_number' => $studentNo,
            'contact' => $contact,
        ]);

        if ($admin && ! $user->isAdmin()) {
            $user->forceFill(['role' => 'admin'])->save();
        }

        return $user;
    }

    private function item(User $owner, string $type, string $title, string $desc, string $category, string $location, string $photo, $when): Item
    {
        $existing = Item::where('user_id', $owner->id)->where('title', $title)->first();
        if ($existing) {
            return $existing;
        }

        $item = new Item([
            'user_id' => $owner->id,
            'type' => $type,
            'title' => $title,
            'description' => $desc,
            'category' => $category,
            'location' => $location,
            'photo_path' => $photo,
        ]);
        $item->created_at = $when;
        $item->updated_at = $when;
        $item->save();

        return $item;
    }
}
