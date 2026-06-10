<?php
namespace App\com_acme_gallery\Model;

use Pinoox\Component\Database\Model;

class GalleryItemModel extends Model
{
    protected $table = 'gallery_items';

    protected $fillable = ['title', 'file_id'];
}
