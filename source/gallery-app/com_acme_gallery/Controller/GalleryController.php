<?php
namespace App\com_acme_gallery\Controller;

use App\com_acme_gallery\Model\GalleryItemModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\File;
use Pinoox\Portal\View;

class GalleryController extends Controller
{
    private function galleryItems()
    {
        return GalleryItemModel::orderByDesc('id')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'url' => File::url($item->file_id),
                'thumb' => File::thumb($item->file_id) ?: File::url($item->file_id),
            ];
        });
    }

    public function index()
    {
        return View::render('pages/gallery', [
            'title' => 'گالری تصاویر',
            'items' => $this->galleryItems(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'title' => 'required|string|max:200',
            'photo' => 'required|file|image|max:4096',
        ]);

        $result = File::upload('photo')
            ->to('uploads/gallery')
            ->group('gallery')
            ->thumb()
            ->maxSize('4MB')
            ->extensions('jpg,jpeg,png,webp,gif')
            ->save();

        if (!$result->success) {
            return View::render('pages/gallery', [
                'title' => 'گالری تصاویر',
                'items' => $this->galleryItems(),
                'error' => $result->error ?: 'آپلود ناموفق',
            ]);
        }

        GalleryItemModel::create([
            'title' => $data['title'],
            'file_id' => $result->id,
        ]);

        return redirect(url('/'));
    }

    public function destroy(Request $request, int $id)
    {
        $item = GalleryItemModel::find($id);

        if ($item) {
            File::remove($item->file_id);
            $item->delete();
        }

        return redirect(url('/'));
    }
}
