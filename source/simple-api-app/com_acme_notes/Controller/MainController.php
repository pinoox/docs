<?php
namespace App\com_acme_notes\Controller;

use App\com_acme_notes\Model\NoteModel;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class MainController extends Controller
{
    public function browse()
    {
        $notes = NoteModel::orderByDesc('id')->get();

        return View::render('pages/browse.twig', [
            'title' => 'یادداشت‌ها',
            'notes' => $notes,
        ]);
    }
}
