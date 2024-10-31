<?php //c39b01f2e664f7912c655dc633b8bd25
/** @noinspection all */

namespace App\Jobs {

    use Illuminate\Foundation\Bus\PendingDispatch;

    /**
     * @method static PendingDispatch dispatch($post_id, $published_at)
     * @method static void dispatchSync($post_id, $published_at)
     */
    class PostPublishingJob {}
}

namespace Botble\Base\Events {

    use Botble\Base\Contracts\BaseModel;
    use Botble\Base\Contracts\PanelSections\Manager;
    use Botble\Base\Contracts\PanelSections\PanelSection;
    use Botble\Base\Contracts\PanelSections\PanelSectionItem;
    use Botble\Base\Forms\FormAbstract;
    use Botble\Base\Supports\DashboardMenu;
    use Botble\Base\Supports\ValueObjects\CoreProduct;
    use Botble\Base\Widgets\Contracts\AdminWidget;
    use Illuminate\Broadcasting\PendingBroadcast;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\Request;
    use Illuminate\Support\Collection;

    /**
     * @method static void dispatch(Request $request, bool|BaseModel|null $data)
     * @method static PendingBroadcast broadcast(Request $request, bool|BaseModel|null $data)
     */
    class BeforeCreateContentEvent {}

    /**
     * @method static void dispatch(Request $request, bool|BaseModel|null $data)
     * @method static PendingBroadcast broadcast(Request $request, bool|BaseModel|null $data)
     */
    class BeforeUpdateContentEvent {}

    /**
     * @method static void dispatch(string $screen, Request $request, bool|Model|null $data)
     * @method static PendingBroadcast broadcast(string $screen, Request $request, bool|Model|null $data)
     */
    class CreatedContentEvent {}

    /**
     * @method static void dispatch(DashboardMenu $dashboardMenu, Collection $menuItems)
     * @method static PendingBroadcast broadcast(DashboardMenu $dashboardMenu, Collection $menuItems)
     */
    class DashboardMenuRetrieved {}

    /**
     * @method static void dispatch(DashboardMenu $dashboardMenu)
     * @method static PendingBroadcast broadcast(DashboardMenu $dashboardMenu)
     */
    class DashboardMenuRetrieving {}

    /**
     * @method static void dispatch(string $screen, Request $request, bool|Model|null $data)
     * @method static PendingBroadcast broadcast(string $screen, Request $request, bool|Model|null $data)
     */
    class DeletedContentEvent {}

    /**
     * @method static void dispatch(string $rendered, string $name, null|string $value = null, bool $withShortcode = false, array $attributes = [])
     * @method static PendingBroadcast broadcast(string $rendered, string $name, null|string $value = null, bool $withShortcode = false, array $attributes = [])
     */
    class EditorRendered {}

    /**
     * @method static void dispatch(string $name, null|string $value = null, bool $withShortcode = false, array $attributes = [])
     * @method static PendingBroadcast broadcast(string $name, null|string $value = null, bool $withShortcode = false, array $attributes = [])
     */
    class EditorRendering {}

    /**
     * @method static void dispatch(FormAbstract $form)
     * @method static PendingBroadcast broadcast(FormAbstract $form)
     */
    class FormRendering {}

    /**
     * @method static void dispatch(string $licenseKey, string $licenseName)
     * @method static PendingBroadcast broadcast(string $licenseKey, string $licenseName)
     */
    class LicenseActivated {}

    /**
     * @method static void dispatch(string $licenseKey, string $licenseName)
     * @method static PendingBroadcast broadcast(string $licenseKey, string $licenseName)
     */
    class LicenseActivating {}

    /**
     * @method static void dispatch(string $licenseKey, string $licenseName)
     * @method static PendingBroadcast broadcast(string $licenseKey, string $licenseName)
     */
    class LicenseInvalid {}

    /**
     * @method static void dispatch(string $licenseKey, string $licenseName)
     * @method static PendingBroadcast broadcast(string $licenseKey, string $licenseName)
     */
    class LicenseRevoked {}

    /**
     * @method static void dispatch(string $licenseKey, string $licenseName)
     * @method static PendingBroadcast broadcast(string $licenseKey, string $licenseName)
     */
    class LicenseRevoking {}

    /**
     * @method static void dispatch(PanelSectionItem $item, string $content)
     * @method static PendingBroadcast broadcast(PanelSectionItem $item, string $content)
     */
    class PanelSectionItemRendered {}

    /**
     * @method static void dispatch(PanelSectionItem $item)
     * @method static PendingBroadcast broadcast(PanelSectionItem $item)
     */
    class PanelSectionItemRendering {}

    /**
     * @method static void dispatch(PanelSection $section, array $items, string $content)
     * @method static PendingBroadcast broadcast(PanelSection $section, array $items, string $content)
     */
    class PanelSectionItemsRendered {}

    /**
     * @method static void dispatch(PanelSection $section, array $items)
     * @method static PendingBroadcast broadcast(PanelSection $section, array $items)
     */
    class PanelSectionItemsRendering {}

    /**
     * @method static void dispatch(PanelSection $section, string $content)
     * @method static PendingBroadcast broadcast(PanelSection $section, string $content)
     */
    class PanelSectionRendered {}

    /**
     * @method static void dispatch(PanelSection $section)
     * @method static PendingBroadcast broadcast(PanelSection $section)
     */
    class PanelSectionRendering {}

    /**
     * @method static void dispatch(Manager $panelSectionManager)
     * @method static PendingBroadcast broadcast(Manager $panelSectionManager)
     */
    class PanelSectionsRendered {}

    /**
     * @method static void dispatch(Manager $panelSectionManager)
     * @method static PendingBroadcast broadcast(Manager $panelSectionManager)
     */
    class PanelSectionsRendering {}

    /**
     * @method static void dispatch(AdminWidget $widget)
     * @method static PendingBroadcast broadcast(AdminWidget $widget)
     */
    class RenderingAdminWidgetEvent {}

    /**
     * @method static void dispatch(CoreProduct $coreProduct)
     * @method static PendingBroadcast broadcast(CoreProduct $coreProduct)
     */
    class SystemUpdateAvailable {}

    /**
     * @method static void dispatch(string $filePath)
     * @method static PendingBroadcast broadcast(string $filePath)
     */
    class SystemUpdateDownloaded {}

    /**
     * @method static void dispatch(Model|string $screen, Request $request, bool|Model|null $data)
     * @method static PendingBroadcast broadcast(Model|string $screen, Request $request, bool|Model|null $data)
     */
    class UpdatedContentEvent {}
}

namespace Botble\Captcha\Events {

    use Illuminate\Broadcasting\PendingBroadcast;

    /**
     * @method static void dispatch(string $rendered = '')
     * @method static PendingBroadcast broadcast(string $rendered = '')
     */
    class CaptchaRendered {}

    /**
     * @method static void dispatch(array $attributes = [], array $options = [], string $head = '', string $footer = '')
     * @method static PendingBroadcast broadcast(array $attributes = [], array $options = [], string $head = '', string $footer = '')
     */
    class CaptchaRendering {}
}

namespace Botble\Media\Events {

    use Botble\Media\Models\MediaFile;
    use Botble\Media\Models\MediaFolder;
    use Illuminate\Broadcasting\PendingBroadcast;

    /**
     * @method static void dispatch(MediaFile $file)
     * @method static PendingBroadcast broadcast(MediaFile $file)
     */
    class MediaFileRenamed {}

    /**
     * @method static void dispatch(MediaFile $file, string $newName, bool $renameOnDisk)
     * @method static PendingBroadcast broadcast(MediaFile $file, string $newName, bool $renameOnDisk)
     */
    class MediaFileRenaming {}

    /**
     * @method static void dispatch(MediaFile $file)
     * @method static PendingBroadcast broadcast(MediaFile $file)
     */
    class MediaFileUploaded {}

    /**
     * @method static void dispatch(MediaFolder $folder)
     * @method static PendingBroadcast broadcast(MediaFolder $folder)
     */
    class MediaFolderRenamed {}

    /**
     * @method static void dispatch(MediaFolder $file, string $newName, bool $renameOnDisk)
     * @method static PendingBroadcast broadcast(MediaFolder $file, string $newName, bool $renameOnDisk)
     */
    class MediaFolderRenaming {}
}

namespace Botble\Slug\Events {

    use Botble\Slug\Models\Slug;
    use Illuminate\Broadcasting\PendingBroadcast;
    use Illuminate\Database\Eloquent\Model;

    /**
     * @method static void dispatch(bool|Model|null $data, Slug $slug)
     * @method static PendingBroadcast broadcast(bool|Model|null $data, Slug $slug)
     */
    class UpdatedSlugEvent {}
}

namespace Illuminate\Bus {

    use Illuminate\Foundation\Bus\PendingDispatch;

    /**
     * @method static PendingDispatch dispatch(PendingBatch $batch)
     * @method static void dispatchSync(PendingBatch $batch)
     */
    class ChainedBatch {}
}

namespace Illuminate\Foundation\Console {

    use Illuminate\Foundation\Bus\PendingDispatch;

    /**
     * @method static PendingDispatch dispatch(array $data)
     * @method static void dispatchSync(array $data)
     */
    class QueuedCommand {}
}

namespace Illuminate\Foundation\Events {

    use Illuminate\Broadcasting\PendingBroadcast;

    /**
     * @method static void dispatch(array $stubs)
     * @method static PendingBroadcast broadcast(array $stubs)
     */
    class PublishingStubs {}
}

namespace Illuminate\Queue {

    use Illuminate\Foundation\Bus\PendingDispatch;
    use Laravel\SerializableClosure\SerializableClosure;

    /**
     * @method static PendingDispatch dispatch(SerializableClosure $closure)
     * @method static void dispatchSync(SerializableClosure $closure)
     */
    class CallQueuedClosure {}
}

namespace Kris\LaravelFormBuilder\Events {

    use Illuminate\Broadcasting\PendingBroadcast;

    /**
     * @method static void dispatch(object $form)
     * @method static PendingBroadcast broadcast(object $form)
     */
    class FormComponentRegistered {}

    /**
     * @method static void dispatch(object $form)
     * @method static PendingBroadcast broadcast(object $form)
     */
    class FormComponentRegistering {}
}

namespace Maatwebsite\Excel\Jobs {

    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\PendingDispatch;
    use Maatwebsite\Excel\Concerns\FromQuery;
    use Maatwebsite\Excel\Concerns\FromView;
    use Maatwebsite\Excel\Files\TemporaryFile;

    /**
     * @method static PendingDispatch dispatch(object $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, array $data)
     * @method static void dispatchSync(object $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, array $data)
     */
    class AppendDataToSheet {}

    /**
     * @method static PendingDispatch dispatch(FromQuery $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, int $page, int $perPage)
     * @method static void dispatchSync(FromQuery $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, int $page, int $perPage)
     */
    class AppendPaginatedToSheet {}

    /**
     * @method static PendingDispatch dispatch(FromQuery $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, int $page, int $chunkSize)
     * @method static void dispatchSync(FromQuery $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, int $page, int $chunkSize)
     */
    class AppendQueryToSheet {}

    /**
     * @method static PendingDispatch dispatch(FromView $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex)
     * @method static void dispatchSync(FromView $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex)
     */
    class AppendViewToSheet {}

    /**
     * @method static PendingDispatch dispatch(object $export, TemporaryFile $temporaryFile, string $writerType)
     * @method static void dispatchSync(object $export, TemporaryFile $temporaryFile, string $writerType)
     */
    class QueueExport {}

    /**
     * @method static PendingDispatch dispatch(ShouldQueue $import = null)
     * @method static void dispatchSync(ShouldQueue $import = null)
     */
    class QueueImport {}
}
