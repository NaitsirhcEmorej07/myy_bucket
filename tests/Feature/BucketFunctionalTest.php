<?php

namespace Tests\Feature;

use App\Models\BucketFile;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BucketFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_dashboard_displays_unlimited_storage_and_empty_state(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Myy Bucket Storage');
        $response->assertSee('Unlimited');
        $response->assertSee('0 B');
        $response->assertSee('of Unlimited Storage');
    }

    public function test_can_create_and_delete_folder(): void
    {
        // 1. Create folder
        $response = $this->actingAs($this->user)->postJson(route('bucket.folder.store'), [
            'name' => 'Project Alpha',
            'color' => 'indigo',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'folder' => [
                'name' => 'Project Alpha',
                'color' => 'indigo',
            ],
        ]);

        $this->assertDatabaseHas('folders', [
            'user_id' => $this->user->id,
            'name' => 'Project Alpha',
        ]);

        $folder = Folder::where('name', 'Project Alpha')->first();

        // 2. Delete folder
        $deleteResponse = $this->actingAs($this->user)->deleteJson(route('bucket.folder.destroy', $folder->id));
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('folders', ['id' => $folder->id]);
    }

    public function test_can_upload_rename_toggle_star_and_delete_file(): void
    {
        Storage::fake('public');

        // Create folder
        $folder = Folder::create([
            'user_id' => $this->user->id,
            'name' => 'Documents',
            'color' => 'blue',
        ]);

        // 1. Upload file
        $file = UploadedFile::fake()->create('sample_doc.pdf', 1024, 'application/pdf');

        $uploadResponse = $this->actingAs($this->user)->post(route('bucket.upload'), [
            'folder_id' => $folder->id,
            'files' => [$file],
        ]);

        $uploadResponse->assertStatus(200);
        $uploadResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('files', [
            'user_id' => $this->user->id,
            'folder_id' => $folder->id,
            'name' => 'sample_doc.pdf',
            'category' => 'document',
        ]);

        $bucketFile = BucketFile::where('name', 'sample_doc.pdf')->first();

        // 2. Toggle star
        $starResponse = $this->actingAs($this->user)->postJson(route('bucket.file.star', $bucketFile->id));
        $starResponse->assertStatus(200);
        $starResponse->assertJson(['starred' => true]);
        $this->assertTrue($bucketFile->fresh()->is_starred);

        // 3. Rename file
        $renameResponse = $this->actingAs($this->user)->patchJson(route('bucket.file.rename', $bucketFile->id), [
            'name' => 'renamed_doc.pdf',
        ]);
        $renameResponse->assertStatus(200);
        $this->assertEquals('renamed_doc.pdf', $bucketFile->fresh()->name);

        // 4. Download file
        $downloadResponse = $this->actingAs($this->user)->get(route('bucket.file.download', $bucketFile->id));
        $downloadResponse->assertStatus(200);

        // 5. Delete file
        $deleteResponse = $this->actingAs($this->user)->deleteJson(route('bucket.file.destroy', $bucketFile->id));
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('files', ['id' => $bucketFile->id]);
    }

    public function test_dashboard_reflects_uploaded_file_size_and_category(): void
    {
        Storage::fake('public');

        // Create file in database
        BucketFile::create([
            'user_id' => $this->user->id,
            'folder_id' => null,
            'name' => 'vacation.jpg',
            'path' => 'uploads/test/vacation.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 2097152, // 2 MB
            'category' => 'image',
            'is_starred' => false,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('2.0 MB');
        $response->assertSee('vacation.jpg');
    }

    public function test_can_upload_and_stream_video_file(): void
    {
        Storage::fake('public');

        $folder = Folder::create([
            'user_id' => $this->user->id,
            'name' => 'Videos',
            'color' => 'purple',
        ]);

        $video = UploadedFile::fake()->create('drone_shot.mp4', 15360, 'video/mp4'); // 15MB video

        $response = $this->actingAs($this->user)->post(route('bucket.upload'), [
            'folder_id' => $folder->id,
            'files' => [$video],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('files', [
            'user_id' => $this->user->id,
            'name' => 'drone_shot.mp4',
            'category' => 'video',
            'extension' => 'MP4',
        ]);

        $bucketFile = BucketFile::where('name', 'drone_shot.mp4')->first();

        // Test video preview stream response
        $previewResponse = $this->actingAs($this->user)->get(route('bucket.file.preview', $bucketFile->id));
        $previewResponse->assertStatus(200);
        $previewResponse->assertHeader('Content-Type', 'video/mp4');
    }

    public function test_cannot_upload_file_without_folder(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('orphan.txt', 10, 'text/plain');

        $response = $this->actingAs($this->user)->postJson(route('bucket.upload'), [
            'files' => [$file],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['folder_id']);
    }

    public function test_bucket_view_renders_create_folder_card_and_excludes_storage_card(): void
    {
        $response = $this->actingAs($this->user)->get(route('bucket'));

        $response->assertStatus(200);
        $response->assertSee('Create folder');
        $response->assertSee('New directory');
        // Storage card breakdown was removed from bucket page
        $response->assertDontSee('6 MB of Unlimited Storage');
        $response->assertDontSee('Unlimited Plan');
    }

    public function test_image_upload_is_automatically_optimized(): void
    {
        Storage::fake('public');

        $folder = Folder::create([
            'user_id' => $this->user->id,
            'name' => 'Photos',
            'color' => 'sky',
        ]);

        // Create an actual image file with uncompressed gradient
        $width = 400;
        $height = 300;
        $im = imagecreatetruecolor($width, $height);
        for ($y = 0; $y < $height; $y += 5) {
            $col = imagecolorallocate($im, ($y * 2) % 255, ($y * 5) % 255, ($y * 7) % 255);
            imageline($im, 0, $y, $width, $y, $col);
        }
        $tempImg = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        imagejpeg($im, $tempImg, 100);
        imagedestroy($im);

        $uploadedFile = new UploadedFile($tempImg, 'camera_photo.jpg', 'image/jpeg', null, true);

        $response = $this->actingAs($this->user)->post(route('bucket.upload'), [
            'folder_id' => $folder->id,
            'files' => [$uploadedFile],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $bucketFile = BucketFile::where('name', 'camera_photo.jpg')->first();
        $this->assertNotNull($bucketFile);
        $this->assertTrue($bucketFile->is_optimized);
        $this->assertGreaterThan($bucketFile->size, $bucketFile->original_size);
        $this->assertGreaterThan(0, $bucketFile->savings_percent);

        // Download and verify it's a valid readable image
        $downloadResponse = $this->actingAs($this->user)->get(route('bucket.file.download', $bucketFile->id));
        $downloadResponse->assertStatus(200);

        @unlink($tempImg);
    }

    public function test_document_is_transparently_compressed_and_decompressed_identically_on_download(): void
    {
        Storage::fake('public');

        $folder = Folder::create([
            'user_id' => $this->user->id,
            'name' => 'Work',
            'color' => 'blue',
        ]);

        // Create a compressible text document
        $originalText = str_repeat("Myy Bucket Cloud Storage is an alternative to iCloud with fast performance and high efficiency. ", 100);
        $tempDoc = tempnam(sys_get_temp_dir(), 'test_doc_') . '.txt';
        file_put_contents($tempDoc, $originalText);

        $uploadedFile = new UploadedFile($tempDoc, 'project_report.txt', 'text/plain', null, true);

        $response = $this->actingAs($this->user)->post(route('bucket.upload'), [
            'folder_id' => $folder->id,
            'files' => [$uploadedFile],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $bucketFile = BucketFile::where('name', 'project_report.txt')->first();
        $this->assertNotNull($bucketFile);
        $this->assertTrue($bucketFile->is_compressed);
        $this->assertLessThan($bucketFile->original_size, $bucketFile->size);
        $this->assertGreaterThan(50, $bucketFile->savings_percent); // Expect >50% space savings on text

        // Download and verify the contents are 100% byte-for-byte identical to original
        $downloadResponse = $this->actingAs($this->user)->get(route('bucket.file.download', $bucketFile->id));
        $downloadResponse->assertStatus(200);

        // StreamDownload returns content via output buffering
        ob_start();
        $downloadResponse->sendContent();
        $downloadedContent = ob_get_clean();

        $this->assertEquals($originalText, $downloadedContent);

        @unlink($tempDoc);
    }

    public function test_user_isolation_cannot_see_or_access_another_users_files_and_folders(): void
    {
        Storage::fake('public');

        // User A (this->user) creates folder and file
        $folderA = Folder::create([
            'user_id' => $this->user->id,
            'name' => 'User A Private Folder',
            'color' => 'blue',
        ]);

        $fileA = BucketFile::create([
            'user_id' => $this->user->id,
            'folder_id' => $folderA->id,
            'name' => 'private_document.pdf',
            'disk' => 'public',
            'path' => 'buckets/' . $this->user->id . '/private_document.pdf',
            'size' => 1024,
            'mime_type' => 'application/pdf',
            'extension' => 'PDF',
            'category' => 'document',
            'is_starred' => false,
        ]);
        Storage::disk('public')->put($fileA->path, 'secret user A content');

        // Create User B
        $userB = User::factory()->create();

        // 1. User B cannot see User A's data in the bucket API
        $dataResponse = $this->actingAs($userB)->getJson(route('bucket.data'));
        $dataResponse->assertStatus(200);
        $dataResponse->assertJsonMissing(['name' => 'User A Private Folder']);
        $dataResponse->assertJsonMissing(['name' => 'private_document.pdf']);

        // 2. User B cannot download User A's file (403 Forbidden)
        $downloadResponse = $this->actingAs($userB)->get(route('bucket.file.download', $fileA->id));
        $downloadResponse->assertStatus(403);

        // 3. User B cannot preview User A's file (403 Forbidden)
        $previewResponse = $this->actingAs($userB)->get(route('bucket.file.preview', $fileA->id));
        $previewResponse->assertStatus(403);

        // 4. User B cannot rename User A's file (403 Forbidden)
        $renameResponse = $this->actingAs($userB)->patchJson(route('bucket.file.rename', $fileA->id), ['name' => 'hacked.pdf']);
        $renameResponse->assertStatus(403);

        // 5. User B cannot delete User A's file (403 Forbidden)
        $deleteResponse = $this->actingAs($userB)->deleteJson(route('bucket.file.destroy', $fileA->id));
        $deleteResponse->assertStatus(403);

        // 6. User B cannot delete User A's folder (403 Forbidden)
        $deleteFolderResponse = $this->actingAs($userB)->deleteJson(route('bucket.folder.destroy', $folderA->id));
        $deleteFolderResponse->assertStatus(403);

        // 7. User B cannot upload into User A's folder (422 validation error)
        $dummyUpload = UploadedFile::fake()->create('malicious.txt', 10, 'text/plain');
        $uploadIntoOtherResponse = $this->actingAs($userB)->postJson(route('bucket.upload'), [
            'folder_id' => $folderA->id,
            'files' => [$dummyUpload],
        ]);
        $uploadIntoOtherResponse->assertStatus(422);
        $uploadIntoOtherResponse->assertJsonValidationErrors(['folder_id']);
    }
}

