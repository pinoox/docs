<?php
namespace App\com_acme_gallery\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('gallery_items', 'com_acme_gallery'), function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->unsignedBigInteger('file_id');
            $table->timestamps();

            $table->index('file_id');
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('gallery_items', 'com_acme_gallery'));
    }
};
